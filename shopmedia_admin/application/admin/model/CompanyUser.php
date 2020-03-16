<?php

namespace app\admin\model;

use think\Model;

/**
 * 供应商账户模型类
 * Class CompanyUser
 * @package app\admin\model
 */
class CompanyUser extends Base
{
    /**
     * 表前缀
     */
    protected $connection = ['prefix' => 'goodshop_'];

    /**
     * 供应商账户登录
     * @param $account
     * @param $password
     * @return array|false|\PDOStatement|string|Model
     */
    public function checklogin($account, $password)
    {
        $map['account|phone'] = $account;
        $map['password'] = $password;
        $map['status'] = config('code.status_enable'); // 启用状态
        $list = $this->where($map)->field('password,token_time', true)->find();
        return $list;
    }

    /**
     * 存入token到供应商账户表
     * @param $id
     * @param $token
     * @return false|int
     */
    public function savetoken($id, $token)
    {
        $map['user_id'] = $id;
        $data['token'] = $token;
        $data['token_time'] = time(); // token失效时间
        $data['login_time'] = time(); // 登录时间
        $data['login_ip'] = request()->ip(); // 登录IP
        $list = $this->save($data, $map);
        return $list;
    }

    /**
     * 检查账户登录状态
     * @param $token
     * @return array|false|\PDOStatement|string|Model
     */
    public function loginstatus($token)
    {
        $map['token'] = $token;
        $list = $this->where($map)->find();
        return $list;
    }

    /**
     * 重置登录过期时间
     * @param $token
     */
    public function setlogintime($token){
        $map['token'] = $token;
        $data['token_time'] = time();
        $list = $this->save($data, $map);
    }

    /**
     * 获取供应商账户列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getCompanyUser($map = [], $size = 5)
    {
        if(!isset($map['cu.is_delete'])) {
            $map['cu.is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['cu.user_id' => 'asc'];

        $result = $this->alias('cu')
            ->field($this->_getListField())
            ->join('__COMPANY_USER__ pcu', 'cu.parent_id = pcu.user_id', 'LEFT') // 上级
            ->join('__COMPANY__ c', 'cu.company_id = c.id', 'LEFT') // 供应商
            ->join('__AUTH_GROUP_ACCESS__ aga', 'cu.user_id = aga.uid', 'LEFT') // 供应商账户角色
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
            'cu.user_id',
            'cu.company_id',
            'cu.parent_id',
            'cu.user_name',
            'cu.account',
            'cu.phone',
            'cu.avatar',
            'cu.status',
            'cu.create_time',
            'cu.create_ip',
            'cu.login_time',
            'cu.login_ip',
            'pcu.user_name parent_name',
            'c.name company_name',
            'ag.title auth_group_title'
        ];
    }
}
