<?php

namespace app\common\model;

use think\Model;

/**
 * 广告套餐模型类
 * Class AdCombo
 * @package app\common\model
 */
class AdCombo extends Base
{
    /**
     * 获取广告套餐列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getAdCombo($map = [], $size = 5)
    {
        $result = $this->field(true)->where($map)->cache(true, 10)->paginate($size);
        return $result;
    }
}