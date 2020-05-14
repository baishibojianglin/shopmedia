<?php

namespace app\common\model;

use think\Model;

/**
 * 店家店铺-广告屏明细模型类
 * Class Device
 * @package app\common\model
 */
class ShopDevice extends Base
{
    /**
     * 获取广告屏列表（不分页，用于 Select 选择器等）
     * @param $map
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getShopDeviceList($map)
    {
        $result = $this->alias('sd')
            ->field('d.device_id, d.brand, d.model, d.size, d.shop_id, s.shop_name, s.province_id, s.city_id, s.county_id, s.town_id, s.address, s.longitude, s.latitude, s.cate shopcate, rp.region_name province, rc.region_name city, rco.region_name county, rt.region_name street, c.company_name, sd.today_income, sd.total_income')
            ->join('__DEVICE__ d', 'sd.device_id = d.device_id', 'LEFT') // 广告屏
            ->join('__SHOP__ s', 'd.shop_id = s.shop_id', 'LEFT') // 店铺
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
}