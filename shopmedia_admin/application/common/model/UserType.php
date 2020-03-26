<?php

namespace app\common\model;

use think\Model;

/**
 * 用户类型模型类
 * Class UserType
 * @package app\common\model
 */
class UserType extends Base
{
    /**
     * 获取用户类型列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getUserType($map = [], $size = 5)
    {
        $result = $this->where($map)->paginate($size);
        return $result;
    }
}
