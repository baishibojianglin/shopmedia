<?php

namespace app\api\controller;

use think\Controller;

/**
 * api模块客户端发送短信控制器类
 * Class Device
 * @package app\admin\controller
 */
class SendSms extends Controller
{
    /**
     * 调用阿里云短信接口，获取短信验证码
     * @return \think\response\Json
     */
    public function sendSms(){
        $value = input();
        $verifyCode = mt_rand(1000,9999);
        sendSms($value['phone'], $verifyCode);
        return json($verifyCode);
    }
}
