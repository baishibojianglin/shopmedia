<?php

namespace app\api\controller;

use app\common\lib\exception\ApiException;
use app\common\lib\IAuth;
use think\Controller;
use think\Db;
use think\Request;

/**
 * api模块客户端店家店铺控制器类
 * Class Shop
 * @package app\api\controller
 */
class Shop extends AuthBase
{
    /**
     * 保存新建的店铺资源（通过业务员添加）
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        // 判断为POST请求
        if(request()->isPost()){
            // 1 传入的参数
            $data = input('post.');

            // 2 判断数据是否存在
            // 2.1 判断业务员（包括业务员所属用户）数据：user_salesman、user表
            $salesmanUser = Db::name('user')->find($data['user_id']);
            if (empty($data['user_id']) || !$salesmanUser) {
                return show(config('code.error'), '业务员所属用户不存在', '', 403);
            }
            $salesman = Db::name('user_salesman')->where(['uid' => $data['user_id'], 'role_id' => 6])->find();
            if (!$salesman) {
                return show(config('code.error'), '用户未开通店家业务员角色', '', 403);
            }

            // 2.3 TODO：判断店铺数据
            // 处理数据
            $data['image'] = json_encode($data['image']);
            // 创建店铺数据（公共部分）
            $shopData = [
                'shop_name' => trim($data['shop_name']),
                'cate' => intval($data['cate']),
                'shop_area' => floatval($data['shop_area']),
                'address' => trim($data['address']),
                'longitude' => floatval($data['longitude']),
                'latitude' => floatval($data['latitude']),
                'shop_pic' => $data['image'],
                'environment' => intval($data['environment']),
                'describe' => $data['describe'],
                'status' => config('code.status_enable'),
                'create_time' => time()
            ];

            // 3 入库操作
            /* 手动控制事务 s */
            // 启动事务
            Db::startTrans();
            try {
                // 2.2 判断店铺所属店家（包括店家所属用户）数据：user_shopkeeper、user表
                // 获取店家所属用户数据
                $shopkeeperUser = Db::name('user')->where(['phone' => trim($data['phone'])])->find();
                // 获取店家数据
                $shopkeeper = Db::name('user_shopkeeper')->where(['user_id' => $shopkeeperUser['user_id']])->find();
                if ($shopkeeperUser && $shopkeeper) { // 店家及店家所属用户均存在，则只创建店铺
                    // 创建店铺
                    $shopData['user_id'] = $shopkeeperUser['user_id']; // 店家所属用户ID
                    $shopData['shopkeeper_id'] = $shopkeeper['id']; // 店铺所属店家ID
                    $res[0] = Db::name('shop')->insertGetId($shopData);
                } elseif (!$shopkeeperUser) { // 店家所属用户不存在，店家一定不存在，则创建店家所属用户、店家及店铺
                    // 创建店家所属用户
                    $shopkeeperUserData = [
                        'user_name' => 'Sustock-' . trim($data['phone']), // 定义默认用户名
                        'role_ids' => 3, // 用户角色ID
                        'phone' => trim($data['phone']),
                        'phone_verified' => 1,
                        'password' => IAuth::encrypt(trim($data['phone'])),
                        'status' => config('code.status_enable'),
                        'create_time' => time(), // 创建时间
                        'create_ip' => request()->ip() // 创建IP
                    ];
                    $res[1] = $shopkeeperUserID = Db::name('user')->strict(true)->insertGetId($shopkeeperUserData);

                    // 创建店家
                    $shopkeeperData = [
                        'user_id' => $shopkeeperUserID,
                        'salesman_id' => $salesman['id'],
                        'status' => config('code.status_enable'),
                        'create_time' => time()
                    ];
                    $res[2] = $shopkeeperID = Db::name('user_shopkeeper')->insertGetId($shopkeeperData);

                    // 创建店铺
                    $shopData['user_id'] = $shopkeeperUserID; // 店家所属用户ID
                    $shopData['shopkeeper_id'] = $shopkeeperID; // 店铺所属店家ID
                    $res[3] = Db::name('shop')->insertGetId($shopData);
                } elseif (!$shopkeeper) { // 店家所属用户存在，但店家不存在时，则创建店家及店铺
                    // 创建店家
                    $shopkeeperData = [
                        'user_id' => $shopkeeperUser['user_id'],
                        'salesman_id' => $salesman['id'],
                        'status' => config('code.status_enable'),
                        'create_time' => time()
                    ];
                    $res[4] = $shopkeeperID = Db::name('user_shopkeeper')->insertGetId($shopkeeperData);

                    // 创建店铺
                    $shopData['user_id'] = $shopkeeperUser['user_id']; // 店家所属用户ID
                    $shopData['shopkeeper_id'] = $shopkeeperID; // 店铺所属店家ID
                    $res[5] = Db::name('shop')->insertGetId($shopData);
                }

                // 任意一个表写入失败都会抛出异常，TODO：是否可以不做该判断
                if (in_array(0, $res)) {
                    return show(config('code.error'), '店铺创建失败', '', 403);
                }

                // 提交事务
                Db::commit();
                return show(config('code.success'), '店铺创建成功', '', 201);
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return show(config('code.error'), '网络忙，请重试', '', 500);
            }
            /* 手动控制事务 e */
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 显示指定的店铺资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            try {
                $data = model('Shop')->alias('s')
                    ->field(['s.*', 'u.user_name', 'rp.region_name province', 'rc.region_name city', 'rco.region_name county', 'rt.region_name town'])
                    ->join('__USER__ u', 's.user_id = u.user_id', 'LEFT') // 用户
                    ->join('__USER_SHOPKEEPER__ us', 's.shopkeeper_id = us.id', 'LEFT') // 店家
                    ->join('__REGION__ rp', 's.province_id = rp.region_id', 'LEFT') // 区域（省级）
                    ->join('__REGION__ rc', 's.city_id = rc.region_id', 'LEFT') // 区域（市级）
                    ->join('__REGION__ rco', 's.county_id = rco.region_id', 'LEFT') // 区域（区县）
                    ->join('__REGION__ rt', 's.town_id = rt.region_id', 'LEFT') // 区域（乡镇街道）
                    ->find($id);
            } catch (\Exception $e) {
                return show(config('code.error'), '请求异常', '', 500);
                //throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            if ($data) {
                // 处理数据
                // 定义status_msg
                $status = config('code.status');
                $data['status_msg'] = $status[$data['status']];

                return show(config('code.success'), 'ok', $data);
            }
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 保存更新的店铺资源
     * @param Request $request
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function update(Request $request, $id)
    {
        // 判断为PUT请求
        if (request()->isPut()) {
            // 传入的参数
            $param = input('param.');

            // 判断数据是否存在
            $data = [];
            if (!empty($param['device_ids'])) {
                $data['device_ids'] = trim($param['device_ids']);
            }
            if (!empty($param['device_quantity'])) {
                $data['device_quantity'] = intval($param['device_quantity']);
            }
            if (!empty($param['device_price'])) {
                $data['device_price'] = floatval($param['device_price']);
            }
            if (!empty($param['party_b_share'])) {
                $data['party_b_share'] = floatval($param['party_b_share'] / 100);
            }
            if (!empty($param['party_b_name'])) {
                $data['party_b_name'] = trim($param['party_b_name']);
            }
            if (!empty($param['party_b_signature'])) {
                $data['party_b_signature'] = $param['party_b_signature'];
            }

            if (empty($data)) {
                return show(config('code.error'), '数据不合法', '', 404);
            }

            // 签署时间
            $data['sign_time'] = time();

            // 更新
            try {
                $result = model('Shop')->save($data, ['shop_id' => $id]); // 更新
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
            if (false === $result) {
                return show(config('code.error'), '更新失败', '', 403);
            } else {
                return show(config('code.success'), '更新成功', '', 201);
            }
        }else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }
}
