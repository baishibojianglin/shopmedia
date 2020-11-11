<?php

namespace app\common\model;

use think\Model;

/**
 * 广告套餐订单模型类
 * Class AdComboOrder
 * @package app\common\model
 */
class AdComboOrder extends Base
{
    /**
     * 获取广告订单套餐列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getAdComboOrder($map = [], $size = 5)
    {
        $result = $this->alias('o')
            ->field('o.order_id, o.order_sn, o.user_id, o.advertiser_address, o.salesman_id, o.ad_name, o.combo_id, o.device_quantity, o.ad_days, o.combo_price, o.order_price, o.order_status, o.order_time, o.pay_status, o.pay_time, u.user_name, u.phone, us.uid, su.phone salesman_phone')
            ->join('__USER__ u', 'u.user_id = o.user_id', 'LEFT') // 广告主所属用户
            ->join('__USER_SALESMAN__ us', 'us.id = o.salesman_id', 'LEFT') // 广告主业务员
            ->join('__USER__ su', 'su.user_id = us.uid', 'LEFT') // 广告主业务员所属用户
            ->where($map)
            ->cache(true, 10)
            ->paginate($size);
        return $result;
    }
}
