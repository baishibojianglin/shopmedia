<?php

namespace app\admin\controller;

use app\common\lib\Aes;
use app\common\lib\exception\ApiException;

/**
 * admin模块登录控制器类
 * Class Login
 * @package app\admin\controller
 */
class Login extends Common
{
    /**
     * 后台登录
     * @return \think\response\Json
     * @throws ApiException
     */
    public function login()
    {
        // 传入的参数
        $param = input();

        // 实例化 AES 加解密类
        $aes = new Aes();

        // 解密验签sign
        $sign = $aes->adminDecrypt($param['sign']);
        // 判断验签sign是否正确
        if ($sign != 'jl_goodshop') {
            return show(config('code.error'), '验签错误', '', 403);
            /*$result['status'] = 0; $result['message'] = '验签不正确'; return json($result);*/
        }

        // 解密账号和密码
        $str = $aes->adminDecrypt($param['str']);
        // 字符串分解成变量
        parse_str($str, $data);
        // 账号是否为空
        if (empty($data['account'])) {
            return show(config('code.error'), '账号不能为空', '', 401);
        }
        // 密码是否为空
        if (empty($data['password'])) {
            return show(config('code.error'), '密码不能为空', '', 401);
        }
        // 验证码是否为空
        if (empty($data['verifycode'])) {
            return show(config('code.error'), '验证码不能为空', '', 401);
        }
        // 验证码是否正确
        if ($data['verifycode'] != session('code')) {
            return show(config('code.error'), '验证码错误', '', 401);
        }

        // 查询该账号管理用户是否存在
        $map['account'] = $data['account'];
        $adminUser = model('Admin')->where($map)->field('token_time', true)->find();
        if (empty($adminUser)) {
            return show(config('code.error'), '账号不存在', '', 404);
        }
        if (md5($data['password']) != $adminUser['password']) {
            return show(config('code.error'), '账号或密码错误', '', 401);
        }
        if ($adminUser['status'] != config('code.status_enable')) {
            return show(config('code.error'), '账号被禁用', '', 403);
        }

        // 通过验证，生成token
        $token = md5(uniqid(mt_rand(), true)) . mt_rand();
        $saveData = [
            'token' => $token, // token
            'token_time' => strtotime('+' . config('admin.login_time_out')), // token失效时间
            'login_time' => time(), // 登录时间
            'login_ip' => request()->ip() // 登录IP
        ];

        // 更新token和token失效时间等
        try { // 捕获异常
            $result = model('Admin')->save($saveData, ['account' => $data['account']]);
        } catch (\Exception $e) {
            throw new ApiException('网络忙，请重试', 500, config('code.error'));
        }

        // 判断是否登录成功
        if ($result) {
            // 重新获取管理用户信息，排出字段 password、token_time
            $adminUser = model('Admin')->where($map)->field('password,token_time', true)->find();
            // 用AES加密token
            $adminUser['token'] = $aes->adminEncrypt($token);
            // 成功放行登录
            if (!empty($adminUser)) {
                return show(config('code.success'), '登录成功', $adminUser);
                /*$result1['value'] = $adminUser; $result1['status'] = 1; $result1['message'] = '登录成功'; return json($result1);*/
            }
        } else {
            return show(config('code.error'), '登录失败', '', 403);
        }
    }

    /**
     * 生成登录验证码
     * @return \think\response\Json
     */
    public function createverifycode()
    {
        $result['one'] = mt_rand(1, 50);
        $result['two'] = mt_rand(1, 50);
        $result['status'] = 1;
        session('code', $result['one'] + $result['two']);
        return json($result);
    }
}
