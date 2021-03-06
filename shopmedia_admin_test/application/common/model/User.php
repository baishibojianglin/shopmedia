<?php

namespace app\common\model;

use think\Model;

/**
 * 用户模型类
 * Class User
 * @package app\common\model
 */
class User extends Base
{
    /**
     * 获取用户列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getUser($map = [], $size = 5)
    {
        if(!isset($map['is_delete'])) {
            $map['is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['user_id' => 'asc'];

        $result = $this->field('password', true) // 字段排除
            ->where($map)
            ->order($order)
            ->paginate($size);
        return $result;
    }

    /**
     * 获取用户（业务员）列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getUserSalesman($map = [], $size = 5)
    {
        if(!isset($map['u.is_delete'])) {
            $map['u.is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['u.user_id' => 'asc', 'us.id' => 'asc'];

        $result = $this->alias('u')
            ->field(array_merge($this->_getListField(), ['us.id', 'us.role_id', 'us.company_id', 'us.parent_id', 'us.money', 'us.income', 'us.cash', 'us.comm_ratio', 'us.parent_comm_ratio', 'us.auth_son_ratio', 'us.son_invitation_code', 'us.auth_open_user', 'us.invitation_code', 'us.status us_status', 'c.company_name', 'pu.user_name parent_name', 'ur.title']))
            ->join('__USER_SALESMAN__ us', 'u.user_id = us.uid') // 业务员
            ->join('__USER_ROLE__ ur', 'us.role_id = ur.id and ur.parent_id = 1') // 角色
            ->join('__COMPANY__ c', 'us.company_id = c.company_id', 'LEFT') // 分公司
            ->join('__USER_SALESMAN__ p', 'us.parent_id = p.id', 'LEFT') // 上级用户角色
            ->join('__USER__ pu', 'p.uid = pu.user_id', 'LEFT') // 上级用户
            ->where($map)
            ->order($order)
            ->paginate($size);
        return $result;
    }

    /**
     * 获取用户（广告屏合作商）列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getUserPartner($map = [], $size = 5)
    {
        if(!isset($map['up.is_delete'])) {
            $map['up.is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['u.user_id' => 'asc'];

        $result = $this->alias('u')
            ->field(array_merge($this->_getListField(), ['up.role_id', 'up.money', 'up.income', 'up.cash', 'up.status partner_status', 'up.is_delete', 'us.company_id']))
            ->join('__USER_PARTNER__ up', 'u.user_id = up.user_id') // 广告屏合作商
            ->join('__USER_ROLE__ ur', 'up.role_id = ur.id and up.role_id = 2') // 角色
            ->join('__USER_SALESMAN__ us', 'up.salesman_id = us.id and us.role_id = 4') // 广告屏合作商业务员
            ->where($map)
            ->order($order)
            ->paginate($size);
        return $result;
    }

    /**
     * 获取用户（店家）列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getUserShopkeeper($map = [], $size = 5)
    {
        if(!isset($map['u.is_delete'])) {
            $map['u.is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['us.id' => 'desc'];

        $result = $this->alias('u')
            ->field(array_merge($this->_getListField(), ['us.id shopkeeper_id', 'us.role_id', 'us.parent_id', 'us.money', 'us.income', 'us.cash', 'us.status us_status', 'pu.user_name parent_name']))
            ->join('__USER_SHOPKEEPER__ us', 'u.user_id = us.user_id') // 店家
            ->join('__USER_ROLE__ ur', 'us.role_id = ur.id and us.role_id = 3') // 角色
            ->join('__USER_SALESMAN__ usm', 'us.salesman_id = usm.id', 'LEFT') // 店家业务员
            ->join('__USER_SHOPKEEPER__ p', 'us.parent_id = p.id', 'LEFT') // 上级用户角色
            ->join('__USER__ pu', 'p.user_id = pu.user_id', 'LEFT') // 上级用户
            ->where($map)
            ->order($order)
            ->paginate($size);
        return $result;
    }

    /**
     * 获取用户（广告主）列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getUserAdvertiser($map = [], $size = 5)
    {
        if(!isset($map['u.is_delete'])) {
            $map['u.is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['u.user_id' => 'asc'];

        $result = $this->alias('u')
            ->field(array_merge($this->_getListField(), ['ua.id advertiser_id', 'ua.role_id', 'ua.status ua_status']))
            ->join('__USER_ADVERTISER__ ua', 'u.user_id = ua.user_id') // 广告主
            ->join('__USER_ROLE__ ur', 'ua.role_id = ur.id and ua.role_id = 7') // 角色
            ->join('__USER_SALESMAN__ usm', 'ua.salesman_id = usm.id', 'LEFT') // 广告主业务员
            ->where($map)
            ->order($order)
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
            'u.user_id',
            'u.user_name',
            'u.role_ids',
            'u.phone',
            'u.phone_verified',
            'u.avatar',
            'u.status',
            'u.create_time',
            'u.create_ip',
            'u.login_time',
            'u.login_ip'
        ];
    }
}
