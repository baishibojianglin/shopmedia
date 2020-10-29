<?php

namespace app\api\controller;

use think\Controller;
use think\Request;
use think\Db;

/**
 * api模块客户端用户（店家）商品交易提成控制器类
 * Class UserShopkeeperCommission
 * @package app\api\controller
 */
class UserShopkeeperOrderCommission extends Controller {
    /**
     * 用户在商城系统 dt.dilinsat.com 购买商品后广告屏安装店家提成
     */
    public function shopkeeperOrderCommission()
    {
        // 判断为POST请求
        /*if (!request()->isPost()) {
            return show(config('code.error'), '请求不合法', '', 400);
        }*/

        // 传入的参数
        $data = input('param.');

        // 根据微信公众号用户信息获取广告屏设备ID
        $userOauthMap = [
            'oauth' => isset($data['oauth']) && $data['oauth'] == 'weixin' ? 'wx' : '',
            'openid' => isset($data['openid']) ? $data['openid'] : ''
        ];
        $userOauth = Db::name('user_oauth')->field('device_id')->where($userOauthMap)->find();

        // 根据广告屏设备ID获取店家及其用户ID
        $device = Db::name('device')->alias('d')
            ->join('__SHOP__ s', 's.shop_id = d.shop_id')
            ->field('s.shopkeeper_id, s.user_id')
            ->where(['device_id' => $userOauth['device_id']])
            ->find();

        // 店家及其用户提成


        //return show(config('code.success'), 'ok', [$data, $userOauth, $device]);
        file_put_contents('./shopkeeperOrderCommission.txt', json_encode([json_encode($data), json_encode($userOauth), json_encode($device)]));
    }
}