<?php

namespace app\common\model;

use think\Model;

/**
 * 用户反馈模型类
 * Class Feedback
 * @package app\common\model
 */
class Feedback extends Base
{
    /**
     * 获取用户反馈列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getFeedback($map = [], $size = 5)
    {
        /*if(!isset($map['is_delete'])) {
            $map['is_delete'] = ['neq', config('code.is_delete')];
        }*/

        $order = ['fb.create_time' => 'desc'];

        $result = $this->alias('fb')
            ->field('fb.*, u.user_name, u.phone')
            ->join('__USER__ u', 'fb.user_id = u.user_id', 'LEFT')
            ->where($map)
            ->order($order)
            ->paginate($size);
        return $result;
    }
}
