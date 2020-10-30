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

        // 店家及其用户提成入库操作
        /* 手动控制事务 s */
        // 启动事务
        Db::startTrans();
        try {
            $res = [];

            // 店家表写入提成
            $res[0] = Db::name('user_shopkeeper')->where(['id' => $device['shopkeeper_id']])->setInc('money', $data['commission_money']) === false ? 0 : true;
            $res[1] = Db::name('user_shopkeeper')->where(['id' => $device['shopkeeper_id']])->setInc('income', $data['commission_money']) === false ? 0 : true;
            // 店家用户表写入提成
            $res[2] = Db::name('user')->where(['user_id' => $device['user_id']])->setInc('money', $data['commission_money']) === false ? 0 : true;
            $res[3] = Db::name('user')->where(['user_id' => $device['user_id']])->setInc('income', $data['commission_money']) === false ? 0 : true;

            // 任意一个表写入失败都会抛出异常
            if (in_array(0, $res)) {
                file_put_contents('../runtime/shopkeeper_order_commission.txt', json_encode($res));
                return show(config('code.error'), '新增失败', $res, 403);
            }

            // 提交事务
            Db::commit();
            file_put_contents('../runtime/shopkeeper_order_commission.txt', json_encode([json_encode($data), json_encode($userOauth), json_encode($device)]));
            return show(config('code.success'), '新增成功', '', 201);
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            file_put_contents('../runtime/shopkeeper_order_commission.txt', '请求异常' . $e->getMessage());
            return show(config('code.error'), '请求异常' . $e->getMessage(), '', 500);
        }
    }
}