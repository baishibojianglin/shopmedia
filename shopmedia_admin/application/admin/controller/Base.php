<?php

namespace app\admin\controller;

use app\common\lib\Aes;
use app\common\lib\exception\ApiException;
use app\common\model\Admin;
use think\Controller;
use think\Request;

/**
 * admin模块登录授权基础控制器类
 * Class Base
 * @package app\admin\controller
 */
class Base extends Common
{
    /**
     * 登录管理用户的基本信息
     * @var array
     */
    public $adminUser = [];

    /**
     * 初始化方法
     */
    public function _initialize()
    {
        parent::_initialize();

        // 判断是否登录
        if (!($this->isLogin())) {
            throw new ApiException('未登录', 401);
            //return show(config('code.error'), '未登录', '', 401);
        }
    }

    /**
     * 判断是否登录
     * @return bool
     */
    public function isLogin()
    {
        // 判断 token 是否存在
        if (empty($this->headers['admin-user-token'])) {
            return false;
        }

        // 判断 token 合法性
        $aes = new Aes();
        $adminUserToken = $aes->adminDecrypt($this->headers['admin-user-token']); // AES解密
        if (empty($adminUserToken)) {
            return false;
        }

        // 查询管理用户是否存在或启用
        $adminUser = Admin::get(['token' => $adminUserToken]);
        if (!$adminUser || $adminUser['status'] != config('code.status_enable')) {
            return false;
        }

        // 验证 token 过期时间
        if(time() > $adminUser['token_time']){
            return false;
        }

        // 赋值登录管理用户的基本信息
        $this->adminUser = $adminUser;
        return true;
    }
}
