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

        $order = ['u.user_id' => 'asc'];

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

        $order = ['u.user_id' => 'asc'];

        $result = $this->alias('u')
            ->field(array_merge($this->_getListField(), ['utp.company_id', 'utp.parent_id', 'utp.money', 'utp.income', 'utp.cash', 'utp.status utp_status', 'c.company_name', 'p.user_name parent_name']))
            ->join('__USER_TO_PARTNER__ utp', 'u.user_id = utp.user_id') // 传媒设备合作者业务员
            ->join('__COMPANY__ c', 'utp.company_id = c.company_id', 'LEFT') // 分公司
            ->join('__USER__ p', 'utp.parent_id = p.user_id', 'LEFT') // 上级
            ->where($map)
            ->order($order)
            ->paginate($size);
        return $result;
    }

    /**
     * 获取用户（广告主业务员）列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getUserToAd($map = [], $size = 5)
    {
        if(!isset($map['u.is_delete'])) {
            $map['u.is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['u.user_id' => 'asc'];

        $result = $this->alias('u')
            ->field(array_merge($this->_getListField(), ['uta.company_id', 'uta.parent_id', 'uta.money', 'uta.income', 'uta.cash', 'uta.status uta_status', 'c.company_name', 'p.user_name parent_name']))
            ->join('__USER_TO_AD__ uta', 'u.user_id = uta.user_id') // 广告主业务员
            ->join('__COMPANY__ c', 'uta.company_id = c.company_id', 'LEFT') // 分公司
            ->join('__USER__ p', 'uta.parent_id = p.user_id', 'LEFT') // 上级
            ->where($map)
            ->order($order)
            ->paginate($size);
        return $result;
    }

    /**
     * 获取用户（店铺端业务员）列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getUserToShop($map = [], $size = 5)
    {
        if(!isset($map['u.is_delete'])) {
            $map['u.is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['u.user_id' => 'asc'];

        $result = $this->alias('u')
            ->field(array_merge($this->_getListField(), ['uts.company_id', 'uts.parent_id', 'uts.money', 'uts.income', 'uts.cash', 'uts.status uts_status', 'c.company_name', 'p.user_name parent_name']))
            ->join('__USER_TO_SHOP__ uts', 'u.user_id = uts.user_id') // 店铺端业务员
            ->join('__COMPANY__ c', 'uts.company_id = c.company_id', 'LEFT') // 分公司
            ->join('__USER__ p', 'uts.parent_id = p.user_id', 'LEFT') // 上级
            ->where($map)
            ->order($order)
            ->paginate($size);
        return $result;
    }


    /**
     * 获取用户（店铺端用户）列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getUserShop($map = [], $size = 5)
    {
        if(!isset($map['u.is_delete'])) {
            $map['u.is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['u.user_id' => 'asc'];

        $result = $this->alias('u')
            ->field(array_merge($this->_getListField(), ['us.parent_id', 'us.money', 'us.income', 'us.cash', 'us.status us_status', 'p.user_name parent_name']))
            ->join('__USER_SHOP__ us', 'u.user_id = us.user_id') // 店铺端业务员
            ->join('__USER__ p', 'us.parent_id = p.user_id', 'LEFT') // 上级
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
