<?php

namespace app\common\model;

use think\Model;

/**
 * 广告屏模型类
 * Class Device
 * @package app\common\model
 */
class Device extends Base
{
    /**
     * 获取广告屏列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getDevice($map = [], $size = 5)
    {
        $result = $this->alias('d')
            ->field($this->_getListField())
            ->join('__SHOP__ s', 'd.shop_id = s.shop_id', 'LEFT') // 店铺
            ->join('__REGION__ rp', 's.province_id = rp.region_id', 'LEFT') // 区域（省份）
            ->join('__REGION__ rc', 's.city_id = rc.region_id', 'LEFT') // 区域（城市）
            ->join('__REGION__ rco', 's.county_id = rco.region_id', 'LEFT') // 区域（区县）
            ->join('__REGION__ rt', 's.town_id = rt.region_id', 'LEFT') // 区域（街道）
            ->join('__COMPANY__ c', 'd.company_id = c.company_id', 'LEFT') // 分公司
            ->where($map)
            ->cache(true, 10)
            ->paginate($size);
        return $result;
    }

    /**
     * 获取广告屏列表（不分页，用于 Select 选择器等）
     * @param $map
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getDeviceList($map)
    {
        $result = $this->alias('d')
            ->field('d.device_id, d.device_sn, d.brand, d.model, d.size, d.shop_id, s.shop_name, s.province_id, s.city_id, s.county_id, s.town_id, s.address, s.longitude, s.latitude, s.cate shopcate, rp.region_name province, rc.region_name city, rco.region_name county, rt.region_name street, c.company_name, sd.today_income, sd.total_income')
            ->join('__SHOP__ s', 'd.shop_id = s.shop_id', 'LEFT') // 店铺
            ->join('__SHOP_DEVICE__ sd', 'd.device_id = sd.device_id', 'LEFT') // 店家所在店铺安装的广告屏明细
            ->join('__REGION__ rp', 's.province_id = rp.region_id', 'LEFT') // 区域（省份）
            ->join('__REGION__ rc', 's.city_id = rc.region_id', 'LEFT') // 区域（城市）
            ->join('__REGION__ rco', 's.county_id = rco.region_id', 'LEFT') // 区域（区县）
            ->join('__REGION__ rt', 's.town_id = rt.region_id', 'LEFT') // 区域（街道）
            ->join('__COMPANY__ c', 'd.company_id = c.company_id', 'LEFT') // 分公司
            ->where($map)
            ->cache(true, 10)
            ->select();
        return $result;
    }

    /**
     * 根据条件获取广告屏列表数据
     * @param array $map
     * @param int $from
     * @param int $size
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getDeviceByCondition($map = [], $from = 0, $size = 5)
    {
        if(!isset($map['d.is_delete'])) {
            $map['d.is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['d.device_id' => 'desc'];

        $result = $this->alias('d')
            ->field($this->_getListField())
            ->join('__SHOP__ s', 'd.shop_id = s.shop_id', 'LEFT') // 店铺
            ->join('__REGION__ rp', 's.province_id = rp.region_id', 'LEFT') // 区域（省份）
            ->join('__REGION__ rc', 's.city_id = rc.region_id', 'LEFT') // 区域（城市）
            ->join('__REGION__ rco', 's.county_id = rco.region_id', 'LEFT') // 区域（区县）
            ->join('__REGION__ rt', 's.town_id = rt.region_id', 'LEFT') // 区域（街道）
            ->join('__COMPANY__ c', 'd.company_id = c.company_id', 'LEFT') // 分公司
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
            'd.device_id', 'd.device_sn', 'd.device_cate', 'd.device_quantity', 'd.brand', 'd.model', 'd.size', 'd.shop_id', 'd.url_image', 'd.sale_price', 'd.saled_part', 'd.company_id', 'd.status', 'd.level',
            's.shop_name', 's.cate', 's.province_id', 's.city_id', 's.county_id', 's.town_id', 's.address', 's.longitude', 's.latitude',
            'rp.region_name province',
            'rc.region_name city',
            'rco.region_name county',
            'rt.region_name street',
            'c.company_name'
        ];
    }
}