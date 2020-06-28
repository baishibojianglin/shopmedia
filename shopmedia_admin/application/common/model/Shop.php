<?php

namespace app\common\model;

use think\Model;

/**
 * 用户（店家）店铺模型类
 * Class Shop
 * @package app\common\model
 */
class Shop extends Base
{
    /**
     * 获取店铺列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getShop($map = [], $size = 5)
    {
        if(!isset($map['s.is_delete'])) {
            $map['s.is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['s.shop_id' => 'desc'];

        $result = $this->alias('s')
            ->field($this->_getListField())
            ->join('__USER__ u', 's.user_id = u.user_id', 'LEFT') // 用户
            ->join('__USER_SHOPKEEPER__ us', 's.shopkeeper_id = us.id', 'LEFT') // 店家
            ->join('__USER_SALESMAN__ usa', 'us.salesman_id = usa.id', 'LEFT') // 店铺业务员
            ->join('__REGION__ rp', 's.province_id = rp.region_id', 'LEFT') // 区域（省级）
            ->join('__REGION__ rc', 's.city_id = rc.region_id', 'LEFT') // 区域（市级）
            ->join('__REGION__ rco', 's.county_id = rco.region_id', 'LEFT') // 区域（区县）
            ->join('__REGION__ rt', 's.town_id = rt.region_id', 'LEFT') // 区域（乡镇街道）
            ->where($map)->order($order)->cache(true, 10)->paginate($size);
        return $result;
    }

    /**
     * 通用化获取参数的数据字段
     * @return array
     */
    private function _getListField()
    {
        return [
            's.*',
            'u.user_name',
            'rp.region_name province',
            'rc.region_name city',
            'rco.region_name county',
            'rt.region_name town'
        ];
    }

    /**
     * 统计店铺数据
     * @param string $field
     * @param array $map
     * @return int|string
     * @throws \think\Exception
     */
    public function shopCount($field = '', $map = [])
    {
        $count = $this->alias('s')
            ->join('__USER_SHOPKEEPER__ us', 's.shopkeeper_id = us.id', 'LEFT') // 店家
            ->join('__USER_SALESMAN__ usa', 'us.salesman_id = usa.id', 'LEFT') // 店铺业务员
            ->where($map)
            ->count($field);
        return $count;
    }
}
