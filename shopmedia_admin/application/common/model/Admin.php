<?php

namespace app\common\model;

use think\Model;

/**
 * 管理员模型类
 * Class Admin
 * @package app\admin\model
 */
class Admin extends Base
{
    /**
     * 获取管理员列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getAdmin($map = [], $size = 5)
    {
        if(!isset($map['a.is_delete'])) {
            $map['a.is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['a.id' => 'asc'];

        $result = $this->alias('a')
            ->field($this->_getListField())
            ->join('__ADMIN__ pa', 'a.parent_id = pa.id', 'LEFT') // 上级
            ->join('__COMPANY__ c', 'a.company_id = c.company_id', 'LEFT') // 分公司
            ->join('__AUTH_GROUP_ACCESS__ aga', 'a.id = aga.uid', 'LEFT') // 用户组明细
            ->join('__AUTH_GROUP__ ag', 'aga.group_id = ag.id', 'LEFT') // Auth用户组
            ->where($map)
            ->order($order)
            ->cache(true, 10)
            ->paginate($size);
        return $result;
    }

    /**
     * 通用化获取参数的数据字段
     * @return array
     */
    private function _getListField()
    {
        return [
            'a.id',
            'a.account',
            'a.parent_id',
            'a.company_id',
            'a.create_time',
            'a.create_ip',
            'a.login_time',
            'a.login_ip',
            'a.status',
            'pa.account parent_account',
            'c.company_name',
            'ag.title auth_group_title'
        ];
    }
}
