<?php

namespace app\common\model;

use think\Model;

/**
 * 活动模型类
 * Class Activity
 * @package app\common\model
 */
class Activity extends Base
{
    // 数据库配置
    protected $connection = [
        // 时间字段取出后的默认时间格式
        'datetime_format' => false
    ];

    /**
     * 获取活动列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getActivity($map = [], $size = 5)
    {
        if(!isset($map['is_delete'])) {
            $map['is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['act_id' => 'desc'];

        $result = $this->field('description', true) // 字段排除
            ->where($map)
            ->order($order)
            ->paginate($size);
        return $result;
    }

    /**
     * 根据条件获取活动列表数据
     * @param array $map
     * @param int $from
     * @param int $size
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getActivityByCondition($map = [], $from = 0, $size = 5)
    {
        if(!isset($map['is_delete'])) {
            $map['is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['act_id' => 'desc'];

        $result = $this->field('description', true)
            ->where($map)
            ->limit($from, $size)
            ->order($order)
            ->select();
        return $result;
    }

    /**
     * 根据条件获取活动列表数据的总数
     * @param array $map
     * @return int|string
     * @throws \think\Exception
     */
    public function getActivityCountByCondition($map = [])
    {
        if(!isset($map['is_delete'])) {
            $map['is_delete'] = ['neq', config('code.is_delete')];
        }

        $count = $this->where($map)->count();
        return $count;
    }
}
