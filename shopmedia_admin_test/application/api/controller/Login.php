<?php

namespace app\api\controller;

use app\common\lib\Aes;
use app\common\lib\exception\ApiException;
use app\common\lib\IAuth;
use app\common\model\User;
use think\Controller;
use think\Db;

/**
 * api模块客户端登录控制器类
 * Class Login
 * @package app\api\controller
 */
class Login extends Common
{
    /**
     * 检查电话是否存在
     * @return \think\response\Json
     */
    public function hasphone(){
        $form = input();
        $match['phone'] = $form['phone'];
        $userlist = Db::name('user')->where($match)->find();
        if(empty($userlist)){
            //用户不存在
            $message['status'] = 0;
            $message['words'] = '用户不存在';
            return json($message);
        }
        if($userlist['status'] == 0){
            //用户被禁用
            $message['status'] = 0;
            $message['words'] = '账号被冻结';
            return json($message);
        }
        $message['status'] = 1;
        return json($message);
    }

    /**
     * 用户登录（备用）
     * 系统默认以 手机号码 + 短信验证码 注册，以 手机号码 + 密码（暂不支持短信验证码） 登录
     *
     * @return \think\response\Json
     * @throws ApiException
     */
    public function login0()
    {
        // 判断是否为PUT请求
        if (!request()->isPut()) {
            return show(config('code.error'), '请求不合法', [], 400);
        }

        // 传入的参数
        $param = input('param.');
        $param['phone'] = trim($param['phone']);

        // 实例化Aes
        $aesObj = new Aes();
   
        // 判断传入的参数是否存在
        // 手机号码
        if (empty($param['phone'])) {
            return show(config('code.error'), '手机号码不能为空', [], 401);
        } else {
            // TODO：客户端需对手机号码AES加密（可以与密码一起加密），服务端对手机号码AES解密
            $param['phone'] = $aesObj->decrypt($param['phone']);
        }

        // 密码
        if (empty($param['password'])) {
            return show(config('code.error'), '密码不能为空', [], 401);
        }

        // validate验证
        $validate = validate('User');
        if (!$validate->check($param, [], 'login')) {
            return show(config('code.error'), $validate->getError(), [], 403);
        }

        // 设置登录的唯一性token
        $token = IAuth::setAppLoginToken($param['phone']);

        // 待更新数据
        $data = [
            'token' => $token, // token
            'token_time' => strtotime('+' . config('app.login_time_out')), // token失效时间
            'login_time' => time(), // 登录时间
            'login_ip' => request()->ip() // 登录IP
        ];

        // 查询该手机号用户是否存在
        $user = User::get(['phone' => $param['phone']]);
        if ($user && $user['status'] == config('code.status_enable')) { // 用户存在且已启用，则登录并更新token和token失效时间   
            // 当通过密码登录时，判断密码是否正确
            if (!empty($param['password'])) {
                if (IAuth::encrypt($aesObj->decrypt($param['password'])) != $user['password']) {
                    return show(config('code.error'), '密码错误', [], 401);
                }
            }

            // 更新token和token失效时间
            try { // 捕获异常
                $id = model('User')->save($data, ['phone' => $param['phone']]); // 更新
            } catch (\Exception $e) {
                return show(config('code.error'), $e->getMessage(), '', 500);
                //throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
        } else {
            if (!$user) {
                return show(config('code.error'), '用户不存在', '', 404);
            }
            if ($user['status'] == config('code.status_disable')) {
                return show(config('code.error'), '用户被冻结', '', 401);
            }
        }

        // 判断是否登录成功
        if ($id) {
            // 返回token给客户端
            $result = [
                'token' => $aesObj->encrypt($token . '&' . $user['user_id']), // AES加密（自定义拼接字符串）
                'user_id' => $user['user_id'],
                'user_name' => $user['user_name'],
                'phone' => $user['phone']
            ];
            return show(config('code.success'), 'OK', $result);
        } else {
            return show(config('code.error'), '用户登录失败', [], 403);
        }
    }

    /**
     * 用户注册
     * 以 手机号码 + 短信验证码 注册，同时需输入 密码 便于 手机号码 + 密码 登录
     * @return \think\response\Json
     * @throws ApiException
     */
    public function register()
    {
        // 判断是否为POST请求
        if (request()->isPost()) {
            // 传入的参数
            $param = input('param.');
            $param['phone'] = trim($param['phone']);

            // 实例化Aes
            $aesObj = new Aes();

            // 判断传入的参数是否存在及合法性
            // 手机号码
            if (empty($param['phone'])) {
                return show(config('code.error'), '手机号码不能为空', '', 401);
            }/* else {
            // 客户端需对手机号码AES加密（可以与短信验证码一起加密），服务端对手机号码AES解密
                $param['phone'] = $aesObj->decrypt($param['phone']);
            }*/

            // 手机短信验证码
            if (empty($param['verify_code'])) {
                return show(config('code.error'), '短信验证码不能为空', '', 401);
            } else {
                // 客户端需对短信验证码AES加密，服务端对短信验证码AES解密
                //$param['verify_code'] = $aesObj->decrypt($param['verify_code']);

                // 判断短信验证码是否合法
                $verifyCode = $param['return_code']; // TODO：获取 调用阿里云短信服务接口时 生成的session值
                if (empty($verifyCode) || $verifyCode != $param['verify_code']) {
                    return show(config('code.error'), '短信验证码错误', '', 401);
                }
            }

            // 密码
            if (empty($param['password'])) {
                return show(config('code.error'), '密码不能为空', '', 401);
            }
            // 确认两次密码一致性
            /*if ($param['repassword'] != $param['password']) {
                return show(config('code.error'), '两次输入密码不一致', '', 401);
            }*/
   
            // validate验证 TODO：需做注册场景的验证
            $validate = validate('User');
            if (!$validate->check($param, [], 'login')) {
                return show(config('code.error'), $validate->getError(), '', 403);
            }

            // 根据（目标客户或下级业务员）邀请码获取业务员信息
            $salesman = Db::name('user_salesman')->where(['invitation_code|son_invitation_code' => $param['invitation_code']])->find();
            // 判断（目标客户或下级业务员）邀请码对应的业务员是否存在，并且两者的邀请码不能相同
            if (!$salesman || $salesman['invitation_code'] == $salesman['son_invitation_code']) {
                return show(config('code.error'), '邀请码错误', '', 401);
            }
            // 判断该业务员所属用户是否存在广告主业务员角色，存在则默认创建广告主
            $advertiserSalesman = Db::name('user_salesman')->where(['uid' => $salesman['uid'], 'role_id' => 5])->find();

            /* TODO：获取新增的目标客户或下级业务员的角色类型，封装方法 s */
            $roleId = ''; 
            // 判断为（目标客户）邀请码时
            if ($salesman['invitation_code'] == $param['invitation_code']) {
                // 根据业务员角色类型，获取新增的目标客户的角色类型
                switch ($salesman['role_id']) {
                    case 4: // 广告屏业务员
                        $roleId = 2; // 广告屏合作商
                        break;
                    case 5: // 广告业务员
                        $roleId = 7; // 广告主
                        break;
                    case 6: // 店铺业务员
                        $roleId = 3; // 店家
                        break;
                    default:
                        // 其他情况默认执行代码
                }
            }

            // 判断为（下级业务员）邀请码时
            if($salesman['son_invitation_code'] == $param['invitation_code']) {
                $roleId = $salesman['role_id'];
            }
            /* TODO：获取新增的目标客户或下级业务员的角色类型，封装方法 e */

            // 查询该手机号用户是否存在
            $user = User::get(['phone' => $param['phone']]);
            if ($user) { // 用户已存在
                return show(config('code.error'), '该手机号已注册', '', 403);
            } else { // 用户不存在，则注册用户
                // 设置唯一性token
                $token = IAuth::setAppLoginToken($param['phone']);

                // 用户基本信息
                $data['token'] = $token; // token
                $data['token_time'] = strtotime('+' . config('app.login_time_out')); // token失效时间
                $data['user_name'] = 'sustock-' . trim($param['phone']); // 定义默认用户名

                if ($advertiserSalesman) {
                    $data['role_ids'] = implode(',', array_unique([$roleId, 7])); // 用户角色ID集合，默认创建广告主角色
                } else {
                    $data['role_ids'] = $roleId; // 用户角色ID集合
                }

                $data['phone'] = trim($param['phone']);
                $data['phone_verified'] = 1; // 手机号已验证
                $data['password'] = IAuth::encrypt($param['password']);
                $data['status'] = config('code.status_enable');
                $data['create_time'] = time(); // 创建时间
                $data['create_ip'] = request()->ip(); // 创建IP

                // 入库操作
                /* 手动控制事务 s */
                // 启动事务
                Db::startTrans();
                try {
                    // 注册用户基本信息
                    $res[0] = $userId = Db::name('user')->strict(false)->insertGetId($data); // 新增数据并返回主键值

                    // 新增（目标客户或下级业务员）用户角色明细
                    if ($roleId == 2) { // 广告屏合作商
                        $data1['user_id'] = $userId;
                        $data1['salesman_id'] = $salesman['id']; // 业务员ID
                        $data1['role_id'] = $roleId; // 用户角色ID
                        $data1['status'] = config('code.status_enable');
                        $data1['create_time'] = time(); // 创建时间
                        $res[1] = Db::name('user_partner')->insert($data1);
                    } elseif ($roleId == 3) { // 店家
                        $data1['user_id'] = $userId;
                        $data1['salesman_id'] = $salesman['id']; // 业务员ID
                        $data1['role_id'] = $roleId; // 用户角色ID
                        $data1['status'] = config('code.status_enable');
                        $data1['create_time'] = time(); // 创建时间
                        $res[1] = Db::name('user_shopkeeper')->insert($data1);
                    } elseif ($roleId == $salesman['role_id']) { // 下级业务员
                        // TODO：新增下级业务员数据
                        $data1['uid'] = $userId;
                        $data1['role_id'] = $roleId; // 用户角色ID
                        $data1['company_id'] = $salesman['company_id']; // 分公司ID
                        $data1['parent_id'] = $salesman['id']; // 上级ID
                        $data1['status'] = config('code.status_enable');
                        $data1['create_time'] = time(); // 创建时间

                        // （下级业务员）邀请码
                        // 获取（下级业务员）邀请码集合
                        $sonInvitationCodes = Db::name('user_salesman')->column('son_invitation_code');

                        // 生成唯一（下级业务员）邀请码，加前缀 1 用于区别于（目标客户）邀请码（两种邀请码也必须不同）
                        //return show(config('code.error'), '注册失败，请重试', $sonInvitationCodes, 500);
                        $data1['son_invitation_code'] = uniqueRand('1', 10000, 99999, $sonInvitationCodes);

                        // （目标客户）邀请码
                        // 获取（下级业务员）邀请码集合
                        $invitationCodes = Db::name('user_salesman')->column('invitation_code');
                        // 生成唯一（目标客户）邀请码，加前缀 2 用于区别于（下级业务员）邀请码（两种邀请码也必须不同）
                        $data1['invitation_code'] = uniqueRand('2', 10000, 99999, $invitationCodes);

                        $res[1] = Db::name('user_salesman')->insert($data1);
                    }

                    if ($advertiserSalesman) {
                        // 默认创建广告主角色
                        $data2['user_id'] = $userId;
                        $data2['salesman_id'] = $advertiserSalesman['id']; // 业务员ID
                        //$data2['role_id'] = 7; // 用户角色ID
                        $data2['status'] = config('code.status_enable');
                        $data2['create_time'] = time(); // 创建时间
                        $res[2] = Db::name('user_advertiser')->insert($data2);
                    }

                    // 任意一个表写入失败都会抛出异常，TODO：是否可以不做该判断
                    if (in_array(0, $res)) {
                        return show(config('code.error'), '注册失败', '', 403);
                    }

                    // 返回token给客户端
                    $result = [
                        'token' => $aesObj->encrypt($token . '&' . $userId) // AES加密（自定义拼接字符串）
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
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 找回密码
     * 手机号码 + 短信 方式
     */
    public function pwd()
    {
        // 判断为PUT请求
        if (request()->isPut()) {
            // 传入的参数
            $param = input('param.');
            $param['phone'] = trim($param['phone']);

            // 判断传入的参数是否存在及合法性
            // 手机号码
            if (empty($param['phone'])) {
                return show(config('code.error'), '手机号码不能为空', '', 401);
            }

            // 手机短信验证码
            if (empty($param['verify_code'])) {
                return show(config('code.error'), '短信验证码不能为空', '', 401);
            } else {
                // 客户端需对短信验证码AES加密，服务端对短信验证码AES解密
                //$param['verify_code'] = $aesObj->decrypt($param['verify_code']);

                // 判断短信验证码是否合法
                $verifyCode = $param['return_code']; // TODO：获取 调用阿里云短信服务接口时 生成的session值
                if (empty($verifyCode) || $verifyCode != $param['verify_code']) {
                    return show(config('code.error'), '短信验证码错误', '', 401);
                }
            }

            // 密码
            if (empty($param['password'])) {
                return show(config('code.error'), '密码不能为空', '', 401);
            }
            // 确认两次密码一致性
            if ($param['repassword'] != $param['password']) {
                return show(config('code.error'), '两次输入密码不一致', '', 401);
            }


            // validate验证 TODO：需做更新密码场景的验证
            /*$validate = validate('User');
            if (!$validate->check($param, '', 'login')) {
                return show(config('code.error'), $validate->getError(), '', 403);
            }*/

            // 查询该手机号用户是否存在
            $user = User::get(['phone' => $param['phone']]);
            if (!$user) { // 用户不存在
                return show(config('code.error'), '用户不存在', '', 404);
            } else { // 用户存在，更新密码
                $data['password'] = IAuth::encrypt($param['password']);
                // 更新密码
                try { // 捕获异常
                    $result = model('User')->save($data, ['phone' => $param['phone']]); // 更新
                } catch (\Exception $e) {
                    throw new ApiException($e->getMessage(), 500, config('code.error'));
                }
            }

            // 判断是否成功
            if ($result) {
                return show(config('code.success'), '密码更新成功', '', 201);
            } else {
                return show(config('code.error'), '密码更新失败', '', 403);
            }
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 退出登录
     * 方式1.把token字段值清空，让app再次请求的时候，找不到token，从而登录失败
     * 方式2.把token失效时间字段token_time值清空或修改，使当前时间大于token失效时间时，登录时间过期，从而登录失败
     *
     * @return \think\response\Json
     * @throws ApiException
     */
    public function logout()
    {
        // 判断为PUT请求
        if (request()->isPut()) {
            // 实例化Aes
            $aesObj = new Aes();
            // 获取token
            $accessUserToken = $aesObj->decrypt($this->headers['access-user-token']); // AES解密
            list($token, $id) = explode('&', $accessUserToken); // token

            // 清空token或token失效时间
            $data = [
                //'token' => '', // token
                'token_time' => 0, // token失效时间
            ];
            try { // 捕获异常
                $result = model('User')->save($data, ['token' => $token]); // 更新
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
            if ($result === false) {
                return show(config('code.error'), '退出登录失败', [], 403);
            } else {
                return show(config('code.success'), '退出登录成功', [], 201);
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }

    /**
     * 用户登录与注册
     * 说明：
     * 1.系统默认以 手机号码 + 短信验证码 注册（首次登录），以 手机号码 + 短信验证码（或密码） 登录
     * 2.注册用户时的默认角色：①创建广告主；②默认绑定尾货业务员（用户扩展信息表）
     * 3.TODO：根据邀请码创建对应客户角色或业务员角色
     *
     * @return \think\response\Json
     * @throws ApiException
     */
    public function login()
    {
        // 判断是否为PUT请求
        if (!request()->isPut()) {
            return show(config('code.error'), '请求不合法', [], 400);
        }

        // 实例化Aes
        $aesObj = new Aes();

        // 传入的参数
        $param = input('param.');
        // 客户端需对手机号码、短信验证码（或密码）单独（或统一成一个参数如data）进行AES加密，服务端对这些参数进行AES解密
        $_data = json_decode($aesObj->decrypt($param['data']), true);

        // 判断传入的参数是否存在
        // 手机号码
        if (empty($_data['phone'])) {
            return show(config('code.error'), '手机号码不能为空', [], 401);
        }
        // 同意用户及隐私协议
        if (empty($_data['signed_agreement']) || $_data['signed_agreement'] != 1) {
            return show(config('code.error'), '请同意用户及隐私协议', [], 401);
        }
        // 手机短信验证码或密码二选一
        if (empty($_data['verify_code']) && empty($_data['password'])) {
            return show(config('code.error'), '短信验证码或密码不能为空', [], 401);
        }

        // 当通过手机短信验证码登录时，判断手机短信验证码是否合法
        if (!empty($_data['verify_code'])) {
            $verifyCode = $_data['return_code']; // TODO：获取 调用阿里云短信服务接口时 生成的session值
            if (empty($verifyCode) || $verifyCode != $_data['verify_code']) {
                return show(config('code.error'), '短信验证码错误', '', 401);
            }
        }

        // validate验证
        $validate = validate('User');
        if (!$validate->check($_data, [], 'login')) {
            return show(config('code.error'), $validate->getError(), [], 403);
        }

        // 设置登录的唯一性token
        $token = IAuth::setAppLoginToken($_data['phone']);
        $userData = [
            'token' => $token, // token
            'token_time' => strtotime('+' . config('app.login_time_out')), // token失效时间
        ];

        // 查询该手机号用户是否存在
        $user = User::get(['phone' => $_data['phone']]);
        if ($user && $user->status == config('code.status_enable')) { // 用户已存在，则登录并更新token和token失效时间
            // 获取用户ID
            $userId = $user['user_id'];

            // 当通过密码登录时，判断密码是否正确
            if (!empty($_data['password'])) {
                if (IAuth::encrypt($_data['password']) != $user->password) {
                    return show(config('code.error'), '密码错误', [], 403);
                }
            }

            // 更新token和token失效时间
            try { // 捕获异常
                $res = model('User')->save($userData, ['phone' => $_data['phone']]); // 更新
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
        } else { // 如果为首次登录，则注册用户
            if (!empty($_data['verify_code'])) {
                // 根据（目标客户或下级业务员）邀请码获取业务员信息
                $salesman = Db::name('user_salesman')->where(['invitation_code|son_invitation_code' => $_data['invitation_code']])->find();
                // 判断（目标客户或下级业务员）邀请码对应的业务员是否存在，并且两者的邀请码不能相同
                if (!$salesman || $salesman['invitation_code'] == $salesman['son_invitation_code']) {
                    return show(config('code.error'), '邀请码错误', '', 401);
                }
                // 获取新增的目标客户或下级业务员的角色类型
                $roleId = $this->_getRoleId($salesman, $_data['invitation_code']);
                // 判断该业务员所属用户是否存在广告主业务员角色，存在则默认创建广告主，不存在则创建为平台直属分公司（成都分公司）广告业务员的广告主
                $advertiserSalesman = Db::name('user_salesman')->where(['uid' => $salesman['uid'], 'role_id' => 5])->find();
                // 判断该业务员所属用户是否存在尾货业务员角色，存在则绑定尾货业务员，不存在则绑定平台直属分公司（成都分公司）尾货业务员
                $tgSalesman = Db::name('user_salesman')->where(['uid' => $salesman['uid'], 'role_id' => 8])->find();

                // 入库操作
                /* 手动控制事务 s */
                // 启动事务
                Db::startTrans();
                try {
                    $res = [];

                    // 注册用户基本信息
                    $userData['token'] = $token; // token
                    $userData['token_time'] = strtotime('+' . config('app.login_time_out')); // token失效时间
                    $userData['user_name'] = 'sustock-' . trim($_data['phone']); // 定义默认用户名
                    $userData['phone'] = trim($_data['phone']);
                    $userData['status'] = config('code.status_enable');
                    $userData['signed_agreement'] = $_data['signed_agreement'];
                    if ($advertiserSalesman) {
                        $userData['role_ids'] = implode(',', array_unique([$roleId, 7])); // 用户角色ID集合，默认创建广告主角色
                    } else {
                        $userData['role_ids'] = $roleId; // 用户角色ID集合
                    }
                    $userData['phone_verified'] = 1; // 手机号已验证
                    $userData['password'] = IAuth::encrypt($_data['phone']); // 短信注册时默认密码
                    $userData['create_time'] = time(); // 创建时间
                    $userData['create_ip'] = request()->ip(); // 创建IP
                    $res[0] = $userId = Db::name('User')->insertGetId($userData);
                    $res[0] === false ? 0 : true;

                    // 创建用户扩展信息，绑定尾货业务员
                    if (empty($tgSalesman)) {
                        // 获取平台直属分公司（成都分公司）尾货业务员
                        $tgSalesman = Db::name('user_salesman')->where(['role_id' => 8, 'company_id' => 1, 'parent_id' => 0, 'invitation_code' => '288888'])->find();
                    }
                    $userExtendData['user_id'] = $userId;
                    $userExtendData['tg_salesman_id'] = $tgSalesman['id'];
                    $res[1] = Db::name('user_extend')->insert($userExtendData)=== false ? 0 : true;

                    // 创建广告主角色
                    if (empty($advertiserSalesman)) {
                        // 获取平台直属分公司（成都分公司）广告业务员
                        $advertiserSalesman = Db::name('user_salesman')->where(['role_id' => 5, 'company_id' => 1, 'parent_id' => 0, 'invitation_code' => '255555'])->find();
                    }
                    $advertiserData['user_id'] = $userId;
                    $advertiserData['salesman_id'] = $advertiserSalesman['id']; // 业务员ID
                    $advertiserData['status'] = config('code.status_enable');
                    $advertiserData['create_time'] = time(); // 创建时间
                    $res[2] = Db::name('user_advertiser')->insert($advertiserData) === false ? 0 : true;

                    // 任意一个表写入失败都会抛出异常，TODO：是否可以不做该判断
                    if (in_array(0, $res)) {
                        return show(config('code.error'), '注册失败', '', 403);
                    }

                    // 提交事务
                    Db::commit();
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                    //return show(config('code.error'), '注册失败，请重试' . $e->getMessage(), '', 500);
                    throw new ApiException('注册失败，请重试', 500, config('code.error'));
                }
                /* 手动控制事务 e */
            } else { // 首次登录（实为注册），当短信验证码为空，则以密码（上面已经判断短信验证码或密码二选一）注册时，并且系统默认不能用密码注册，则会注册失败
                return show(config('code.error'), '用户不存在', [], 403);
            }
        }

        // 判断是否登录或注册成功
        if ($res) {
            // 返回token给客户端
            $result = [
                'token' => $aesObj->encrypt($token . '&' . $userId), // AES加密（自定义拼接字符串）
            ];
            return show(config('code.success'), 'OK', $result);
        } else {
            return show(config('code.error'), '用户登录失败', [], 403);
        }
    }

    /**
     * 获取新增的目标客户或下级业务员的角色类型
     * @param $salesman
     * @param $invitation_code
     * @return int|string
     */
    private function _getRoleId($salesman, $invitation_code)
    {
        $roleId = '';
        // 判断为（目标客户）邀请码时
        if ($salesman['invitation_code'] == $invitation_code) {
            // 根据业务员角色类型，获取新增的目标客户的角色类型
            switch ($salesman['role_id']) {
                case 4: // 广告屏业务员
                    $roleId = 2; // 广告屏合作商
                    break;
                case 5: // 广告业务员
                    $roleId = 7; // 广告主
                    break;
                case 6: // 店铺业务员
                    $roleId = 3; // 店家
                    break;
                case 8: // 尾货业务员
                    $roleId = 9; // 尾货卖家
                    break;
                default:
                    // 其他情况默认执行代码
            }
        }

        // 判断为（下级业务员）邀请码时
        if($salesman['son_invitation_code'] == $invitation_code) {
            $roleId = $salesman['role_id'];
        }

        return $roleId;
    }
}
