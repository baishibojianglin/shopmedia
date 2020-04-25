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
        $result = $this->alias('o')
            ->field($this->_getListField())
            ->join('__USER__ u', 'o.user_id = u.user_id', 'LEFT') // 用户
            ->join('__DEVICE__ d', 'o.device_id = d.device_id', 'LEFT') // 广告屏
            ->where($map)->cache(true, 10)->paginate($size);
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
            'd.shopname'
        ];
    }
}
