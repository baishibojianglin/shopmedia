<?php
/**
 * Created by PhpStorm.
 * User: Yan
 * Date: 2019/7/18
 * Time: 15:22
 */

// APP客户端配置
return [
    'password_pre_salt' => '#sustock_shop_media', // 密码加密盐
    'aeskey' => '7ff9968f64d69c6ccabe3e2c1bc38ffe', // aes密钥，服务端和客户端必须保持一致 MD5('#sustock_shop_media')
    'apptypes' => ['android', 'ios', 'devtools','other'],
    'version' => 1, // 大版本号
    'did' => 'sustock2020', // 设备号
    'app_sign_time' => 10, // sign失效时间（秒）
    'app_sign_cache_time' => 20, // sign缓存失效时间（秒）
    'login_time_out' => 7 . 'days', // 登录token的失效时间

    'I_SERVER_NAME' => 'http://' . $_SERVER['SERVER_NAME'] . '/index.php/', // 当前域名
];