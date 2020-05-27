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
class Index extends AuthBase
{
    /**
     * 获取整体广告屏数量、城市、店家数据
     * @return \think\response\Json
     */
    public function getTotalData()
    {
             $match['status']=1;
             $data['device']=Db::name('shop')->where($match)->count()+10000;
             $data['city']=Db::name('company')->where($match)->count()+2;
             $data['shop']=Db::name('ad')->count()+5000;
             return json($data);

    }


}
