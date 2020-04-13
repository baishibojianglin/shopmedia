<?php

namespace app\common\model;

use think\Model;

/**
 * 广告模型类
 * Class Ad
 * @package app\common\model
 */
class Ad extends Base
{
    /**
     * 获取广告列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getAd($map = [], $size = 5)
    {
        if(!isset($map['a.is_delete'])) { // 是否删除
            $map['a.is_delete'] = ['neq', config('code.is_delete')];
        }

        $result = $this->alias('a')
            ->field('a.ad_id, a.ad_cate_id, a.ad_name, a.ad_pic, a.start_datetime, a.end_datetime, a.start_time, a.end_time, a.play_times, a.ad_price, a.advertisers, a.phone, a.shop_cate_ids, a.audit_id, a.audit_status, a.audit_time, a.is_show, a.sort, a.is_delete')
            ->where($map)
            ->cache(true, 10)
            ->paginate($size);
        return $result;
    }
}