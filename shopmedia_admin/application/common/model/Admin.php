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
     * 表前缀
     */
    protected $connection = ['prefix' => 'goodsmedia_'];

    /**
     * 存入token到管理员表
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
