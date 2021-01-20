<?php

namespace app\common\model;

use think\Model;

/**
 * 活动奖品模型类
 * Class ActPrize
 * @package app\common\model
 */
class ActPrize extends Base
{
    /**
     * 获取活动奖品列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getActPrize($map = [], $size = 5)
    {
        $order = ['ap.prize_id' => 'desc'];

        $result = $this->alias('ap')
            ->field('ap.*, a.act_name')
            ->join('__ACTIVITY__ a', 'ap.act_id = a.act_id', 'LEFT')
            ->where($map)
            ->order($order)
            ->paginate($size);
        return $result;
    }
}
