<?php

namespace app\common\model;

use think\Model;

/**
 * 用户（广告屏合作商）订单模型类
 * Class PartnerOrder
 * @package app\common\model
 */
class PartnerOrder extends Base
{
    /**
     * 获取订单列表数据
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getPartnerOrder($map = [], $size = 5)
    {
        $order = ['o.order_time' => 'desc'];

        $result = $this->alias('o')
            ->field($this->_getListField())
            ->join('__USER__ u', 'o.user_id = u.user_id', 'LEFT') // 用户
            ->join('__USER_PARTNER__ up', 'o.partner_id = up.id', 'LEFT') // 广告屏合作商
            ->join('__USER_SALESMAN__ us', 'up.salesman_id = us.id', 'LEFT') // 广告屏合作商业务员
            ->join('__DEVICE__ d', 'o.device_id = d.device_id', 'LEFT') // 广告屏
            ->join('__SHOP__ s', 'd.shop_id = s.shop_id', 'LEFT') // 店铺
            ->where($map)->order($order)->cache(true, 10)->paginate($size);
        return $result;
    }

    /**
     * 获取订单总金额
     * @param $map
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getPartnerOrderPriceSum($map)
    {
        $result = $this->alias('o')
            ->join('__USER__ u', 'o.user_id = u.user_id', 'LEFT') // 用户
            ->join('__USER_PARTNER__ up', 'o.partner_id = up.id', 'LEFT') // 广告屏合作商
            ->join('__DEVICE__ d', 'o.device_id = d.device_id', 'LEFT') // 广告屏
            ->join('__SHOP__ s', 'd.shop_id = s.shop_id', 'LEFT') // 店铺
            ->where($map)->sum('o.order_price');
        return $result;
    }

    /**
     * 通用化获取参数的数据字段
     * @return array
     */
    private function _getListField()
    {
        return [
            'o.*',
            'u.user_name',
            'up.salesman_id',
            's.shop_name',
            's.address',
            's.longitude',
            's.latitude'
        ];
    }
}
