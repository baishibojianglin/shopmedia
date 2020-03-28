<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Db;
use think\Request;

/**
 * admin模块用户（传媒设备合作者）拥有的设备控制器类
 * Class UserPartnerDevice
 * @package app\admin\controller
 */
class UserPartnerDevice extends Base
{
    /**
     * 获取用户（传媒设备合作者）拥有的设备列表
     * @return \think\response\Json
     */
    public function index()
    {
        // 判断为GET请求
        if (!request()->isGet()) {
            return show(config('code.error'), '请求不合法', '', 400);
        }

        // 传入的参数
        $param = input('param.');
        if (isset($param['size'])) { // 每页条数
            $param['size'] = intval($param['size']);
        }

        // 查询条件
        $map = [];
        if (!empty($param['keywords'])) { // 用户名称
            $map['d.brand|d.model'] = ['like', '%' . $param['keywords'] . '%'];
        }
        // 获取传媒设备ID集合
        $userPartner = Db::name('user_partner')->field('device_ids')->where(['user_id' => $param['user_id']])->find();
        $deviceIdsAndShare = json_decode($userPartner['device_ids'], true);
        $deviceIds = [];
        foreach ($deviceIdsAndShare as $key => $value) {
            $deviceIds[] = $value['device_id'];
        }
        $map['d.device_id'] = ['in', $deviceIds];

        // 获取分页page、size
        $this->getPageAndSize($param);

        // 获取用户（传媒设备合作者）拥有的传媒设备分页列表数据 模式一：基于paginate()自动化分页
        try {
            $data = model('Device')->getDevice($map, $this->size);
        } catch (\Exception $e) {
            return show(config('code.error'), '网络忙，请重试', '', 500); // $e->getMessage()
        }

        // 处理数据
        $status = config('code.status');
        foreach($data as $key1 => $value1) {
            $data[$key1]['status_msg'] = $status[$value1['status']]; // 定义状态信息

            foreach ($deviceIdsAndShare as $key2 => $value2) {
                if ($value1['device_id'] == $value2['device_id']) {
                    $data[$key1]['share'] = $value2['share']; // 定义share
                    //$data[$key1]['agreement'] = $value2['agreement']; // 定义agreement
                }
            }
        }

        return show(config('code.success'), 'OK', $data);
    }

    /**
     * 更新传媒设备合作者拥有的设备所占份额
     * @param Request $request
     * @param $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function update(Request $request, $id)
    {
        // 判断为PUT请求
        if (!request()->isPut()) {
            return show(config('code.error'), '请求不合法', '', 400);
        }

        // 传入的参数
        $param = input('param.');

        // 判断数据是否存在
        $data = [];
        if (isset($param['device_id']) && isset($param['share'])) {
            // 获取用户（传媒设备合作者）信息
            $userPartner = DB::name('user_partner')->where(['user_id' => $id])->find();
            $deviceIdsAndShare = json_decode($userPartner['device_ids'], true);
            foreach ($deviceIdsAndShare as $key => $value) {
                if ($param['device_id'] == $value['device_id']) {
                    $deviceIdsAndShare[$key]['share'] = trim($param['share']);
                }
            }

            $data['device_ids'] = json_encode($deviceIdsAndShare);
        }

        if (empty($data)) {
            return show(config('code.error'), '数据不合法', '', 404);
        }

        try {
            $result = Db::name('user_partner')->where(['user_id' => $id])->update($data);
        } catch(\Exception $e) {
            throw new ApiException('网络忙，请重试', 500, config('code.error')); // $e->getMessage()
        }
        if (false === $result) {
            return show(config('code.error'), '更新失败', '', 403);
        } else {
            return show(config('code.success'), '更新成功', '', 201);
        }
    }
}
