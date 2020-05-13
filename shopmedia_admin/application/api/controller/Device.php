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
class Device extends AuthBase
{
    /**
     * 获取广告屏位置列表信息
     * @return \think\response\Json
     */
    public function getMarkers(){
        // 查询条件
        $map = [];
        // 广告屏列表
        try{
            $devicelist = Db::name('device')->alias('d')
                ->field('d.*, po.order_status')
                ->join('__PARTNER_ORDER__ po', 'd.device_id = po.device_id and po.order_status = 2', 'LEFT') // 若广告屏已经生成订单，取订单被取消对应的广告屏
                ->where($map)
                ->group('d.device_id')
                ->select();
        } catch (\Exception $e) {
            return show(config('code.error'), '请求异常', $e->getMessage(), 500);
        }
        
        if(!empty($devicelist)){
            $message['data'] = $devicelist;
            $message['status'] = 1;
            $message['words'] = '获取成功';
        }else{
            $message['status'] = 0;
            $message['words'] = '获取失败';
        }
        return json($message);
    }

    /**
     * 获取广告屏详细信息
     * @return \think\response\Json
     */
    public function DeviceDetail(){
        $form = input();
        $match['device_id'] = $form['device_id'];
        $devicelist = Db::name('device')->where($match)->find();
        
        if(!empty($devicelist)){
            $message['data'] = $devicelist;
            $message['status'] = 1;
            $message['words'] = '获取成功';
        }else{
            $message['status'] = 0;
            $message['words'] = '获取失败';
        }
        return json($message);
    }
}
