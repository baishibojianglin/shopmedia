<?php

namespace app\api\controller;

use app\common\lib\Aes;
use app\common\lib\exception\ApiException;
use app\common\lib\IAuth;
use think\Controller;
use think\Request;
use think\Db;

/**
 * api模块客户端广告屏控制器类
 * Class Device
 * @package app\api\controller
 */
class Ad extends AuthBase
{
    /**
     * 获取整体广告屏数量、城市、店家数据
     * @return \think\response\Json
     */
    public function getzonelist()
    {
             $form=input();
             $match['parent_id']=$form['parent_id'];
             $zonelist=Db::name('region')->where($match)->select();
             if(!empty($zonelist)){
                return json($zonelist);
             }
             
    }

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
