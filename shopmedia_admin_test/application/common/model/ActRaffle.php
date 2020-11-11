<?php

namespace app\common\model;

use think\Model;

/**
 * 活动中奖纪录模型类
 * Class ActRaffle
 * @package app\common\model
 */
class ActRaffle extends Base
{
    /**
     * 获取活动中奖纪录列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getActRaffle($map = [], $size = 5)
    {
        $order = ['ar.raffle_id' => 'desc'];

        $result = $this->alias('ar')
            ->field('ar.*, a.act_name, ap.sponsor, ap.phone sponsor_phone, ap.address sponsor_address, ap.is_sponsor_address, s.shop_name, s.address shop_address')
            ->join('__ACTIVITY__ a', 'ar.act_id = a.act_id', 'LEFT')
            ->join('__ACT_PRIZE__ ap', 'ar.prize_id = ap.prize_id', 'LEFT')
            ->join('__DEVICE__ d', 'ar.device_id = d.device_id', 'LEFT')
            ->join('__SHOP__ s', 'd.shop_id = s.shop_id', 'LEFT')
            ->where($map)
            ->order($order)
            ->paginate($size);
        return $result;
    }
}
