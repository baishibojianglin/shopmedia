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
     * 获取用户（传媒设备合作者）列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getUserPartner($map = [], $size = 5)
    {
        if(!isset($map['u.is_delete'])) {
            $map['u.is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['user_id' => 'asc'];

        $result = $this->alias('u')
            ->field(array_merge($this->_getListField(), ['up.money', 'up.income', 'up.cash', 'up.status partner_status']))
            ->join('__USER_PARTNER__ up', 'u.user_id = up.user_id') // 传媒设备合作者
            ->where($map)
            ->order($order)
            ->paginate($size);
        return $result;
    }

    /**
     * 获取用户（传媒设备合作者业务员）列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getUserToPartner($map = [], $size = 5)
    {
        if(!isset($map['u.is_delete'])) {
            $map['u.is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['user_id' => 'asc'];

        $result = $this->alias('u')
            ->field(array_merge($this->_getListField(), ['utp.company_id', 'utp.money', 'utp.income', 'utp.cash', 'utp.status to_partner_status', 'c.company_name']))
            ->join('__USER_TO_PARTNER__ utp', 'u.user_id = utp.user_id') // 传媒设备合作者业务员
            ->join('__COMPANY__ c', 'utp.company_id = c.company_id', 'LEFT') // 分公司
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
            'u.phone',
            'u.phone_verified',
            'u.avatar',
            'u.status',
            'u.create_time',
            'u.create_ip',
            'u.login_time',
            'u.login_ip',
            'u.is_delete'
        ];
    }
}
