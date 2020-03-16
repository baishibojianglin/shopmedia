<?php

namespace app\admin\controller;

use app\common\lib\Aes;

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
     */
    public function login()
    {
        $value = input();

        // 解密类实例化
        $aes = new Aes();
        // 解密sign
        $sign = $aes->decrypt($value['sign']);
        if ($sign != 'jl_goodshop') {
            $result['status'] = 0;
            $result['message'] = '验签不正确';
            return json($result);
        }
        // 解密账号和密码
        $str = $aes->decrypt($value['str']);
        // 字符串分解成变量
        parse_str($str, $data);
        // 账号是否为空
        if (empty($data['account'])) {
          $result['status'] = 0;
          $result['message'] = '请填写账号';
          return json($result);
        }
        // 密码是否为空
        if (empty($data['password'])) {
          $result['status'] = 0;
          $result['message'] = '请填写密码';
          return json($result);
        }
        // 验证码是否为空
        if (empty($data['verifycode'])) {
          $result['status'] = 0;
          $result['message'] = '请填写验证码';
          return json($result);
        }
        // 验证码是否正确
        if ($data['verifycode'] != session('code')) {
          $result['status'] = 0;
          $result['message'] = '验证码不正确';
          return json($result);
        }
        // 密码md5加密
        $data['password'] = md5($data['password']);
        // 查询数据表模型类
        $list = model('CompanyUser')->checklogin($data['account'], $data['password']);
        if (empty($list)) {
          $result['status'] = 0;
          $result['message'] = '账号或密码不正确';
          return json($result);
        }
        // 通过验证,生成token
        $token = md5(uniqid(mt_rand(), true)) . mt_rand();
        // 将token存入供应商账户表
        $lsittoken = model('CompanyUser')->savetoken($list['user_id'], $token);
        // 用aes加密token
        $token = $aes->encrypt($token);
        $list['token'] = $token;
        // 成功放行登录
        if (!empty($lsittoken)) {
          $result['value'] = $list;
          $result['status'] = 1;
          $result['message'] = '登录成功';
          return json($result);
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
