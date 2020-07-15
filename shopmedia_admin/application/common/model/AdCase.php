<?php

namespace app\common\model;

use think\Model;

/**
 * 广告案例模型类
 * Class AdCase
 * @package app\common\model
 */
class AdCase extends Base
{
    /**
     * 获取广告案例列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getAdCase($map = [], $size = 5)
    {
        $result = $this->field(true)->where($map)->cache(true, 10)->paginate($size);
        return $result;
    }
}