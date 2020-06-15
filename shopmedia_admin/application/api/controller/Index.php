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
     * 获取整体广告屏数量
     * @return \think\response\Json
     */
    public function getTotalData()
    {
             $match['status']=1;
             $data['device']=Db::name('shop')->where($match)->count()+100;
             $data['city']=Db::name('company')->where($match)->count()+2;
             $data['shop']=Db::name('ad')->count()+20;
             return json($data);

    }

    /**
     * 获取城市
     * @return \think\response\Json
     */
    public function getCity()
    {
        $city = config('code.fix_city'); // 广告类别
        return json($city);

    }



}
