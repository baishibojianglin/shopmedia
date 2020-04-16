<?php

namespace app\api\controller;

use think\Controller;
use think\Db;

/**
 * api模块用户（广告设备合作者）拥有的设备控制器类
 * Class UserPartnerDevice
 * @package app\api\controller
 */
class UserPartnerDevice extends AuthBase
{
    /**
     * 获取用户（广告设备合作者）拥有的设备列表
     * @return \think\response\Json
     */
    public function index()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            return show(config('code.error'), '请求不合法', '', 400);
        }
        return show(config('code.success'), 'OK', 123);
        // 传入的参数
        $param = input('param.');

        // 查询条件
        $map = [];
        if (isset($param['user_id']) && isset($param['role_id'])) { // 用户ID、用户角色ID
            $map['user_id'] = intval($param['user_id']);
            $map['role_id'] = intval($param['role_id']);
        }
        // 获取广告设备ID集合
        $userPartner = Db::name('user_partner')->field('device_ids')->where(['user_id' => intval($param['user_id']), 'role_id' => intval($param['role_id'])])->find();
        $deviceIdsAndShare = json_decode($userPartner['device_ids'], true);
        $deviceIds = [];
        foreach ($deviceIdsAndShare as $key => $value) {
            $deviceIds[] = $value['device_id'];
        }
        $map['d.device_id'] = ['in', $deviceIds];

        // 获取分页page、size
        $this->getPageAndSize($param);

        // 获取用户（广告设备合作者）拥有的广告设备分页列表数据 模式一：基于paginate()自动化分页
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
}
