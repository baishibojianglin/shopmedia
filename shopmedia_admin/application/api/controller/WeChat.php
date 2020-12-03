<?php
/**
 * Created by PhpStorm.
 * User: Yan
 * Date: 2020/7/20
 * Time: 16:25
 */

namespace app\api\controller;

use think\Controller;
use think\Db;

/**
 * 微信公众号开发
 * Class WeChat
 * @package app\api\controller
 */
class WeChat extends Controller
{
    // 微信公众号全局唯一接口调用凭据 access_token
    public $accessToken = '';

    // 定义 回复文本、图片、视频、音乐、图文等消息相应的回复模板
    private $_msgTemplate = array(
        'text' => '<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[%s]]></Content></xml>', // 回复文本消息XML模板
        'image' => '<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[image]]></MsgType><Image><MediaId><![CDATA[%s]]></MediaId></Image></xml>', // 回复图片消息XML模板
        'music' => '<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[music]]></MsgType><Music><Title><![CDATA[%s]]></Title><Description><![CDATA[%s]]></Description><MusicUrl><![CDATA[%s]]></MusicUrl><HQMusicUrl><![CDATA[%s]]></HQMusicUrl><ThumbMediaId><![CDATA[%s]]></ThumbMediaId></Music></xml>', // 回复音乐消息XML模板
        'news' => '<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[news]]></MsgType><ArticleCount>%s</ArticleCount><Articles>%s</Articles></xml>', // （回复图文消息）图文消息主体XML模板
        'news_item' => '<item><Title><![CDATA[%s]]></Title><Description><![CDATA[%s]]></Description><PicUrl><![CDATA[%s]]></PicUrl><Url><![CDATA[%s]]></Url></item>', // （回复图文消息）某个图文消息XML模板
    );

    /**
     * 初始化方法
     */
    /*public function _initialize()
    {
        parent::_initialize();

        // 获取微信公众号access_token
        $this->getWxAccessToken();
    }*/

    /**
     * 检验signature 与 接收事件推送（关注/取消关注事件）
     *
     * 注意：tp5需要最先判断 $_GET['echostr'] 是否存在（原生PHP写法不需要），否则不会执行 else 逻辑
     */
    public function index()
    {
        // 获取随机字符串
        if (!empty($_GET['echostr'])) {
            $echostr = $_GET['echostr'];
            if($this->checkSignature() && $echostr){ // 第一次接入微信API接口时
                echo $echostr; // 这样写才能验证成功
                exit;
            }
        } else {
            $this->responseMsg();
        }
    }

    /**
     * 检验signature
     *
     * 加密/校验流程如下：
     * 1）将token、timestamp、nonce三个参数进行字典序排序
     * 2）将三个参数字符串拼接成一个字符串进行sha1加密
     * 3）开发者获得加密后的字符串可与signature对比，标识该请求来源于微信
     *
     * @return bool
     */
    public function checkSignature()
    {
        // 1）将token、timestamp、nonce三个参数进行字典序排序
        $signature = input('signature'); //$_GET["signature"];
        $timestamp = input('timestamp'); //$_GET["timestamp"];
        $nonce = input('nonce'); //$_GET["nonce"];

        $token = config('wechat.token');
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);

        // 2）将三个参数字符串拼接成一个字符串进行sha1加密
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        // 3）开发者获得加密后的字符串可与signature对比，标识该请求来源于微信
        if($tmpStr == $signature){ // 第一次接入微信API接口时
            return true;
        }else {
            return false;
        }
    }

    /**
     * index() 与 checkSignature() 方法拆分前的方法
     */
    public function checkSignature1()
    {
        //获得参数 signature nonce token timestamp echostr
        $nonce = $_GET['nonce'];
        $token = config('wechat.token');
        $timestamp = $_GET['timestamp'];
        $echostr = $_GET['echostr'];
        $signature = $_GET['signature'];
        //形成数组，然后按字典序排序
        $array = array();
        $array = array($nonce, $timestamp, $token);
        sort($array);
        //拼接成字符串,sha1加密 ，然后与signature进行校验
        $str = sha1(implode($array));
        if ($str == $signature && $echostr) {
            //第一次接入weixin api接口的时候
            echo $echostr;
            exit;
        } else {
            $this->responseMsg();
        }
    }

    /**
     * 接收事件推送并回复
     */
    public function responseMsg()
    {
        // 1.获取到微信推送过来的post数据（XML格式）
        //$postArr = $GLOBALS['HTTP_RAW_POST_DATA']; // php7版本以上不支持。php7版本以下需要使用需要开启php.ini配置文件里面的：always_populate_raw_post_data = On
        //$postArr = file_get_contents('php://input'); // php7+
        $postArr = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents('php://input');

        // 2.处理消息类型，并设置回复类型和内容
        $postObj = simplexml_load_string($postArr); // XML格式转对象
        if (strtolower($postObj->MsgType) == 'event') {
            $this->doEvent($postObj);
        }
    }

    /**
     * 处理事件消息
     * @param $postObj
     */
    private function doEvent($postObj)
    {
        switch (strtolower($postObj->Event)){
            case 'subscribe':
                // 关注事件
                $this->_subscribe($postObj);
                break;
            case 'unsubscribe':
                // 取消关注事件
                //$this->_unsubscribe($postObj);
                break;
            case 'scan':
                // 扫描带参数二维码事件
                $this->_scan($postObj);
                break;
            case 'click':
                // 自定义菜单点击事件
                $this->_click($postObj);
                break;
            default:
                break;
        }
    }

    /**
     * 关注公众号事件
     * @param $postObj
     *
     * 关注/取消关注事件 推送XML数据包示例：
     * <xml>
        <ToUserName><![CDATA[toUser]]></ToUserName>
        <FromUserName><![CDATA[FromUser]]></FromUserName>
        <CreateTime>123456789</CreateTime>
        <MsgType><![CDATA[event]]></MsgType>
        <Event><![CDATA[subscribe]]></Event>
       </xml>
     *
     * 参数说明：
     * $postObj->ToUserName = ''; // 开发者微信号
     * $postObj->FromUserName = ''; // 发送方帐号（一个OpenID）
     * $postObj->CreateTime = ''; // 消息创建时间 （整型）
     * $postObj->MsgType = ''; // 消息类型，event
     * $postObj->Event = ''; // 事件类型，subscribe(订阅)、unsubscribe(取消订阅)
     */
    private function _subscribe($postObj)
    {
        // 获取用户基本信息
        $openid = $postObj->FromUserName;
        $userInfo = $this->getUserInfo($openid);

        // 创建微信用户
        $this->createWxUser($userInfo, $postObj);

        // 回复用户消息
        //$this->_msgText($postObj, $userInfo); // 回复文本消息
        $this->_scanMsgNews($postObj, $userInfo); // 回复图文消息
    }

    /**
     * TODO：自定义菜单点击事件
     * @param $postObj
     */
    private function _click($postObj)
    {
        $newsItems = []; // 定义图文消息信息列表
        switch (strtolower($postObj->EventKey)){
            case 'ad_price':
                // 广告价格 菜单
                $newsItems = [
                    [
                        'title' => '店通传媒价格详情',
                        'description' => '',
                        'picUrl' => 'http://sustock-shopmedia.oss-cn-chengdu.aliyuncs.com/a68927afa975b22287476deca36c45dcxd_slyj3.jpeg',
                        'url' => 'https://mp.weixin.qq.com/s?__biz=MzIwNjYzNjMwOA==&tempkey=MTA3Ml9VbGtqY0pPNnl0SHVFb0JGaHdyMVJ0TURSNV8wdXZDUzctd0pSZmdqdFVJNGRueXlFeTJKX0k2T2wtU1hoMDhwSWQ1c0FoVnh2YXpKU203T0hSTGlpZ1VnaEJSMHhhWUVTUlZtWkZndHRwNEQ2TFBjbk8zR0V2OHI3Nk9rSHhYM3F4TW94ZU9hUlEyU0VmbkVFMmVHeXNUM2pXb242MWJUSVVjZVlRfn4%3D&chksm=171fd56320685c752c1faa71f3ca18275ea8d1d0a86e9c8659752ddd8968c9c96e608516d88d#rd'
                    ]
                ];
                $this->_msgNews($postObj, $newsItems);
                break;
            case 'contact_us':
                // 联系我们 菜单
                $content = "☆ 座机：028-8473 4560\n☆ 专属顾问\n顾问一：180 1150 4575\n顾问二：136 9344 4308\n☆ 公司地址：成都市武侯区武科东四路慧谷office 1幢";
                $this->_msgText($postObj, [], $content);
                break;
            default:
                break;
        }
    }

    /**
     * 回复文本消息
     * @param $postObj
     * @param array $userInfo
     * @param $content
     */
    private function _msgText($postObj, $userInfo = [], $content)
    {
        $toUser   = $postObj->FromUserName;
        $fromUser = $postObj->ToUserName;
        $time     = time();
        //$msgType  = 'text';
        /*$eventKey   = str_replace('qrscene_', '', $postObj->EventKey); // 事件KEY值（扫描带参数二维码事件，并关注）
        $content  = $userInfo['nickname'] . '，欢迎关注我们的公众号 ' . $fromUser . '，scene_id ' . $eventKey;*/
        $template = $this->_msgTemplate['text']; // 回复文本消息XML模板
        /*$template = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[%s]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    </xml>";*/
        $info     = sprintf($template, $toUser, $fromUser, $time,/* $msgType,*/ $content); // 从第二个参数开始与 XML 模板（如上注释的$template）参数的顺序一致，其中 MsgType 已在模板中不用传参
        //$info     = preg_replace('/[ ]/', '', $info); // 去掉空格
        echo $info;
    }

    /**
     * 回复图文消息
     * @param $postObj
     * @param $newsItems
     */
    private function _msgNews($postObj, $newsItems)
    {
        $toUser   = $postObj->FromUserName;
        $fromUser = $postObj->ToUserName;
        $time     = time();
        //$eventKey = str_replace('qrscene_', '', $postObj->EventKey); // 事件KEY值

        $articleCount = count($newsItems); // 图文消息个数

        $articles = ''; // 图文消息信息
        foreach ($newsItems as $key => $value) {
            //（回复图文消息）某个图文消息XML模板，以下两种写法都可行
            /*$articles .= '<item>
                            <Title><![CDATA[' . $value['title'] . ']]></Title>
                            <Description><![CDATA[' . $value['description'] . ']]></Description>
                            <PicUrl><![CDATA[' . $value['picUrl'] . ']]></PicUrl>
                            <Url><![CDATA[' . $value['url'] . ']]></Url>
                          </item>';*/

            $newsItemTemplate = $this->_msgTemplate['news_item'];
            $articles .= sprintf($newsItemTemplate, $value['title'], $value['description'], $value['picUrl'], $value['url']);
        }

        $template = $this->_msgTemplate['news']; // （回复图文消息）图文消息主体XML模板
        $info     = sprintf($template, $toUser, $fromUser, $time, $articleCount, $articles);
        echo $info;
    }

    /**
     * 回复图文消息（用于扫码或关注）
     * @param $postObj
     * @param $userInfo
     */
    private function _scanMsgNews($postObj, $userInfo)
    {
        $createTime = $postObj->CreateTime; // 消息创建时间 （整型）
        $eventKey = str_replace('qrscene_', '', $postObj->EventKey); // 事件KEY值

        // 定义图文消息信息列表
        $newsItems = [
            [
                'title' => '欢迎关注店通传媒',
                'description' => '惊喜不断，立即点击开始抽奖吧！',
                'picUrl' => 'https://sustock-shopmedia.oss-cn-chengdu.aliyuncs.com/wechant/prize_cover_for_gh_925caa1fb92e_20200807161612_200%C3%97200.png',
                'url' => config('app.http_type') . config('app.I_SERVER_NAME') . '/activity_h5?create_time=' . $createTime . '&scene_id=' . $eventKey . '&openid=' . $userInfo['openid'] . '&nickname=' . $userInfo['nickname'] . '&headimgurl=' . $userInfo['headimgurl']
            ]
        ];

        $this->_msgNews($postObj, $newsItems);
    }

    /**
     * 扫描带参数二维码事件
     * @param $postObj
     *
     * 1. 用户未关注时，进行关注后的事件推送（此时执行关注公众号事件，参考 _subscribe() 方法）
     * 推送XML数据包示例：
     * <xml>
        <ToUserName><![CDATA[toUser]]></ToUserName>
        <FromUserName><![CDATA[FromUser]]></FromUserName>
        <CreateTime>123456789</CreateTime>
        <MsgType><![CDATA[event]]></MsgType>
        <Event><![CDATA[subscribe]]></Event>
        <EventKey><![CDATA[qrscene_123123]]></EventKey>
        <Ticket><![CDATA[TICKET]]></Ticket>
       </xml>
     *
     * 2.用户已关注时的事件推送
     * 推送XML数据包示例：
     * <xml>
        <ToUserName><![CDATA[toUser]]></ToUserName>
        <FromUserName><![CDATA[FromUser]]></FromUserName>
        <CreateTime>123456789</CreateTime>
        <MsgType><![CDATA[event]]></MsgType>
        <Event><![CDATA[SCAN]]></Event>
        <EventKey><![CDATA[SCENE_VALUE]]></EventKey>
        <Ticket><![CDATA[TICKET]]></Ticket>
       </xml>
     */
    private function _scan($postObj)
    {
        /*$toUser   = $postObj->FromUserName;
        $fromUser = $postObj->ToUserName;
        $time     = time();
        $eventKey   = $postObj->EventKey; // 事件KEY值
        $content  = '欢迎关注我们的公众号 ' . $fromUser . ', scene_id ' . $eventKey;
        $template = $this->_msgTemplate['text'];
        $info     = sprintf($template, $toUser, $fromUser, $time, $content);
        echo $info;*/

        // 获取用户基本信息
        $openid = $postObj->FromUserName;
        $userInfo = $this->getUserInfo($openid);

        // 创建微信用户
        $this->createWxUser($userInfo, $postObj);

        // 回复用户消息
        //$this->_msgText($postObj, $userInfo); // 回复文本消息
        $this->_scanMsgNews($postObj, $userInfo); // 回复图文消息
    }

    /**
     * cURL请求
     * @param string $url 接口url
     * @param string $type 请求方式
     * @param string $res 返回数据类型
     * @param string $arr post请求参数
     * @return mixed
     *
     * 注意：先判断cURL是否错误，再关闭cURL资源
     */
    public function http_curl($url, $type = 'get', $res = 'json', $arr = '')
    {
        // 1.初始化curl（创建一个cURL资源）
        $ch = curl_init();

        // 2.设置curl的参数（设置URL和相应的选项）
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($type == 'post') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
        }

        // 3.采集（抓取URL并把它传递给浏览器）
        $output = curl_exec($ch);

        // 先判断cURL是否错误，再关闭cURL资源
        if (curl_errno($ch)) {
            return curl_error($ch);
        }

        // 4.关闭cURL资源，并且释放系统资源
        curl_close($ch);

        if ($res == 'json') {
            return json_decode($output, true);
        }
        //var_dump($output);
        return $output;
    }

    /**
     * 获取微信公众号access_token
     * @return mixed
     *
     * 注意：①需要在微信公众号配置IP白名单；②设置 access_token 缓存的有效期应小于凭证（即access_token）有效时间 expires_in
     */
    public function getWxAccessToken()
    {
        // 判断 access_token 缓存是否存在
        if (empty(cache('access_token'))) {
            // 1.请求url地址
            $appid = config('wechat.app_id');
            $appsecret = config('wechat.app_secret');
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $appsecret;

            $arr = $this->http_curl($url, 'get', 'json');
            //var_dump($arr);
            // 设置 access_token 缓存
            cache('access_token', $arr['access_token'], $arr['expires_in'] / 2); // 其中 expires_in 凭证有效时间为7200秒，这里缓存有效期取3600秒
        }

        // 获取 access_token 缓存
        //$this->accessToken = cache('access_token');
        $accessToken = cache('access_token');
        return $accessToken;
    }

    /**
     * 获取微信服务器IP地址
     */
    public function getWxServerIp()
    {
        //$accessToken = $this->accessToken;
        $accessToken = $this->getWxAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=' . $accessToken;

        $arr = $this->http_curl($url, 'get', 'json');
        var_dump($arr);
    }

    /**
     * 获取用户基本信息
     * @param $openid
     * @return mixed
     */
    public function getUserInfo($openid)
    {
        //$accessToken = $this->accessToken;
        $accessToken = $this->getWxAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $accessToken . '&openid=' . $openid . '&lang=zh_CN';

        $userInfo = $this->http_curl($url, 'get', 'json');
        return $userInfo;
    }

    /**
     * TODO：创建自定义菜单
     */
    public function definedItem()
    {
        // 创建微信菜单
        // 目前微信接口的调用方式都是通过curl post/get
        $accessToken = $this->getWxAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . $accessToken;
        $postArr = array(
            'button' => array(
                array(
                    'name' => urlencode('店通传媒'),
                    'sub_button' => array(
                        array(
                            'type' => 'view',
                            'name' => urlencode('广告投放'),
                            'url' => 'https://media.sustock.net/h5/'
                        ),
                        /*array(
                            'type' => 'click',
                            'name' => urlencode('广告价格'),
                            'key' => 'ad_price'
                        ),*/
                        array(
                            'type' => 'view',
                            'name' => urlencode('合作案例'),
                            'url' => 'https://media.sustock.net/case/'
                        )
                    )
                ),
                array(
                    'type' => 'view',
                    'name' => urlencode('八七兔商城'),
                    'url' =>'http://dt.dilinsat.com/'
                ),
                array(
                    'type' => 'click',
                    'name' => urlencode('联系我们'),
                    'key' => 'contact_us'
                )
            )
        );
        $postJson = urldecode(json_encode($postArr));
        //echo $postJson;
        $res = $this->http_curl($url, 'post', 'json', $postJson);
        //var_dump($res);
    }

    /**
     * 生成带参数的二维码：第一步、创建二维码ticket
     * @param int|string $sceneId 场景值ID
     * @param int $type 二维码类型：0临时，1永久
     * @return mixed
     */
    public function getQRCodeTicket($sceneId, $type = 0)
    {
        //$accessToken = $this->accessToken;
        $accessToken = $this->getWxAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $accessToken;

        if ($type == 1) {
            //生成永久二维码
            //临时二维码POST数据（json格式）例子：{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": 123}}}
            $postArr = [
                'action_name' => 'QR_LIMIT_SCENE', // 二维码类型，QR_LIMIT_SCENE为永久的整型参数值
                'action_info' => [ // 二维码详细信息
                    'scene' => [
                        'scene_id' => $sceneId // 场景值ID
                    ]
                ]
            ];
        } else {
            //生成临时二维码
            //临时二维码POST数据（json格式）例子：{"expire_seconds": 604800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": 123}}}
            $postArr = [
                'expire_seconds' => 604800, // 二维码有效时间（秒），24*60*60*7
                'action_name' => 'QR_SCENE', // 二维码类型，QR_SCENE为临时的整型参数值
                'action_info' => [ // 二维码详细信息
                    'scene' => [
                        'scene_id' => $sceneId // 场景值ID
                    ]
                ]
            ];
        }

        $postJson = json_encode($postArr);
        $QRCodeTicket = $this->http_curl($url, 'post', 'json', $postJson);
        //var_dump($QRCodeTicket);
        return $QRCodeTicket;
    }

    /**
     * 生成带参数的二维码：第二步、通过ticket到指定URL换取二维码
     * 浏览器访问如 http://media.dilinsat.com/index.php/api/show_wx_qrcode?sceneId=2&type=0
     * @param int|string $sceneId 场景值ID
     * @param int $type 二维码类型：0临时，1永久
     * @return string
     */
    public function showQRCode($sceneId, $type)
    {
        $QRCodeTicket = $this->getQRCodeTicket($sceneId, $type);
        $ticket = urlencode($QRCodeTicket['ticket']); // 提醒：TICKET记得进行UrlEncode
        $url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $ticket; // 不需要使用 http_curl() 转换

        $type = $type == 1 ? "永久二维码" : "临时二维码";
        echo $type . ' <img src="' . $url . '" title="' . $type . '" />';

        //return $url;
        //return $this->downloadQRCode($url, 'qrcode_for_gh_925caa1fb92e_scene_id_' . $sceneId);
        return $this->getLogoQRCode($url, $sceneId);
    }

    /**
     * 生成带logo的二维码并下载到服务器
     * @param $QR
     * @param $sceneId
     * @return resource|string
     */
    protected function getLogoQRCode($QR, $sceneId)
    {
        try{
            $logo = 'static/qrcode/logo_for_gh_925caa1fb92e_20200813120734_210.png';
            $im = @imagecreatetruecolor(430, 430);

            $QR = imagecreatefromstring(file_get_contents($QR));
            $logo = imagecreatefromstring(file_get_contents($logo));
            $QR_width = imagesx($QR);//二维码图片宽度 
            $QR_height = imagesy($QR);//二维码图片高度 
            $logo_width = imagesx($logo);//logo图片宽度 
            $logo_height = imagesy($logo);//logo图片高度 
            $logo_qr_width = $QR_width / 5;
            $scale = $logo_width/$logo_qr_width;
            $logo_qr_height = $logo_height/$scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;
            //重新组合图片并调整大小
            $a = imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
            $dir = "static/qrcode/";
            $filename = 'sceneid_' . $sceneId . '_qrcode_for_gh_925caa1fb92e' . '.png';
            imagepng($QR, $dir.$filename);
            /*if(file_exists($dir.$filename)){
                //上传图片到oss
                $k1 = time();
                $ch = curl_init(API_DOMAIN.'/oss/upload');
                $cfile = curl_file_create(realpath($dir.$filename),"image/png",realpath($dir.$filename));
                $data = [
                    'source'=>1,
                    'upload'=> $cfile,
                    'is_rename'=>0,
                    'set_dir'=>"wechat/",
                    'k1'=>$k1,
                    "k2"=>md5(md5($k1).C('SUPER_AUTH_KEY'))
                ];
                curl_setopt($ch, CURLOPT_POST,1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                $res = curl_exec($ch);
                curl_close($ch);
                dump($res);
            }*/
            echo '<img src="' . config('app.http_type') . config('app.I_SERVER_NAME') . '/' . $dir . $filename . '" title="' . $filename . '" />';
            return $dir.$filename;
        }catch(\Exception $e){
            echo $e->getMessage();
            return $QR;
        }
    }

    /**
     * 下载二维码到服务器
     * @param $url
     * @param $fileString
     * @return bool|string
     */
    protected function downloadQRCode($url, $fileString){
        if ($url == '') {
            return false;
        }
        $filename = $fileString . '.png';
        ob_start();
        readfile($url);
        $img = ob_get_contents();
        ob_end_clean();
        $size = strlen($img);
        $fp2 = fopen('static/qrcode/' . $filename, 'a');
        if (fwrite($fp2, $img) === false){
            exit();
        }
        fclose($fp2);
        return 'static/qrcode/' . $filename;
    }

    /**
     * 创建微信用户
     * @param $userInfo
     * @param $postObj
     */
    public function createWxUser($userInfo, $postObj)
    {
        // 查询用户是否存在
        $openid = $userInfo['openid'];
        $userOauth = Db::name('user_oauth')->where(['oauth' => 'wx', 'openid' => $openid])->find();
        // 用户不存在，则创建用户
        if (empty($userOauth)) {
            $data['oauth'] = 'wx';
            $data['openid'] = $openid;
            $data['verified'] = 1;
            $data['create_time'] = time();
            $data['create_ip'] = request()->ip();

            $eventKey = isset($postObj->EventKey) ? str_replace('qrscene_', '', $postObj->EventKey) : 0; // 事件KEY值
            $data['device_id'] = $eventKey; // 广告屏设备id

            $res = Db::name('user_oauth')->insertGetId($data);
        }
    }
}