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
     * 获取广告屏列表信息
     * @return \think\response\Json
     */
    public function getDeviceList()
    {
        // 传入的参数
        $param = input('param.');

        // 查询条件
        $map = [];
        $map['d.status'] = config('code.status_enable');
        $map['d.is_delete'] = config('code.not_delete');
        // 获取未付款和已付款订单ID集合
        $orderDeviceIds = Db::name('partner_order')->where(['order_status' => ['in', '0,1']])->column('device_id');
        $orderDeviceIds = array_unique($orderDeviceIds); // 去重
        $map['d.device_id'] = ['not in', $orderDeviceIds]; // 可合作的广告屏（包含若广告屏已经生成订单，取订单被取消对应的广告屏）
        if (!empty($param['region_ids'])) { // 投放区域ID集合（只含全选）
            $map['s.province_id|s.city_id|s.county_id|s.town_id'] = ['in', $param['region_ids']];
        }
        if (!empty($param['shop_cate_ids'])) { // 投放店铺类别ID集合
            $map['s.cate'] = $param['shop_cate_ids']; // ['in', $param['shop_cate_ids']]
        }

        // 广告屏列表
        try{
            $devicelist = Db::name('device')->alias('d')
                ->field('d.*, po.order_id, po.order_status, s.shop_name, s.cate shop_cate, s.address')
                ->join('__PARTNER_ORDER__ po', 'd.device_id = po.device_id', 'LEFT') // 广告屏已经生成的订单
                ->join('__SHOP__ s', 'd.shop_id = s.shop_id', 'LEFT') // 店铺
                ->where($map)
                ->select();
        } catch (\Exception $e) {
            return show(config('code.error'), '请求异常', '', 500);
        }
        
        if(!empty($devicelist)){
            return show(config('code.success'), '获取成功', $devicelist);
        }else{
            return show(config('code.error'), '获取失败', [], 404);
        }
    }

    /**
     * 获取广告屏详细信息
     * @return \think\response\Json
     */
    public function deviceDetail()
    {
        // 传入的参数
        $param = input();

        // 查询条件
        $map['d.device_id'] = $param['device_id'];

        // 广告屏详细信息
        try{
            $device = Db::name('device')->alias('d')
                ->field('d.*, s.shop_name, s.cate shop_cate, s.shop_area, s.province_id, s.city_id, s.county_id, s.town_id, s.address, s.longitude, s.latitude, s.shop_pic, s.environment')
                ->join('__SHOP__ s', 'd.shop_id = s.shop_id', 'LEFT') // 店铺
                ->where($map)
                ->find();
        } catch (\Exception $e) {
            return show(config('code.error'), '请求异常', '', 500);
        }

        if(!empty($device)){
            $shopCate = config('code.shop_cate');
            $shopEnvironment = config('code.shop_environment');
            $device['shop_cate_name'] = $device['shop_cate'] ? $shopCate[$device['shop_cate']] : '';
            $device['environment'] = $device['environment'] ? $shopEnvironment[$device['environment']] : '';
            $message['data'] = $device;
            $message['status'] = 1;
            $message['words'] = '获取成功';
        }else{
            $message['status'] = 0;
            $message['words'] = '获取失败';
        }
        return json($message);
    }
}
