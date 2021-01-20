<?php

namespace app\api\controller;

use app\common\lib\IAuth;
use think\Controller;
use think\Db;
use think\Request;

/**
 * api模块客户端广告套餐订单控制器类
 * Class AdComboOrder
 * @package app\api\controller
 */
class AdComboOrder extends AuthBase
{
    /**
     * 保存新建的订单资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        // 判断为POST请求
        if (request()->isPost()) {
            //return show(config('code.success'), '订单创建成功', ['order_id' => 18], 201); // TODO：用于支付测试
            // 传入的参数
            $param = input('post.');

            // 获取广告主业务员信息
            $salesman = Db::name('user_salesman')->where(['uid' => $param['salesman_user_id'], 'role_id' => 5])->find();
            if (!$salesman) {
                return show(config('code.error'), '用户未开通广告主业务员角色', '', 403);
            }

            /* 手动控制事务 s */
            // 启动事务
            Db::startTrans();
            try {
                // 判断广告主所属用户是否已经存在
                $userData['phone'] = trim($param['phone']);
                $advertiserUser = Db::name('user')->field('user_id')->where(['phone' => $userData['phone']])->find();
                if (empty($advertiserUser)) { // 当广告主所属用户不存在时，则创建广告主所属用户、广告主角色
                    // 创建广告主所属用户
                    $userData['user_name'] = trim($param['user_name']);
                    $userData['role_ids'] = 7;
                    $userData['phone'] = trim($param['phone']);
                    $userData['phone_verified'] = 1;
                    $userData['password'] = IAuth::encrypt(trim($param['phone']));
                    $userData['status'] = config('code.status_enable');
                    $userData['create_time'] = time();
                    $userData['create_ip'] = request()->ip();
                    $res[0] = $userId = Db::name('user')->strict(false)->insertGetId($userData); // 新增数据并返回主键值

                    // 创建广告主角色
                    $advertiserData = [
                        'user_id' => $userId,
                        'salesman_id' => $salesman['id'],
                        'status' => config('code.status_enable'),
                        'create_time' => time()
                    ];
                    $res[1] = $advertiserId = Db::name('user_advertiser')->insertGetId($advertiserData);
                } else { // 当广告主所属用户存在，但广告主角色不存在时，则创建广告主角色
                    // 判断广告主角色是否存在
                    $userId = $advertiserUser['user_id'];
                    $advertiser = Db::name('user_advertiser')->where(['user_id' => $userId])->find();
                    if (empty($advertiser)) {
                        // 创建广告主角色
                        $advertiserData = [
                            'user_id' => $userId,
                            'salesman_id' => $salesman['id'],
                            'status' => config('code.status_enable'),
                            'create_time' => time()
                        ];
                        $res[2] = $advertiserId = Db::name('user_advertiser')->insertGetId($advertiserData);
                    } else {
                        $advertiserId = $advertiser['id'];
                    }
                }

                // 创建订单
                $orderData['order_sn'] = model('AdComboOrder')->getOrderSn('msac'); // 生成唯一订单编号 order_sn
                $orderData['order_time'] = time();
                $orderData['user_id'] = $userId;
                $orderData['advertiser_id'] = $advertiserId;
                $orderData['advertiser_name'] = trim($param['user_name']);
                $orderData['advertiser_address'] = trim($param['advertiser_address']);
                $orderData['salesman_id'] = $salesman['id'];
                $orderData['ad_name'] = trim($param['ad_name']);
                $orderData['combo_id'] = intval($param['combo_id']);
                $orderData['device_quantity'] = intval($param['device_quantity']);
                $orderData['ad_days'] = intval($param['ad_days']);
                $orderData['combo_price'] = trim($param['combo_price']);
                $orderData['order_price'] = trim($param['combo_price']);
                $orderData['order_status'] = 1;
                $orderData['order_time'] = time();
                //$orderData['pay_status'] = 0;
                //$orderData['pay_time'] = time();
                $orderData['party_b_signature'] = isset($param['party_b_signature']) ? $param['party_b_signature'] : '';
                $res[3] = $orderID = Db::name('ad_combo_order')->insertGetId($orderData);

                // 任意一个表写入失败都会抛出异常，TODO：是否可以不做该判断
                if (in_array(0, $res)) {
                    return show(config('code.error'), '订单创建失败', '', 403);
                }

                // 提交事务
                Db::commit();
                return show(config('code.success'), '订单创建成功', ['order_id' => $orderID], 201);
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return show(config('code.error'), '订单创建失败', '', 500);
            }
            /* 手动控制事务 e */
        }
    }

    /**
     * 显示指定的广告套餐订单资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            try {
                $data = model('AdComboOrder')->find($id);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', '', 500); // $e->getMessage()
            }

            if ($data) {
                return show(config('code.success'), 'ok', $data);
            } else {
                return show(config('code.error'), 'Not Found', $data, 404);
            }
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }
}