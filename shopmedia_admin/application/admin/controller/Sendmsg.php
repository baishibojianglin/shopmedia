<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Request;
use think\Db;

/**
 * admin模块媒体设备管理控制器类
 * Class Device
 * @package app\admin\controller
 */
class Sendmsg extends Base
{



    /**
     * 短信接口
     */

    public function sendmsg(){
        $value = input();
        sendmessage($value['phone']);
    }




}
