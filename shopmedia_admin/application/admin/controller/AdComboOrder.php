<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Request;

/**
 * admin模块广告套餐订单控制器类
 * Class AdComboOrder
 * @package app\admin\controller
 */
class AdComboOrder extends Base
{
    /**
     * 显示广告套餐订单资源列表
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

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = model('AdComboOrder')->getAdComboOrder($map, (int)$this->size);
            if (!$data) {
                return show(config('code.error'), 'Not Found', '', 404);
            }

            // 处理数据
            $orderStatus = config('code.order_status'); // 订单状态
            $payStatus = config('code.pay_status'); // 支付状态
            foreach ($data as $key => $value) {
                $data[$key]['order_status_msg'] = $orderStatus[$value['order_status']]; // 定义订单状态信息
                $data[$key]['order_time'] = $value['order_time'] ? date('Y-m-d H:i:s', $value['order_time']) : ''; // 下单时间
                $data[$key]['pay_status_msg'] = $payStatus[$value['pay_status']]; // 定义支付状态信息
                $data[$key]['pay_time'] = $value['pay_time'] ? date('Y-m-d H:i:s', $value['pay_time']) : ''; // 支付时间
            }

            return show(config('code.success'), 'OK', $data);
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }
}