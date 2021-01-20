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
     * 获取所有广告屏列表数据（懒加载）
     * @return \think\response\Json
     */
    public function index()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的参数
            $param = input('param.');

            // 查询条件
            $map = ['d.status' => 1, 'd.is_delete' => config('code.not_delete')];

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 根据传入的广告屏ID加载更多
            if (!empty($param['minId'])) {
                // 获取广告屏最大（倒序时为最小）ID（除 $map['device_id'] 外的其他 $map 条件必须带上）
                $maxId = model('Device')->getMin(['status' => 1, 'is_delete' => config('code.not_delete')], 'device_id');

                // 判断传入的广告屏ID是否为最大（倒序时为最小）ID，即数据是否全部加载
                if ($param['minId'] == $maxId) {
                    return show(config('code.error'), '加载完成', ['maxId' => $maxId], 404);
                }

                // 获取大于（倒序时为小于）传入的广告屏ID的集合
                $map['d.device_id'] = ['lt', $param['minId']];
            }
            // 区域
            if (!empty($param['province_id'])) { // 省级
                $map['s.province_id'] = intval($param['province_id']);
            }
            if (!empty($param['city_id'])) { // 市级
                $map['s.city_id'] = (int)$param['city_id'];
            }
            if (!empty($param['county_id'])) { // 区县
                $map['s.county_id'] = (int)$param['county_id'];
            }
            if (!empty($param['town_id'])) { // 乡镇街道
                $map['s.town_id'] = (int)$param['town_id'];
            }

            // 获取广告屏列表数据 模式二：page当前页，size每页条数，from每页从第几条开始 => 'limit from,size'
            $deviceList = model('Device')->getDeviceByCondition($map, $this->from, $this->size);

            if (!$deviceList) {
                return show(config('code.error'), 'Not Found', '', 404);
            }
            $data = ['data' => $deviceList];
            return show(config('code.success'), 'OK', $data);
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

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

        if (isset($param['role_id']) && intval($param['role_id']) == 7) { // 广告主（注释掉下面说明广告主投放广告的广告屏不一定是已经合作签约的）
            // 获取已付款订单ID集合
            /*$orderDeviceIds = Db::name('partner_order')->where(['order_status' => 1])->column('device_id');
            $orderDeviceIds = array_unique($orderDeviceIds); // 去重
            $map['d.device_id'] = ['in', $orderDeviceIds];*/
        } else {
            // 获取未付款和已付款订单ID集合
            try {
                $orderDeviceIds = Db::name('partner_order')->where(['order_status' => ['in', '0,1']])->column('device_id');
            } catch (\Exception $e) {
                return show(config('code.error'), '请求异常' . $e->getMessage(), [], 500);
            }

            $orderDeviceIds = array_unique($orderDeviceIds); // 去重
            $map['d.device_id'] = ['not in', $orderDeviceIds]; // 可合作的广告屏（包含若广告屏已经生成订单，取订单被取消对应的广告屏）
        }
        if (!empty($param['region_ids'])) { // 投放区域ID集合（只含全选）
            $map['s.province_id|s.city_id|s.county_id|s.town_id'] = ['in', $param['region_ids']];
        }
        if (isset($param['ad_cate_id'])) { // 广告所属行业类别ID
            $map['s.cate'] = ['NEQ', $param['ad_cate_id']]; // 排除该类别
        }
        // 判断是否投放附近区域
        if (isset($param['longitude']) && isset($param['latitude']) && isset($param['distance'])) {
            // 获取全部店铺列表数据
            $shopList = model('Shop')->field('shop_id, longitude, latitude')->where(['status' => config('code.status_enable')])->select();

            // 定义经纬度集合
            $mapLongitude = [];
            $mapLatitude = [];

            foreach ($shopList as $key => $value) {
                // 根据两点的经纬度计算距离，此处用于获取指定距离的经纬度集合
                $shopList[$key]['distance'] = round(distance($param['latitude'], $param['longitude'], $value['latitude'], $value['longitude']), 3);

                // 获取指定距离 $param['distance'] 的经纬度集合
                if ($shopList[$key]['distance'] <= $param['distance']) {
                    $mapLongitude[] = $value['longitude'];
                    $mapLatitude[] = $value['latitude'];
                }
            }
            $map['s.longitude'] = ['in', $mapLongitude]; // 经度集合
            $map['s.latitude'] = ['in', $mapLatitude]; // 纬度集合
        }

        // 广告屏列表
        try{
            $devicelist = Db::name('device')->alias('d')
                ->field('d.*, po.order_id, po.order_status, s.shop_name, s.cate shop_cate, s.address, s.longitude, s.latitude')
                ->join('__PARTNER_ORDER__ po', 'd.device_id = po.device_id', 'LEFT') // 广告屏已经生成的订单
                ->join('__SHOP__ s', 'd.shop_id = s.shop_id', 'LEFT') // 店铺
                ->where($map)
                ->select();
        } catch (\Exception $e) {
            return show(config('code.error'), '请求异常', '', 500);
        }

        if(!empty($devicelist)){
            // 处理数据
            $deviceLevel = config('ad.device_level'); // 广告屏等级（用于计算广告单价）
            foreach ($devicelist as $key => $value) {
                // 定义广告屏投放单价（每条广告每天的价格）
                if ($value['device_cate'] == 1) {
                    $devicelist[$key]['ad_unit_price'] = $deviceLevel[$value['level']];
                }
                // 定义广告框投放单价（每条广告每天的价格）
                if ($value['device_cate'] == 2) {
                    $devicelist[$key]['ad_unit_price'] = $deviceLevel[$value['level']] * 0.5;
                }
            }
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
            $adCate = config('ad.ad_cate');
            $shopEnvironment = config('code.shop_environment');
            $deviceSize = config('ad.device_size');
            $device['shop_cate_name'] = isset($device['shop_cate']) ? $adCate[$device['shop_cate']] : '';
            $device['environment'] = isset($device['environment']) ? $shopEnvironment[$device['environment']] : '';
            $device['size'] = isset($device['size']) ? $deviceSize[$device['size']] : '';
            $message['data'] = $device;
            $message['status'] = 1;
            $message['words'] = '获取成功';
        }else{
            $message['status'] = 0;
            $message['words'] = '获取失败';
        }
        return json($message);
    }


    /**
     * 获取广告屏尺寸
     * @return \think\response\Json
     */
    public function getSize()
    {
        $size = config('ad.device_size');
        return json($size);
    }


    /**
     * 获取广告屏价格
     * @return \think\response\Json
     */
    public function getPrice()
    {
        $price = [1 => '≥1', 2 => '≥1', 3 => '≥1'];
        //$price = config('ad.device_level');
        return json($price);
    }
}
