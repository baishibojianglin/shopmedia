<?php

namespace app\api\controller;

use app\common\lib\Aes;
use app\common\lib\exception\ApiException;
use app\common\lib\IAuth;
use app\common\model\User;
use think\Controller;

/**
 * api模块客户端登录控制器类
 * Class Login
 * @package app\api\controller
 */
class Login extends Common
{
    /**
     * 用户登录
     * 系统默认以 手机号码 + 短信验证码 注册，以 手机号码 + 短信验证码（或密码） 登录
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

        // 传入的参数
        $param = input('param.');

        // 判断传入的参数是否存在
        // 手机号码
        if (empty($param['phone'])) {
            return show(config('code.error'), '手机号码不能为空', [], 404);
        }/* else {
            // TODO：客户端需对手机号码AES加密（可以与密码一起加密），服务端对手机号码AES解密
            $param['phone'] = Aes::opensslDecrypt($param['phone']);
        }*/

        // 密码
        if (empty($param['password'])) {
            return show(config('code.error'), '密码不能为空', [], 404);
        }


        // validate验证
        $validate = validate('User');
        if (!$validate->check($param, [], 'login')) {
            return show(config('code.error'), $validate->getError(), [], 403);
        }

        // 设置登录的唯一性token
        $token = IAuth::setAppLoginToken($param['phone']);
        $data = [
            'token' => $token, // token
            'token_time' => strtotime('+' . config('app.login_time_out_day') . 'days'), // token失效时间
        ];

        // 查询该手机号用户是否存在
        $user = User::get(['phone' => $param['phone']]);
        if ($user && $user['status'] == config('code.status_enable')) { // 用户已存在，则登录并更新token和token失效时间
            // 当通过密码登录时，判断密码是否正确
            if (!empty($param['password'])) {
                if (IAuth::encrypt($param['password']) != $user['password']) {
                    return show(config('code.error'), '密码错误', [], 403);
                }
            }

            // 更新token和token失效时间
            try { // 捕获异常
                $id = model('User')->save($data, ['phone' => $param['phone']]); // 更新
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
        } else {
            return show(config('code.error'), '用户不存在', [], 404);
        }

        // 判断是否登录成功
        if ($id) {
            // 返回token给客户端
            $result = [
                'token' => (new Aes())->encrypt($token . '&' . $user['user_id']), // AES加密（自定义拼接字符串）
                'user_id' => $user['user_id'],
                'user_name' => $user['user_name'],
                'phone' => $user['phone'],
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
            // 实例化Aes
            $aesObj = new Aes();


            // 判断传入的参数是否存在及合法性
            // 手机号码
            if (empty($param['phone'])) {
                return show(config('code.error'), '手机号码不能为空', [], 404);
            }/* else {
            // 客户端需对手机号码AES加密（可以与短信验证码一起加密），服务端对手机号码AES解密
                $param['phone'] = $aesObj->decrypt($param['phone']);
            }*/

            // 手机短信验证码
            if (empty($param['code'])) {
                return show(config('code.error'), '短信验证码不能为空', [], 404);
            } else {
                // 客户端需对短信验证码AES加密，服务端对短信验证码AES解密
                //$param['code'] = $aesObj->decrypt($param['code']);

                // 判断短信验证码是否合法
                $code = '1234'; // TODO：获取 调用阿里云短信服务接口时 生成的session值
                if ($code != $param['code']) {
                    return show(config('code.error'), '短信验证码错误', [], 404);
                }
            }

            // 密码
            if (empty($param['password'])) {
                return show(config('code.error'), '密码不能为空', [], 404);
            }
            // 确认两次密码一致性
            if ($param['repassword'] != $param['password']) {
                return show(config('code.error'), '两次输入密码不一致', [], 404);
            }


            // validate验证 TODO：需做注册场景的验证
            $validate = validate('User');
            if (!$validate->check($param, [], 'login')) {
                return show(config('code.error'), $validate->getError(), [], 403);
            }

            // 查询该手机号用户是否存在
            $user = User::get(['phone' => $param['phone']]);
            if ($user) { // 用户已存在
                return show(config('code.error'), '用户已存在', [], 403);
            } else { // 用户不存在，则注册用户
                // 设置唯一性token
                $token = IAuth::setAppLoginToken($param['phone']);

                $data['token'] = $token; // token
                $data['token_time'] = strtotime('+' . config('app.login_time_out_day') . 'days'); // token失效时间
                $data['user_name'] = 'Sustock-' . trim($param['phone']); // 定义默认用户名
                $data['phone'] = trim($param['phone']);
                $data['password'] = IAuth::encrypt($param['password']);
                $data['status'] = config('code.status_enable');

                // 注册用户
                try { // 捕获异常
                    $id = model('User')->add($data, 'user_id'); // 新增
                } catch (\Exception $e) {
                    throw new ApiException($e->getMessage(), 500, config('code.error'));
                }
            }

            // 判断是否注册成功
            if ($id) {
                // 返回token给客户端
                $result = [
                    'token' => Aes::opensslEncrypt($token . '&' . $id), // AES加密（自定义拼接字符串）
                ];
                return show(config('code.success'), 'OK', $result);
            } else {
                return show(config('code.error'), '用户注册失败', [], 403);
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
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

            // 判断传入的参数是否存在及合法性
            // 手机号码
            if (empty($param['phone'])) {
                return show(config('code.error'), '手机号码不能为空', [], 404);
            }

            // 手机短信验证码
            if (empty($param['code'])) {
                return show(config('code.error'), '短信验证码不能为空', [], 404);
            } else {
                // 客户端需对短信验证码AES加密，服务端对短信验证码AES解密
                //$param['code'] = $aesObj->decrypt($param['code']);

                // 判断短信验证码是否合法
                $code = '1234'; // TODO：获取 调用阿里云短信服务接口时 生成的session值
                if ($code != $param['code']) {
                    return show(config('code.error'), '短信验证码错误', [], 404);
                }
            }

            // 密码
            if (empty($param['password'])) {
                return show(config('code.error'), '密码不能为空', [], 404);
            }
            // 确认两次密码一致性
            if ($param['repassword'] != $param['password']) {
                return show(config('code.error'), '两次输入密码不一致', [], 404);
            }


            // validate验证 TODO：需做更新密码场景的验证
            $validate = validate('User');
            if (!$validate->check($param, [], 'login')) {
                return show(config('code.error'), $validate->getError(), [], 403);
            }

            // 查询该手机号用户是否存在
            $user = User::get(['phone' => $param['phone']]);
            if (!$user) { // 用户不存在
                return show(config('code.error'), '用户不存在', [], 404);
            } else { // 用户存在，更新密码
                $data['password'] = IAuth::encrypt($param['password']);

                // 更新密码
                try { // 捕获异常
                    $result = model('User')->save($data, ['phone' => $param['phone']]); // 更新
                } catch (\Exception $e) {
                    throw new ApiException($e->getMessage(), 500, config('code.error'));
                }
            }

            // 判断是否注册成功
            if ($result) {
                return show(config('code.success'), '密码更新成功', [], 201);
            } else {
                return show(config('code.error'), '密码更新失败', [], 403);
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
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
            // 获取token
            $accessUserToken = Aes::opensslDecrypt($this->headers['access-user-token']); // AES解密
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
            if ($result) {
                return show(config('code.success'), '退出登录成功', [], 201);
            } else {
                return show(config('code.error'), '退出登录失败', [], 403);
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }

    /**
     * 保存新建的资源（用户登录或注册）
     * 系统默认以 手机号码 + 短信验证码 注册，以 手机号码 + 短信验证码（或密码） 登录
     *
     * @return \think\response\Json
     * @throws ApiException
     */
    public function save()
    {
        // 判断是否为POST请求
        if (!request()->isPost()) {
            return show(config('code.error'), '请求不合法', [], 400);
        }

        // 传入的参数
        $param = input('param.');
        // 实例化Aes
        $aesObj = new Aes();

        // 判断传入的参数是否存在
        // 手机号码
        if (empty($param['phone'])) {
            return show(config('code.error'), '手机号码不能为空', [], 404);
        }/* else {
            // 客户端需对手机号码AES加密（可以与短信验证码一起加密），服务端对手机号码AES解密
            $param['phone'] = $aesObj->decrypt($param['phone']);
        }*/
        // 手机短信验证码或密码二选一
        if (empty($param['code']) && empty($param['password'])) {
            return show(config('code.error'), '手机短信验证码或密码不能为空', [], 404);
        }/* else {
            // 客户端需对短信验证码AES加密，服务端对短信验证码AES解密
            $param['code'] = $aesObj->decrypt($param['code']);
        }*/

        // 当通过手机短信验证码登录时，判断手机短信验证码是否合法
        if (!empty($param['code'])) {
            $code = ''; // TODO 获取 调用阿里云短信服务接口时 生成的session值
            if ($code != $param['code']) {
                return show(config('code.error'), '手机短信验证码错误', [], 404);
            }
        }

        // validate验证
        $validate = validate('User');
        if (!$validate->check($param, [], 'login')) {
            return show(config('code.error'), $validate->getError(), [], 403);
        }

        $token = IAuth::setAppLoginToken($param['phone']); // 设置登录的唯一性token
        $data = [
            'token' => $token, // token
            'token_time' => strtotime('+' . config('app.login_time_out_day') . 'days'), // token失效时间
        ];

        // 查询该手机号用户是否存在
        $user = User::get(['phone' => $param['phone']]);
        if ($user && $user->status == config('code.status_enable')) { // 用户已存在，则登录并更新token和token失效时间
            // 当通过密码登录时，判断密码是否正确
            if (!empty($param['password'])) {
                if (IAuth::encrypt($param['password']) != $user->password) {
                    return show(config('code.error'), '密码错误', [], 403);
                }
            }

            // 更新token和token失效时间
            try { // 捕获异常
                $id = model('User')->save($data, ['phone' => $param['phone']]); // 更新
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
        } else { // 如果为首次登录，则注册用户
            if (!empty($param['code'])) {
                $data['user_name'] = 'Sustock-' . $param['phone']; // 定义默认用户名
                $data['phone'] = $param['phone'];
                $data['status'] = config('code.status_enable');

                // 注册用户
                try { // 捕获异常
                    $id = model('User')->add($data, 'user_id'); // 新增
                } catch (\Exception $e) {
                    throw new ApiException($e->getMessage(), 500, config('code.error'));
                }
            } else { // 首次登录（实为注册），当短信验证码为空，则以密码（上面已经判断短信验证码或密码二选一）注册时，并且系统默认不能用密码注册，则会注册失败
                return show(config('code.error'), '用户不存在', [], 403);
            }
        }

        // 判断是否登录或注册成功
        if ($id) {
            // 返回token给客户端
            $result = [
                'token' => Aes::opensslEncrypt($token . '&' . $id), // AES加密（自定义拼接字符串）
            ];
            return show(config('code.success'), 'OK', $result);
        } else {
            return show(config('code.error'), '用户登录失败', [], 403);
        }
    }
}
