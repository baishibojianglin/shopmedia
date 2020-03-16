<?php

namespace app\admin\model;

use think\Model;

/**
 * Auth权限认证用户组模型类
 * Class AuthGroup
 * @package app\common\model
 */
class AuthGroup extends Base
{
    /**
     * 获取Auth用户组列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getAuthGroup($map = [], $size = 5)
    {
        /*if(!isset($map['is_delete'])) {
            $map['is_delete'] = ['neq', config('code.is_delete')];
        }*/

        $order = ['ag.id' => 'asc'];

        $result = $this->alias('ag')
            ->field('ag.*, pag.title parent_title')
            ->join('__AUTH_GROUP__ pag', 'ag.parent_id = pag.id', 'LEFT')
            ->where($map)
            ->order($order)
            ->paginate($size);
        return $result;
    }

    /**
     * 通过当前账户user_id获取其所有（含通用、自身和下级）Auth用户组ID
     * @param $user_id
     * @return array
     */
    public function getAuthGroupIdsByUserId($user_id)
    {
        // 获取通用Auth用户组ID（TODO：除供应商总管理员外，当前账户禁止操作通用Auth用户组）
        $generalGroupIds = $this->where(['type' => 1])->column('id');
        // 通过uid获取Auth用户组明细的group_id
        $selfGroupIds = model('AuthGroupAccess')->where(['uid' => $user_id])->column('group_id');
        // 获取自有下级Auth用户组ID
        $sonGroupIds = $this->where(['parent_id' => ['in', $selfGroupIds]])->column('id');
        // 当前账户所有Auth用户组ID
        $authGroupIds = array_keys(array_flip($generalGroupIds) + array_flip($selfGroupIds) + array_flip($sonGroupIds)); // 多数组合并去重，区别于 array_unique(array_merge($generalGroupIds, $selfGroupIds, $sonGroupIds));
        return $authGroupIds;
    }
}
