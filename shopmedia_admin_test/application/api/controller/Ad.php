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
     * 查询广告
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function getAd()
    {
       $list = Db::name('ad')->field('ad_name,ad_cate_id')->where(['audit_status' => 1])->select();
       return json($list);
    }

    /**
     * 广告类别
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function getAdCate()
    {
        $cate = config('ad.ad_cate');
        return json($cate);
    }


    /**
     * 保存新建的广告资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        // 判断为POST请求
        if(request()->isPost()){
            //return show(config('code.success'), '新增成功', ['ad_id' => 18], 201); // TODO：用于支付测试：不会创建广告投放数据，直接跳转到支付页面做支付测试，若页面提示“参数错误”，修改ad_id对应订单号。
            // 传入的参数
            $data = input('post.');
            // 处理数据
            if (isset($data['startdate']) && !empty($data['startdate'])) {
                $data['start_datetime'] = strtotime($data['startdate']); // 投放开始时间
                $data['end_datetime'] = $data['start_datetime'] + $data['play_days'] * 24 * 3600; // 投放结束时间
            }
            if (!empty($data['ad_cate_id'])) {
                // 广告（所属行业）类别ID
                $data['ad_cate_id'] = (int)$data['ad_cate_id'];

                // 投放店铺（所属行业）类别ID集合
                $adCate = config('ad.ad_cate');
                $data['shop_cate_ids'] = [];
                foreach ($adCate as $key => $value) {
                    if ($key != $data['ad_cate_id']) {
                        array_push($data['shop_cate_ids'], $key);
                    }
                }
                $data['shop_cate_ids'] = implode(',', $data['shop_cate_ids']);
            }
            if (!empty($data['region_ids'])) { // 投放区域ID集合（含全选与半选）
                // 通过半选区域ID集合获取上级区域ID集合，重新组装半选区域ID集合
                $regionIdsHalf = $data['region_ids'][1];
                $parentIds = [];
                foreach ($regionIdsHalf as $key => $value) {
                    if ($value != 0) {
                        $parentIds[] = model('Region')->getParentId($value);
                    }
                }
                $parentIds = array_unique(array_reduce($parentIds, 'array_merge', array())); // 二维数组转一维数组并去重
                sort($parentIds); // 正序重排索引

                $data['region_ids'] = json_encode([
                    'checked' => $data['region_ids'][0], // 全选
                    'half' => $parentIds // 半选
                ]);
            }
            if (!empty($data['distance']) && !empty($data['longitude']) && !empty($data['latitude'])) { // 广告投放定位距离、经纬度
                $data['distance'] = (float)$data['distance'];
                $data['longitude'] = (float)$data['longitude'];
                $data['latitude'] = (float)$data['latitude'];
            }
            if (!empty($data['device_ids'])) { // 投放广告屏ID集合
                $data['device_ids'] = implode(',', $data['device_ids']);
            }

            // 广告主信息
            $advertiser = Db::name('user_advertiser')->field('id')->where(['user_id' => $this->user['user_id']])->find();
            $data['advertiser_id'] = $advertiser['id'];
            $data['advertiser'] = $this->user['user_name'];
            $data['phone'] = $this->user['phone'];

            // 订单编号与下单时间
            $data['order_sn'] = model('Ad')->getOrderSn('msad'); // 生成唯一订单编号 order_sn
            $data['order_time'] = time();

            // validate验证数据合法性
            /*$validate = validate('');
            if (!$validate->check($data)) {
                return show(config('code.error'), $validate->getError(), '', 403);
            }*/

            // 入库操作
            try {
                $id = model('Ad')->add($data, 'ad_id');
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', '', 500); // $e->getMessage()
            }
            if ($id) {
                return show(config('code.success'), '新增成功', ['ad_id' => $id], 201);
            } else {
                return show(config('code.error'), '新增失败', '', 403);
            }
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 显示指定的广告资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            try {
                $data = model('Ad')->find($id);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', '', 500); // $e->getMessage()
            }

            if ($data) {
                // 处理数据
                $data['ad_datetime'] = [ // 定义投放时间数组
                    date('c', $data['start_datetime']), // UNIX 转换成 UTC
                    date('c', $data['end_datetime'])
                ];
                $data['ad_time'] = [ // 定义播放时间段数组
                    date('c', strtotime($data['start_time'])),
                    date('c', strtotime($data['end_time']))
                ];
                $data['shop_cate_ids'] = explode(',', $data['shop_cate_ids']); // 投放店铺类别ID集合
                $data['device_ids'] = explode(',', $data['device_ids']); // 投放广告屏ID集合

                return show(config('code.success'), 'ok', $data);
            } else {
                return show(config('code.error'), 'Not Found', $data, 404);
            }
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }
}
