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
        $order = ['prize_id' => 'desc'];

        $result = $this->field(true)
            ->where($map)
            ->order($order)
            ->paginate($size);
        return $result;
    }
}
