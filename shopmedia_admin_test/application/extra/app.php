<?php
/**
 * Created by PhpStorm.
 * User: Yan
 * Date: 2019/7/18
 * Time: 15:22
 */

// APP客户端配置
return [
    'password_pre_salt' => '#sustock_shop_media_2020', // 密码加密盐
    'aeskey' => '63c1e23f192033ceec6f883639591b76', // aes密钥，服务端和客户端必须保持一致 MD5('#sustock_shop_media_2020')
    'apptypes' => ['android', 'ios', 'devtools', 'other'],
    'version' => 1, // 大版本号
    'did' => 'sustock2020', // 设备号
    'app_sign_time' => 10, // sign失效时间（秒）
    'app_sign_cache_time' => 20, // sign缓存失效时间（秒）
    'login_time_out' => 7 . 'days', // 登录token的失效时间

    'http_type' => ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://', // 判断是http请求头还是https
    'I_SERVER_NAME' => $_SERVER['SERVER_NAME'], // 当前域名
];