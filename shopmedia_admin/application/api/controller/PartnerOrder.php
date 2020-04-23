<?php

namespace app\api\controller;

use think\Controller;
use think\Db;
use think\Model;
use think\Request;

/**
 * api模块客户端广告屏合作商订单控制器类
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

            // 生成唯一订单编号 order_sn
            $data['order_sn'] = $this->_getOrderSn();
            $data['order_time'] = time();

            // validate验证数据合法性
            $validate = validate('PartnerOrder');
            if (!$validate->check($data)) {
                return show(config('code.error'), $validate->getError(), '', 403);
            }

            // 入库操作
            try {
                $id = Db::name('partner_order')->insertGetId($data);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', '', 500); // $e->getMessage()
            }
            if ($id) {
                return show(config('code.success'), '新增成功', ['order_id' => $id], 201);
            } else {
                return show(config('code.error'), '新增失败', '', 403);
            }
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 生成唯一订单编号 order_sn
     * @return string
     */
    private function _getOrderSn()
    {
        // 保证不会有重复订单号存在
        while(true){
            $order_sn = date('YmdHis').rand(1000,9999); // 订单编号
            $order_sn_count = Db::name('partner_order')->where("order_sn = '$order_sn'")->count();
            if($order_sn_count == 0)
                break;
        }
        return $order_sn;
    }
}
