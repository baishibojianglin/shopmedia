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
        if (!request()->isGet()) {
            return show(config('code.error'), '请求不合法', '', 400);
        }

        // 传入的参数
        $param = input('param.');

        // 查询条件
        $map = [];
        // 获取广告设备ID集合
        $partnerDevice = Db::name('partner_device')->field('id, device_id, share, today_income, total_income')->where(['user_id' => intval($param['user_id']), 'role_id' => intval($param['role_id'])])->select();
        $deviceIds = [];
        foreach ($partnerDevice as $key => $value) {
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

            foreach ($partnerDevice as $key2 => $value2) {
                if ($value1['device_id'] == $value2['device_id']) {
                    $data[$key1]['share'] = $value2['share']; // 定义share
                    $data[$key1]['today_income'] = $value2['today_income']; // 定义今日收益
                    $data[$key1]['total_income'] = $value2['total_income']; // 定义累计收益
                }
            }
        }

        return show(config('code.success'), 'OK', $data);
    }
}
