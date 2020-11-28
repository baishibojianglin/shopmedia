<?php

namespace app\api\controller;

use app\common\lib\Aes;
use app\common\lib\exception\ApiException;
use app\common\lib\IAuth;
use think\Controller;
use think\Db;

/**
 * 第三方登录
 * Class ThirdLogin
 * @package app\api\controller
 */
class ThirdLogin extends Controller
{
    //第三方登录
    public function thirdlogin(){
        //授权获取信息
        $data = $this->getOpenid();

        // 判断用户是否存在
        $userOauth = Db::name('user_oauth')->where(['oauth' => $data['oauth'], 'openid' => $data['openid']])->find();
        $oauthInfo = (new Aes())->encrypt(json_encode($data)); // AES加密三方授权用户信息
        if (!empty($userOauth) && $userOauth['user_id']) {
            $user = Db::name('user')->find($userOauth['user_id']);
            if (empty($user) || !$user['phone'] || $user['phone'] == null) {
                return show(config('code.error'), '请输入手机号码', ['oauth_info' => $oauthInfo], 401);
            }
        } else {
            return show(config('code.error'), '请输入手机号码', ['oauth_info' => $oauthInfo], 401);
        }

        return show(config('code.success'), 'OK', $this->createUser($data));
    }

    /**
     * 绑定手机号
     */
    public function bindPhone()
    {
        // 判断为POST请求
        if (!request()->isPost()) {
            return show(config('code.error'), '请求不合法', '', 400);
        }

        // 传入的参数
        $param = input('param.');
        $param['phone'] = trim($param['phone']);
        $oauth_info = json_decode((new Aes())->decrypt($param['oauth_info']), true); // AES解密三方授权用户信息
        $oauth = $oauth_info['oauth'];
        $openid = $oauth_info['openid'];

        // 判断传入的参数是否存在及合法性
        // 验证手机号码
        if (empty($param['phone'])) {
            return show(config('code.error'), '手机号码不能为空', '', 401);
        }

        // 验证手机短信验证码
        if (empty($param['verify_code'])) {
            return show(config('code.error'), '短信验证码不能为空', '', 401);
        } else {
            // TODO：客户端需对短信验证码AES加密，服务端对短信验证码AES解密
            //$param['code'] = $aesObj->decrypt($param['code']);

            // 判断短信验证码是否合法
            $verifyCode = $param['return_code']; // TODO：获取 调用阿里云短信服务接口时 生成的session值
            if (empty($verifyCode) || $verifyCode != trim($param['verify_code'])) {
                return show(config('code.error'), '短信验证码错误', '', 401);
            }
        }

        // 判断用户是否存在
        $userOauth = Db::name('user_oauth')->where(['oauth' => $oauth, 'openid' => $openid])->find();
        if (!empty($userOauth) && $userOauth['user_id']) {
            $user = Db::name('user')->find($userOauth['user_id']);
            if (empty($user) || !$user['phone'] || $user['phone'] == null) {
                return show(config('code.error'), '请输入手机号码', ['oauth_info' => $oauthInfo], 401);
            }
        } else {
            return show(config('code.error'), '请输入手机号码', ['oauth_info' => $oauthInfo], 401);
        }

        // 入库操作
        /* 手动控制事务 s */
        // 启动事务
        Db::startTrans();
        try {
            // 判断该手机号用户是否存在，不存在则创建
            $user = Db::name('user')->where(['phone' => $param['phone']])->find();
            if (empty($user)) {
                // 新增原始用户
                $res[0] = $userId = Db::name('user')->strict(false)->insertGetId($data); // 新增数据并返回主键值
            }

            // 判断三方授权用户信息是否存在，不存在则创建
            $userOauth = Db::name('user_oauth')->where(['oauth' => $oauth, 'openid' => $openid])->find();

            // 新增原始用户
            $res[0] = $userId = Db::name('user')->strict(false)->insertGetId($data); // 新增数据并返回主键值

            // 任意一个表写入失败都会抛出异常，TODO：是否可以不做该判断
            if (in_array(0, $res)) {
                return show(config('code.error'), '新增失败', '', 403);
            }

            // 返回token给客户端
            $result = [
                'token' => (new Aes())->encrypt($token . '&' . $userId) // AES加密（自定义拼接字符串）
            ];
            // 提交事务
            Db::commit();
            return show(config('code.success'), 'OK', $result, 201);
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return show(config('code.error'), '注册失败，请重试' . $e->getMessage(), '', 500);
            //throw new ApiException($e->getMessage(), 500, config('code.error'));
        }
        /* 手动控制事务 e */
    }

    /**
     * 创建微信用户
     * @param $userInfo
     * @param $postObj
     */
    public function createUser($userInfo)
    {
        // 查询用户是否存在
        $openid = $userInfo['openid'];
        $oauth = $userInfo['oauth'];

        $user = Db::name('user')->where(['oauth' => $oauth, 'openid' => $openid])->find();
        $userOauth = Db::name('user_oauth')->where(['oauth' => $oauth, 'openid' => $openid])->find();

        //开启事务
        Db::startTrans();
        try {
            $res = [];
            $token = IAuth::setAppLoginToken($openid);

            // 用户不存在，则创建用户
            if (empty($user)) {

                $data = [
                    'user_name' => $userInfo['nickname'],
                    'role_ids' => 7,
                    'openid' => $openid,
                    'oauth' => $oauth,
                    'token' => $token,
                    'token_time' => strtotime('+' . config('app.login_time_out')), // token失效时间
                    'avatar' => $userInfo['head_pic'],
                    'gender' => $userInfo['sex'],
                    'status' => 1,
                    'create_time' =>time(),
                    'create_ip' =>request()->ip(),
                    'login_time' => time(), // 登录时间
                    'login_ip' => request()->ip() // 登录IP
                ];

                $userid = Db::name('user')->insertGetId($data);     //新增用户
                $res['1'] = $userid;

                //新增advertiser
                if($userid > 0){

                    $advdata = [
                        'user_id' => $userid,
                        'salesman_id' => 0,
                        'role_id' => 7,
                        'status' => 1,
                        'create_time' => time()
                    ];

                    $advid = Db::name('user_advertiser')->insertGetId($advdata);
                    $res['2'] = $advid;
                }

            }
            else{
                $userid = $user['user_id'];
                //用户存在，修改登录信息
                $data = [
                    'token' => $token, // token
                    'token_time' => strtotime('+' . config('app.login_time_out')), // token失效时间
                    'login_time' => time(), // 登录时间
                    'login_ip' => request()->ip() // 登录IP
                ];

                $loginres = Db::name('user')->where('user_id',$userid)->update($data);
                $res['3'] = $loginres === false ? 0 : true;
            }

            //判断user_oauth
            if(empty($userOauth)){

                $oauthdata = [
                    'user_id' => $userid,
                    'oauth' => $oauth,
                    'openid' => $openid,
                    'verified' => 1,
                    'create_time' =>time(),
                    'create_ip' =>request()->ip(),
                    'login_time' => time(), // 登录时间
                    'login_ip' => request()->ip(), // 登录IP
                    'device_id' => 0 // 广告屏设备id
                ];

                $oauthid = Db::name('user_oauth')->insertGetId($oauthdata);     //新增user_oauth
                $res['4'] = $oauthid;

            }
            else{
                //oauth存在，修改登录信息
                $data = [
                    'user_id' => $userid,
                    'login_time' => time(), // 登录时间
                    'login_ip' => request()->ip() // 登录IP
                ];

                $oauthres = Db::name('user_oauth')->where('oauth_id', $userOauth['oauth_id'])->update($data);
                $res['5'] = $oauthres === false ? 0 : true;
            }

            // 任意一个表写入失败都会抛出异常，TODO：是否可以不做该判断
            if (in_array(0, $res)) {
                return '用户登录失败';
            }

            // 提交事务
            Db::commit();

            $user1 = Db::name('user')->where(['oauth' => $oauth, 'openid' => $openid])->find();

            // 返回token给客户端
            $result = [
                'token' => (new Aes())->encrypt($token . '&' . $user1['user_id']), // AES加密（自定义拼接字符串）
                'user_id' => $user1['user_id'],
                'user_name' => $user1['user_name'],
                'phone' => !empty($user1['phone']) ? $user1['phone'] : ''
            ];
            return $result;

        }catch (\Exception $e){
            // 回滚事务
            Db::rollback();
            return $e->getMessage();
        }
    }

    //微信授权获取openid以及微信用户信息
    public function getOpenid()
    {
//        //通过code获得openid
//        if (!isset($_GET['code'])){
//            //触发微信返回code码
//            //$baseUrl = urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
//            $baseUrl = urlencode($this->get_url());
//            $url = $this->__CreateOauthUrlForCode($baseUrl); // 获取 code地址
//            Header("Location: $url"); // 跳转到微信授权页面 需要用户确认登录的页面
//            exit();
//        } else {
            //上面获取到code后这里跳转回来
            $code = input('param.code');
            $data = $this->getOpenidFromMp($code);//获取网页授权access_token和用户openid
            $data2 = $this->GetUserInfo($data['access_token'],$data['openid']);//获取微信用户信息
            $data['nickname'] = empty($data2['nickname']) ? '微信用户' : trim($data2['nickname']);
            $data['sex'] = $data2['sex'];
            $data['head_pic'] = $data2['headimgurl'];
            $_SESSION['openid'] = $data['openid'];
            $data['oauth'] = 'wx';
            if(isset($data2['unionid'])){
                $data['unionid'] = $data2['unionid'];
            }
            return $data;
//        }
    }

    /**
     * 获取当前的url 地址
     * @return type
     */
    private function get_url() {
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
        return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
    }

    /**
     *
     * 通过code从工作平台获取openid机器access_token
     * @param string $code 微信跳转回来带上的code
     *
     * @return openid
     */
    public function GetOpenidFromMp($code)
    {
        //通过code获取网页授权access_token 和 openid 。网页授权access_token是一次性的，而基础支持的access_token的是有时间限制的：7200s。
        //1、微信网页授权是通过OAuth2.0机制实现的，在用户授权给公众号后，公众号可以获取到一个网页授权特有的接口调用凭证（网页授权access_token），通过网页授权access_token可以进行授权后接口调用，如获取用户基本信息；
        //2、其他微信接口，需要通过基础支持中的“获取access_token”接口来获取到的普通access_token调用。
        $url = $this->__CreateOauthUrlForOpenid($code);
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);//设置超时
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $res = curl_exec($ch);//运行curl，结果以jason形式返回
        $data = json_decode($res,true);
        curl_close($ch);
        return $data;
    }


    /**
     *
     * 通过access_token openid 从工作平台获取UserInfo
     * @return openid
     */
    public function GetUserInfo($access_token,$openid)
    {
        // 获取用户 信息
        $url = $this->__CreateOauthUrlForUserinfo($access_token,$openid);
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);//设置超时
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $res = curl_exec($ch);//运行curl，结果以jason形式返回
        $data = json_decode($res,true);
        curl_close($ch);
        //获取用户是否关注了微信公众号， 再来判断是否提示用户 关注
//        if(!isset($data['unionid'])){
//            $access_token2 = $this->get_access_token();//获取基础支持的access_token
//            $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token2&openid=$openid";
//            $subscribe_info = httpRequest($url,'GET');
//            $subscribe_info = json_decode($subscribe_info,true);
//            $data['subscribe'] = $subscribe_info['subscribe'];
//        }
        return $data;
    }


    public function get_access_token(){
        // 判断 access_token 缓存是否存在
        if (empty(cache('access_token'))) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . config('wechat.app_id') . "&secret=" . config('wechat.app_secret');
            $return = httpRequest($url,'GET');
            $return = json_decode($return,1);

            // 设置 access_token 缓存
            cache('access_token', $return['access_token'], $return['expires_in'] / 2); // 其中 expires_in 凭证有效时间为7200秒，这里缓存有效期取3600秒
        }

        // 获取 access_token 缓存
        //$this->accessToken = cache('access_token');
        $accessToken = cache('access_token');
        return $accessToken;
    }

    /**
     *
     * 构造获取code的url连接
     * @param string $redirectUrl 微信服务器回跳的url，需要url编码
     *
     * @return 返回构造好的url
     */
    private function __CreateOauthUrlForCode($redirectUrl)
    {
        $urlObj["appid"] = config('wechat.app_id');
        $urlObj["redirect_uri"] = "$redirectUrl";
        $urlObj["response_type"] = "code";
//        $urlObj["scope"] = "snsapi_base";
        $urlObj["scope"] = "snsapi_userinfo";
        $urlObj["state"] = "STATE"."#wechat_redirect";
        $bizString = $this->ToUrlParams($urlObj);

        return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
    }

    /**
     *
     * 构造获取open和access_toke的url地址
     * @param string $code，微信跳转带回的code
     *
     * @return 请求的url
     */
    private function __CreateOauthUrlForOpenid($code)
    {
        $urlObj["appid"] = config('wechat.app_id');
        $urlObj["secret"] = config('wechat.app_secret');
        $urlObj["code"] = $code;
        $urlObj["grant_type"] = "authorization_code";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;
    }

    /**
     *
     * 构造获取拉取用户信息(需scope为 snsapi_userinfo)的url地址
     * @return 请求的url
     */
    private function __CreateOauthUrlForUserinfo($access_token,$openid)
    {
        $urlObj["access_token"] = $access_token;
        $urlObj["openid"] = $openid;
        $urlObj["lang"] = 'zh_CN';
        $bizString = $this->ToUrlParams($urlObj);
        return "https://api.weixin.qq.com/sns/userinfo?".$bizString;
    }

    /**
     *
     * 拼接签名字符串
     * @param array $urlObj
     *
     * @return 返回已经拼接好的字符串
     */
    private function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v)
        {
            if($k != "sign"){
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }
}