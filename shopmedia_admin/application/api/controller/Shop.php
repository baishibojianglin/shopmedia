<?php

namespace app\api\controller;

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


            // 2.3 判断店铺数据

            return show(config('code.success'), '新增成功', $data, 201);

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
                    /*$shopData = [
                        'shop_name' => trim($data['shop_name']),
                        'shop_area' =>
                    ];*/
                    $res[0] = Db::name('shop')->insertGetId();
                } else { // 店家所属用户不存在，则创建用户
                    // 创建店家所属用户
                    $shopkeeperUserData = [
                        'user_name' => 'Sustock-' . trim($data['phone']), // 定义默认用户名
                        'role_ids' => 3, // 用户角色ID
                        'phone' => trim($data['phone']),
                        'phone_verified' => 1,
                        'status' => config('code.status_enable'),
                        'create_time' => time(), // 创建时间
                        'create_ip' => request()->ip() // 创建IP
                    ];
                    $res[0] = $shopkeeperUserID = Db::name('user')->strict(true)->insertGetId($shopkeeperUserData);

                    // 创建店家
                    $shopkeeperData = [
                        'user_id' => $shopkeeperUserID,
                        'salesman_id' => $salesman['id'],
                        'status' => config('code.status_enable'),
                        'create_time' => time()
                    ];
                    $res[1] = Db::name('user_shopkeeper')->insertGetId($shopkeeperData);
                }

                // 任意一个表写入失败都会抛出异常，TODO：是否可以不做该判断
                if (in_array(0, $res)) {
                    return show(config('code.error'), '供应商账户新增失败', '', 403);
                }

                // 提交事务
                Db::commit();
                return show(config('code.success'), '供应商账户新增成功', '', 201);
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return show(config('code.error'), '网络忙，请重试', '', 500);
            }
            /* 手动控制事务 e */

            // 入库操作
            try {
                // 新增反馈
                $id = model('Shop')->add($data, 'shop_id');
            } catch (\Exception $e) {
                return show(config('code.error'), $e->getCode().'网络忙，请重试', '', 500); // $e->getMessage()
            }
            if ($id) {
                return show(config('code.success'), '新增成功', '', 201);
            } else {
                return show(config('code.error'), '新增失败', '', 403);
            }
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }
}
