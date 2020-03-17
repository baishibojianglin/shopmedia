<?php

namespace app\admin\model;

use think\Model;

/**
 * 供应商信息模型类
 * Class Company
 * @package app\admin\model
 */
class User extends Base
{
    /**
     * 表前缀
     */
    protected $connection = ['prefix' => 'goodsmedia_'];

    /**
     * 存入token到供应商账户表
     * @param $id
     * @param $token
     * @return false|int
     */
    public function savetoken($id, $token)
    {
        $map['id'] = $id;
        $data['token'] = $token;
        $data['login_time'] = time(); // 登录时间
        $data['login_ip'] = request()->ip(); // 登录IP
        $list = $this->save($data, $map);
        return $list;
    }


}
