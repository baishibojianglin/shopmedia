<?php

namespace app\api\controller;

use think\Controller;
use think\Request;
use think\Db;

/**
 * api模块客户端广告控制器类
 * Class Device
 * @package app\api\controller
 */
class Ad extends AuthBase
{

      /**
     * 获取广告屏数量
     * @return \think\response\Json
     */
    public function getDeviceNumber()
    {

         $match['status']=1;
         $devicenumber=Db::name('device')->where($match)->count();
         if(!empty($devicenumber)){
            return json($devicenumber);
         }
             
    }  


}
