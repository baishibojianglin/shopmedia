<?php

namespace app\api\controller;

use app\common\lib\Aes;
use app\common\lib\exception\ApiException;
use app\common\model\User;
use think\Controller;

/**
 * 客户端Auth登录授权基础控制器类库
 * 1.需要登录的每个接口（个人中心、点赞、评论等）都需要继承该类库
 * 2.判断 token 如 access_user_token 是否合法
 * 3.将用户信息给一个变量如 user
 *
 * Class AuthBase
 * @package app\api\controller\v1
 */
class AuthBase extends Common
{
    /**
     * 登录用户的基本信息
     * @var array
     */
    public $user = [];

    /**
     * 初始化
     */
    public function _initialize()
    {
        parent::_initialize();

        // 判断是否登录
        if (!($this->isLogin())) {
            throw new ApiException('未登录', 401);
        }
    }

    /**
     * 判断是否登录
     * @return bool
     */
    public function isLogin()
    {
        // 判断 access_user_token 是否存在
        if (empty($this->headers['access-user-token'])) {
            return false;
        }

        // 判断 access_user_token 合法性
        $aes = new Aes(); // 实例化Aes
        $accessUserToken = $aes->decrypt($this->headers['access-user-token']); // AES解密
        if (empty($accessUserToken)) {
            return false;
        }
        if (!preg_match('/&/', $accessUserToken)) {
            return false;
        }
        list($token, $id) = explode('&', $accessUserToken);

        // 查询用户是否存在
        $user = User::get(['token' => $token]);
        if (!$user || $user->status != config('code.status_enable')) {
            return false;
        }

        // 判断时间是否过期
        if (time() > $user->token_time) {
            return false;
        }

        // 赋值登录用户的基本信息
        $user['gender_msg'] = config('code.gender')[$user['gender']];
        $this->user = $user;
        return true;
    }
}
