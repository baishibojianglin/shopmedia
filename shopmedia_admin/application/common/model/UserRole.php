<?php

namespace app\common\model;

use think\Model;

/**
 * 用户角色模型类
 * Class UserRole
 * @package app\common\model
 */
class UserRole extends Base
{
    /**
     * 获取用户角色列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getUserRole($map = [], $size = 5)
    {
        $result = $this->alias('ur')
            ->field('ur.*, pur.title parent_title')
            ->join('__USER_ROLE__ pur', 'ur.parent_id = pur.id', 'LEFT')
            ->where($map)
            ->paginate($size);
        return $result;
    }
}
