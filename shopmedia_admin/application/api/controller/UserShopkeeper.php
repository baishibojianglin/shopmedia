<?php

namespace app\api\controller;

use think\Controller;
use think\Request;

/**
 * api模块客户端用户（店家）控制器类
 * Class UserShopkeeper
 * @package app\api\controller
 */
class UserShopkeeper extends AuthBase
{
    /**
     * 显示店家店铺列表
     * @return \think\response\Json
     */
    public function shopList()
    {
        // 判断为GET请求
        if (!request()->isGet()) {
            return show(config('code.error'), '请求不合法', '', 400);
        }

        // 传入的参数
        $param = input('param.');

        // 查询条件
        $map = [];
        $map['s.status'] = config('code.status_enable');
        if (isset($param['user_id'])) {
            $map['s.user_id'] = intval($param['user_id']);
        }

        // 获取分页page、size
        $this->getPageAndSize($param);

        // 获取用户（店家）拥有的店铺分页列表数据 模式一：基于paginate()自动化分页
        try {
            $data = model('Shop')->getShop($map, $this->size);
        } catch (\Exception $e) {
            return show(config('code.error'), '网络忙，请重试', '', 500); // $e->getMessage()
        }
        if ($data) {
            // 处理数据
            foreach ($data as $key => $value) {
                // 获取店铺对应广告屏列表
                $deviceList = model('Device')->getDeviceList(['d.shop_id' => $value['shop_id']]);
                $data[$key]['device_list'] = $deviceList;
            }
        }

        return show(config('code.success'), 'OK', $data);
    }
}