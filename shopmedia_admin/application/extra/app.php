<?php
/**
 * Created by PhpStorm.
 * User: Yan
 * Date: 2019/7/18
 * Time: 15:22
 */

return [
    'password_pre_salt' => '#dlst_hunter', // 密码加密盐
    'aeskey' => 'ccd8a57555e2e8c088044a760626fa03', // aes密钥，服务端和客户端必须保持一致 MD5('#dlst_hunter')
    'apptypes' => ['android', 'ios', 'devtools'],
    'app_sign_time' => 10, // sign失效时间
    'app_sign_cache_time' => 20, // sign缓存失效时间
    'login_time_out_day' => 7, // 登录token的失效时间

    'I_SERVER_NAME' => 'http://' . $_SERVER['SERVER_NAME'] . '/index.php/', // 当前域名
];