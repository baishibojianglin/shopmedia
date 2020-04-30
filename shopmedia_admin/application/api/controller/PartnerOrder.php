<?php

namespace app\api\controller;

use think\Controller;
use think\Db;
use think\Model;
use think\Request;

/**
 * api模块客户端用户（广告屏合作商）订单控制器类
 * Class PartnerOrder
 * @package app\api\controller
 */
class PartnerOrder extends AuthBase
{
    /**
     * 保存新建的订单资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        // 判断为POST请求
        if(request()->isPost()){
            $data = input('post.');

            $data['order_sn'] = model('PartnerOrder')->getOrderSn(); // 生成唯一订单编号 order_sn
            $data['order_time'] = time();
            //$data['order_status'] = 0;
            $data['order_price'] = $data['device_price'];

            // 获取广告屏合作商信息
            $partnerMap = []; // 查询条件
            if (isset($data['user_id']) && isset($data['role_id']) && intval($data['role_id']) == 2) {
                $partnerMap['user_id'] = intval($data['user_id']);
                $partnerMap['role_id'] = intval($data['role_id']);
                $partner = Db::name('user_partner')->field('id')->where($partnerMap)->find();
                $data['partner_id'] = $partner['id'];
            }

            // validate验证数据合法性
            $validate = validate('PartnerOrder');
            if (!$validate->check($data)) {
                return show(config('code.error'), $validate->getError(), '', 403);
            }

            // 入库操作
            try {
                $id = Db::name('partner_order')->strict(false)->insertGetId($data);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', '', 500); // $e->getMessage()
            }
            if ($id) {
                return show(config('code.success'), '下单成功', $data['order_sn'], 201);
            } else {
                return show(config('code.error'), '下单失败', '', 403);
            }
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 显示指定的订单资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的参数
            $param = input('param.');

            // 查询条件
            $map = [];
            if (
                isset($param['partner_id']) && $param['partner_id'] != 0
                && isset($param['device_id']) && $param['device_id'] != 0
            ) {
                $map['partner_id'] = intval($param['partner_id']);
                $map['device_id'] = intval($param['device_id']);
            }

            try {
                if (!empty($id)) {
                    $data = Db::name('partner_order')->find($id);
                } elseif ($map) {
                    $data = Db::name('partner_order')->where($map)->find();
                }
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', '', 500); // $e->getMessage()
            }

            if ($data) {
                // 处理数据
                $data['order_time'] = date('Y-m-d H:i:s'); // 下单时间
                return show(config('code.success'), 'ok', $data);
            } else {
                return show(config('code.error'), 'Not Found', $data, 404);
            }
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }
}
