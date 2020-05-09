<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Request;

/**
 * admin模块广告管理控制器类
 * Class Ad
 * @package app\admin\controller
 */
class Ad extends Base
{
    /**
     * 显示广告资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的参数
            $param = input('param.');
            if (isset($param['size'])) { // 每页条数
                $param['size'] = intval($param['size']);
            }

            // 查询条件
            $map = [];
            if (!empty($param['ad_name'])) { // 广告名称
                $map['a.ad_name'] = ['like', '%' . trim($param['ad_name']) . '%'];
            }
            if (isset($param['is_delete'])) { // 是否删除
                $map['a.is_delete'] = $param['is_delete'];
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取广告列表数据
            try {
                $data = model('Ad')->getAd($map, $this->size);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', '', 500); // $e->getMessage()
            }

            if ($data) {
                // 处理数据
                $auditStatus = config('code.audit_status'); // 审核状态：0待审核，1通过，2驳回
                $adCate = config('code.ad_cate'); // 广告类别
                $shopCate = config('code.shop_cate'); // 店铺类别
                foreach ($data as $key => $value) {
                    $data[$key]['audit_status_msg'] = $auditStatus[$value['audit_status']]; // 定义审核状态信息
                    $data[$key]['ad_cate_name'] = $adCate[$value['ad_cate_id']]; // 定义广告类别名称

                    // 定义店铺类别名称集合
                    $shopCateNames = [];
                    $shopCateIds = explode(',', $value['shop_cate_ids']);
                    foreach ($shopCate as $k => $v) {
                        foreach ($shopCateIds as $k1 => $v1) {
                            if ($k == $v1) {
                                $shopCateNames[] = $shopCate[$v1];
                            }
                        }
                    }
                    $data[$key]['shop_cate_names'] = implode('、', $shopCateNames);

                    $data[$key]['start_datetime'] = $value['start_datetime'] ? date('Y-m-d H:i:s', $value['start_datetime']) : ''; // 投放时间
                    $data[$key]['end_datetime'] = $value['end_datetime'] ? date('Y-m-d H:i:s', $value['end_datetime']) : ''; // 结束时间
                    $data[$key]['audit_time'] = $value['audit_time'] ? date('Y-m-d H:i:s', $value['audit_time']) : ''; // 审核时间
                }

                return show(config('code.success'), 'OK', $data);
            } else {
                return show(config('code.error'), 'Not Found', $data, 404);
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
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
            // 传入的参数
            $data = input('post.');
            // 处理数据
            if (isset($data['ad_datetime']) && !empty($data['ad_datetime'])) {
                $data['start_datetime'] = strtotime($data['ad_datetime'][0]); // 投放开始时间
                $data['end_datetime'] = strtotime($data['ad_datetime'][1]); // 投放结束时间
            }
            if (isset($data['ad_time']) && !empty($data['ad_time'])) {
                $data['start_time'] = date('H:i:s', strtotime($data['ad_time'][0])); // 每日播放时间段（开始时间）
                $data['end_time'] = date('H:i:s', strtotime($data['ad_time'][1])); // 每日播放时间段（结束时间）
            }
            if (!empty($data['shop_cate_ids'])) { // 投放店铺类别ID集合
                $data['shop_cate_ids'] = implode(',', $data['shop_cate_ids']);
            }
            if (!empty($data['region_ids'])) { // 投放区域ID集合（含全选与半选）
                $data['region_ids'] = json_encode([
                    'checked' => $data['region_ids'][0], // 全选
                    'half' => $data['region_ids'][1] // 半选
                ]);
            }
            if (!empty($data['device_ids'])) { // 投放广告屏ID集合
                $data['device_ids'] = implode(',', $data['device_ids']);
            }

            // validate验证数据合法性
            /*$validate = validate('');
            if (!$validate->check($data)) {
                return show(config('code.error'), $validate->getError(), '', 403);
            }*/

            // 入库操作
            try {
                $id = model('Ad')->add($data, 'ad_id');
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试'.$e->getMessage(), '', 500); // $e->getMessage()
            }
            if ($id) {
                return show(config('code.success'), '新增成功', '', 201);
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

    /**
     * 保存更新的广告资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        // 判断为PUT请求
        if (request()->isPut()) {
            // 传入的数据
            $param = input('param.');

            // validate验证数据合法性：判断是审核状态还是更新其他数据
            /*$validate = validate('');
            $rules = [];
            $scene = 'update';
            if (isset($param['audit_status'])) { // 审核操作
                $rules = ['audit_status' => 'require'];
                $scene = [];
            }
            if (!$validate->check($param, $rules, $scene)) {
                return show(config('code.error'), $validate->getError(), '', 403);
            }*/

            // 判断数据是否存在
            $data = [];
            // 当为还原软删除的数据时
            if (isset($param['is_delete']) && $param['is_delete'] == config('code.is_delete')) {
                $data['is_delete'] = config('code.not_delete');
            }
            if (!empty($param['ad_name'])) { // 广告名称
                $data['ad_name'] = trim($param['ad_name']);
            }
            if (!empty($param['ad_cate_id'])) { // 广告类别ID
                $data['ad_cate_id'] = intval($param['ad_cate_id']);
            }
            if (isset($param['ad_datetime']) && !empty($param['ad_datetime'])) {
                $data['start_datetime'] = strtotime($param['ad_datetime'][0]); // 投放开始时间
                $data['end_datetime'] = strtotime($param['ad_datetime'][1]); // 投放结束时间
            }
            if (isset($param['ad_time']) && !empty($param['ad_time'])) {
                $data['start_time'] = date('H:i:s', strtotime($param['ad_time'][0])); // 每日播放时间段（开始时间）
                $data['end_time'] = date('H:i:s', strtotime($param['ad_time'][1])); // 每日播放时间段（结束时间）
            }
            if (isset($param['play_times'])) { // 每日播放次数
                $data['play_times'] = input('param.play_times', null, 'intval');
            }
            if (isset($param['ad_price'])) { // 广告价格
                $data['ad_price'] = input('param.ad_price', null, 'float');
            }
            if (isset($param['discount_ratio'])) { // 广告折扣率
                $data['discount_ratio'] = input('param.discount_ratio/f');
            }
            if (!empty($param['advertiser'])) { // 广告主名称
                $data['advertiser'] = trim($param['advertiser']);
            }
            if (!empty($param['phone'])) { // 广告主电话
                $data['phone'] = trim($param['phone']);
            }
            if (!empty($param['shop_cate_ids'])) { // 投放店铺类别ID集合
                $data['shop_cate_ids'] = implode(',', $param['shop_cate_ids']);
            }
            if (!empty($param['region_ids'])) { // 投放区域ID集合（含全选与半选）
                $data['region_ids'] = json_encode([
                    'checked' => $param['region_ids'][0], // 全选
                    'half' => $param['region_ids'][1] // 半选
                ]);
            }
            if (!empty($param['device_ids'])) { // 投放广告屏ID集合
                $data['device_ids'] = implode(',', $param['device_ids']);
            }
            if (isset($param['audit_status'])) { // 审核状态
                $data['audit_status'] = input('param.audit_status', null, 'intval');
                $data['audit_id'] = $this->adminUser['id'];
                $data['audit_time'] = time();
            }
            if (isset($param['is_show'])) { // 是否显示
                $data['is_show'] = input('param.is_show', null, 'intval');
            }
            if (isset($param['sort'])) { // 排序
                $data['sort'] = input('param.sort', null, 'intval');
            }

            if (empty($data)) {
                return show(config('code.error'), '数据不合法', '', 404);
            }

            // 更新
            try {
                $result = model('Ad')->save($data, ['ad_id' => $id]); // 更新
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', '', 500); // $e->getMessage()
            }
            if (false === $result) {
                return show(config('code.error'), '更新失败', '', 403);
            } else {
                return show(config('code.success'), '更新成功', $data, 201);
            }
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 删除指定广告资源
     *
     * @param  int $id
     * @return \think\Response
     * @throws ApiException
     */
    public function delete($id)
    {
        // 判断为DELETE请求
        if (request()->isDelete()) {
            // 获取指定的广告
            try {
                $data = model('Ad')->find($id);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', '', 500); // $e->getMessage()
            }

            // 判断数据是否存在
            if ($data['ad_id'] != $id) {
                return show(config('code.error'), '数据不存在', '', 404);
            }

            // 判断删除条件：判断广告审核状态
            if ($data['audit_status'] == config('code.status_enable')) { // 审核通过
                return show(config('code.error'), '删除失败：广告已审核通过', '', 403);
            }

            // 软删除
            if ($data['is_delete'] != config('code.is_delete')) {
                // 捕获异常
                try {
                    $result = model('Ad')->softDelete('ad_id', $id);
                } catch (\Exception $e) {
                    throw new ApiException($e->getMessage(), 500, config('code.error'));
                }

                if (!$result) {
                    return show(config('code.error'), '移除失败', '', 403);
                } else {
                    return show(config('code.success'), '移除成功', '');
                }
            }

            // 真删除
            try { // 捕获异常
                $result = model('Ad')->destroy($id);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', [], 500); // $e->getMessage()
            }
            if (!$result) {
                return show(config('code.error'), '删除失败', [], 403);
            } else {
                return show(config('code.success'), '删除成功');
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }
}
