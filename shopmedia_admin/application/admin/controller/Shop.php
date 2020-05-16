<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Request;

/**
 * admin模块店家店铺控制器类
 * Class Shop
 * @package app\admin\controller
 */
class Shop extends Base
{
    /**
     * 显示店铺资源列表
     * @return \think\response\Json
     */
    public function index()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的参数
            $param = input('param.');

            // 查询条件
            $map = [];
            if (!empty($param['shop_name'])) {
                $map['s.shop_name'] = ['like', '%' . trim($param['shop_name']) . '%'];
            }
            if (isset($param['status']) && trim($param['status']) != null) {
                $map['s.status'] = intval($param['status']);
            }
            if (!empty($param['shopkeeper_id']) && intval($param['role_id']) == 3) { // 店家ID
                $map['s.shopkeeper_id'] = intval($param['shopkeeper_id']);
            }
            if (!empty($param['salesman_id']) && intval($param['role_id']) == 6) { // 店铺业务员ID
                $map['usa.id'] = intval($param['salesman_id']);
            }
            if (isset($param['is_commission']) && trim($param['is_commission']) != null) {
                $map['s.is_commission'] = intval($param['is_commission']);
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = model('Shop')->getShop($map, (int)$this->size);
            if (!$data) {
                return show(config('code.error'), 'Not Found', '', 404);
            }
            $status = config('code.status');
            $isCommission = config('code.is_commission'); // 业务员提成状态
            foreach ($data as $key => $value) {
                // 处理数据
                $data[$key]['status_msg'] = $status[$value['status']]; // 定义status_msg
                $data[$key]['is_commission_msg'] = $isCommission[$value['is_commission']]; // 定义is_commission_msg
            }

            return show(config('code.success'), 'OK', $data);
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 统计店铺数据
     * @return \think\response\Json
     */
    public function getShopCount()
    {
        // 判断为GET请求
        if (!request()->isGet()) {
            return show(config('code.error'), '请求不合法', '', 400);
        }

        // 传入的参数
        $param = input('param.');

        $count = [];
        if (!empty($param['salesman_id']) && intval($param['role_id']) == 6) { // 店铺业务员ID
            // 店铺已启用
            $statusEnableCountMap =  ['s.status' => config('code.status_enable'), 's.is_delete' => config('code.not_delete'), 'usa.id' => intval($param['salesman_id'])];
            $statusEnableCount = model('Shop')->shopCount('s.status', $statusEnableCountMap);

            // 店铺已禁用
            $statusDisableCountMap =  ['s.status' => config('code.status_disable'), 's.is_delete' => config('code.not_delete'), 'usa.id' => intval($param['salesman_id'])];
            $statusDisableCount = model('Shop')->shopCount('s.status', $statusDisableCountMap);

            // 店铺待审核
            $statusPendingCountMap =  ['s.status' => config('code.status_pending'), 's.is_delete' => config('code.not_delete'), 'usa.id' => intval($param['salesman_id'])];
            $statusPendingCount = model('Shop')->shopCount('s.status', $statusPendingCountMap);

            // 店铺被驳回
            $statusRejectCountMap =  ['s.status' => config('code.status_reject'), 's.is_delete' => config('code.not_delete'), 'usa.id' => intval($param['salesman_id'])];
            $statusRejectCount = model('Shop')->shopCount('s.status', $statusRejectCountMap);

            // 店铺业务员已提成
            $isCommissionCountMap =  ['s.is_commission' => 1, 's.is_delete' => config('code.not_delete'), 'usa.id' => intval($param['salesman_id'])];
            $isCommissionCount = model('Shop')->shopCount('s.is_commission', $isCommissionCountMap);

            // 店铺业务员未提成
            $notCommissionCountMap =  ['s.is_commission' => 0, 's.is_delete' => config('code.not_delete'), 'usa.id' => intval($param['salesman_id'])];
            $notCommissionCount = model('Shop')->shopCount('s.is_commission', $notCommissionCountMap);

            $count = [
                'status_enable_count' => $statusEnableCount,
                'status_disable_count' => $statusDisableCount,
                'status_pending_count' => $statusPendingCount,
                'status_reject_count' => $statusRejectCount,
                'is_commission_count' => $isCommissionCount,
                'not_commission_count' => $notCommissionCount
            ];
        }

        return show(config('code.success'), 'OK', $count);
    }

    /**
     * 显示指定的店铺资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            try {
                $data = model('Shop')->alias('s')
                    ->field(['s.*', 'u.user_name', 'rp.region_name province', 'rc.region_name city', 'rco.region_name county', 'rt.region_name town'])
                    ->join('__USER__ u', 's.user_id = u.user_id', 'LEFT') // 用户
                    ->join('__USER_SHOPKEEPER__ us', 's.shopkeeper_id = us.id', 'LEFT') // 店家
                    ->join('__REGION__ rp', 's.province_id = rp.region_id', 'LEFT') // 区域（省级）
                    ->join('__REGION__ rc', 's.city_id = rc.region_id', 'LEFT') // 区域（市级）
                    ->join('__REGION__ rco', 's.county_id = rco.region_id', 'LEFT') // 区域（区县）
                    ->join('__REGION__ rt', 's.town_id = rt.region_id', 'LEFT') // 区域（乡镇街道）
                    ->find($id);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            if ($data) {
                // 处理数据
                // 定义status_msg
                $status = config('code.status');
                $data['status_msg'] = $status[$data['status']];

                return show(config('code.success'), 'ok', $data);
            }
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 保存更新的店铺资源
     * @param Request $request
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function update(Request $request, $id)
    {
        // 判断为PUT请求
        if (request()->isPut()) {
            // 传入的参数
            $param = input('param.');

            // 判断数据是否存在
            $data = [];
            if (!empty($param['shop_name'])) {
                $data['shop_name'] = trim($param['shop_name']);
            }
            if (!empty($param['cate_id'])) {
                $data['cate'] = intval($param['cate_id']);
            }
            if (!empty($param['province_id'])) {
                $data['province_id'] = intval($param['province_id']);
            }
            if (!empty($param['city_id'])) {
                $data['city_id'] = intval($param['city_id']);
            }
            if (!empty($param['county_id'])) {
                $data['county_id'] = intval($param['county_id']);
            }
            if (!empty($param['town_id'])) {
                $data['town_id'] = intval($param['town_id']);
            }
            if (!empty($param['address'])) {
                $data['address'] = trim($param['address']);
            }
            if (!empty($param['longitude'])) {
                $data['longitude'] = floatval($param['longitude']);
            }
            if (!empty($param['latitude'])) {
                $data['latitude'] = floatval($param['latitude']);
            }
            if (!empty($param['shop_area'])) {
                $data['shop_area'] = floatval($param['shop_area']);
            }
            if (isset($param['status'])) { // 不能用 !empty() ，否则 status = 0 时也判断为空
                $data['status'] = input('param.status', null, 'intval');
            }
            if (isset($param['is_commission'])) { // 店铺业务员提成状态
                $data['is_commission'] = input('param.is_commission', null, 'intval');
            }

            if (empty($data)) {
                return show(config('code.error'), '数据不合法', '', 404);
            }

            // 更新
            try {
                $result = model('Shop')->save($data, ['shop_id' => $id]); // 更新
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
            if (false === $result) {
                return show(config('code.error'), '更新失败', '', 403);
            } else {
                return show(config('code.success'), '更新成功', '', 201);
            }
        }else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }
}