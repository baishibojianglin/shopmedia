<?php

namespace app\common\model;

use think\Model;

/**
 * 广告框模型类
 * Class AdBox
 * @package app\common\model
 */
class AdBox extends Base
{
    /**
     * 获取广告框列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getAdBox($map = [], $size = 5)
    {
        $result = $this->alias('ab')
            ->field($this->_getListField())
            ->join('__SHOP__ s', 's.shop_id = ab.shop_id', 'LEFT') // 店铺
            ->join('__REGION__ rp', 'rp.region_id = s.province_id', 'LEFT') // 区域（省份）
            ->join('__REGION__ rc', 'rc.region_id = s.city_id', 'LEFT') // 区域（城市）
            ->join('__REGION__ rco', 'rco.region_id = s.county_id', 'LEFT') // 区域（区县）
            ->join('__REGION__ rt', 'rt.region_id = s.town_id', 'LEFT') // 区域（乡镇街道）
            ->join('__COMPANY__ c', 'c.company_id = ab.company_id', 'LEFT') // 分公司
            ->where($map)
            ->cache(true, 10)
            ->paginate($size);
        return $result;
    }

    /**
     * 根据条件获取广告框列表数据
     * @param array $map
     * @param int $from
     * @param int $size
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getDeviceByCondition($map = [], $from = 0, $size = 5)
    {
        if(!isset($map['ab.is_delete'])) {
            $map['ab.is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['ab.ad_box_id' => 'desc'];

        $result = $this->alias('ab')
            ->field($this->_getListField())
            ->join('__SHOP__ s', 's.shop_id = ab.shop_id', 'LEFT') // 店铺
            ->join('__REGION__ rp', 'rp.region_id = s.province_id', 'LEFT') // 区域（省份）
            ->join('__REGION__ rc', 'rc.region_id = s.city_id', 'LEFT') // 区域（城市）
            ->join('__REGION__ rco', 'rco.region_id = s.county_id', 'LEFT') // 区域（区县）
            ->join('__REGION__ rt', 'rt.region_id = s.town_id', 'LEFT') // 区域（乡镇街道）
            ->join('__COMPANY__ c', 'c.company_id = ab.company_id', 'LEFT') // 分公司
            ->where($map)
            ->limit($from, $size)
            ->order($order)
            ->select();
        return $result;
    }

    /**
     * 通用化获取参数的数据字段
     * @return array
     */
    private function _getListField()
    {
        return [
            'ab.ad_box_id', 'ab.width', 'ab.height', 'ab.shop_id', 'ab.	shop_cate', 'ab.company_id', 'ab.pic', 'ab.status', 'ab.create_time',
            's.shop_name', 's.province_id', 's.city_id', 's.county_id', 's.town_id', 's.address', 's.longitude', 's.latitude',
            'rp.region_name province',
            'rc.region_name city',
            'rco.region_name county',
            'rt.region_name street',
            'c.company_name'
        ];
    }
}