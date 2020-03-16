<?php

namespace app\admin\controller;

use app\common\lib\Aes;
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
     * 登录账户的基本信息
     * @var array
     */
    public $companyUser = [];

    /**
     * 初始化方法
     */
    public function _initialize()
    {
        parent::_initialize();

        // 判断是否登录
        if (!($this->isLogin())) {
            return show(config('code.error'), '未登录', '', 401);
        }
    }

    /**
     * 判断是否登录
     * @return bool
     */
    public function isLogin()
    {
        // 判断 token 是否存在
        if (empty($this->headers['company-user-token'])) {
            return false;
        }

        // 判断 token 合法性
        $aes = new Aes();
        $companyToken = $aes->decrypt($this->headers['company-user-token']); // AES解密
        if (empty($companyToken)) {
            return false;
        }

        // 查询账户是否存在或启用
        $companyUser = model('CompanyUser')->loginstatus($companyToken);
        if(!$companyUser || $companyUser['status'] != config('code.status_enable')){
            return false;
        }

        // 验证 token 过期时间
        $time = time();
        if($time - $companyUser['token_time'] > 3600*24){
            return false;
        }

        // 验证通过，重置过期时间
        model('CompanyUser')->setlogintime($companyToken);
        // 赋值登录账户的基本信息
        $this->companyUser = $companyUser;
        return true;
    }
}
