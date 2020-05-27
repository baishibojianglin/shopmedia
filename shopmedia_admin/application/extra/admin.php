<?php
/**
 * Created by PhpStorm.
 * User: Yan
 * Date: 2020/3/31
 * Time: 18:51
 */

// 管理系统配置
return [
    'login_time_out' => 3600 . 'seconds', // 登录token的失效时间

    // session设置
    'session_admin' => 'sustock_shopmedia_admin', // 管理员session名称
    'session_admin_scope' => 'sustock_shopmedia_admin_scope', // 作用域

    'platform_company_id' => 0 // 系统平台对应的分公司ID
];