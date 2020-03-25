<?php

namespace app\common\model;

use think\Model;

/**
 * 传媒设备模型类
 * Class Device
 * @package app\common\model
 */
class Device extends Base
{
    /**
     * 获取传媒设备列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getDevice($map = [], $size = 5)
    {
        $result = $this->alias('d')
            ->field('d.device_id, d.brand, d.model, d.size, d.city_id, d.area_id, d.street_id, d.address, d.shopname, d.url_image, d.sale_price, d.saled_part, d.company_id, d.status, ra.region_name county, rs.region_name street, c.company_name, rp.region_name province, rc.region_name city')
            ->join('__REGION__ rc', 'd.city_id = rc.region_id', 'LEFT') // 区域（城市）
            ->join('__REGION__ ra', 'd.area_id = ra.region_id', 'LEFT') // 区域（区县）
            ->join('__REGION__ rs', 'd.street_id = rs.region_id', 'LEFT') // 区域（街道）
            ->join('__COMPANY__ c', 'd.company_id = c.company_id', 'LEFT') // 分公司
            ->join('__REGION__ rp', 'c.province_id = rp.region_id', 'LEFT') // 区域（省份）
            ->where($map)
            ->paginate($size);
        return $result;
    }
}