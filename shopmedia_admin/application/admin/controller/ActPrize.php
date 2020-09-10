<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use app\common\lib\IAuth;
use think\Db;
use think\Request;

/**
 * admin模块活动奖品控制器类
 * Class ActPrize
 * @package app\admin\controller
 */
class ActPrize extends Base
{
    /**
     * 显示活动奖品资源列表
     * @return \think\response\Json
     */
    public function index()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的参数
            $param = input('param.');

            // 查询条件
            $map = [];
            if (!empty($param['prize_name'])) {
                $map['ap.prize_name'] = ['like', '%' . trim($param['prize_name']) . '%'];
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = model('ActPrize')->getActPrize($map, (int)$this->size);
            if (!$data) {
                return show(config('code.error'), 'Not Found', '', 404);
            }

            $actPrizeLevel = config('activity.act_prize_level');
            $status = [0 => '下架', 1 => '正常'];
            foreach ($data as $key => $value) {
                // 处理数据
                $data[$key]['quantity'] = $data[$key]['prize_type'] == 1 || 3 ? (int)$data[$key]['quantity'] : $data[$key]['quantity'];
                $data[$key]['level_name'] = $actPrizeLevel[$value['level']];
                $data[$key]['status_msg'] = $status[$value['status']];
            }

            return show(config('code.success'), 'OK', $data);
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 保存新建的活动奖品资源
     * @param Request $request
     * @return \think\response\Json
     * @throws ApiException
     */
    public function save(Request $request)
    {
        // 判断为POST请求
        if (request()->isPost()) {
            // 传入的参数
            $param = input('post.');

            // 判断参数是否存在
            if (empty($param['phone'])) {
                return show(config('code.error'), '请输入电话号码', [], 401);
            }

            /* 手动控制事务 s */
            // 启动事务
            Db::startTrans();
            try {
                $res = [];

                // 新增奖品信息
                $actPrizeData = [
                    'act_id' => (int)$param['act_id'],
                    'prize_type' => (int)$param['prize_type'],
                    'prize_name' => trim($param['prize_name']),
                    'prize_pic' => $param['prize_pic'],
                    'quantity' => (int)$param['quantity'],
                    'percentage' => (float)$param['percentage'],
                    'level' => (int)$param['level'],
                    'sponsor' => trim($param['sponsor']),
                    'phone' => trim($param['phone']),
                    'address' => trim($param['address']),
                    'longitude' => (float)$param['longitude'],
                    'latitude' => (float)$param['latitude'],
                    'is_sponsor_address' => (int)$param['is_sponsor_address'],
                    'create_time' => time(),
                    'status' => (int)$param['status']
                ];
                // TODO：validate验证
                /*$validate = validate('');
                if (!$validate->check($actPrizeData)) {
                    return show(config('code.error'), $validate->getError(), [], 403);
                }*/
                try {
                    $res[0] = $prizeId = Db::name('act_prize')->insertGetId($actPrizeData);
                } catch (\Exception $e) {
                    throw new ApiException($e->getMessage(), 500, config('code.error'));
                }

                // 创建店铺数据（公共部分）
                $shopData = [
                    'shop_name' => trim($param['shop_name']),
                    'cate' => intval($param['ad_cate_id']),
                    //'shop_area' => '',
                    'address' => trim($param['address']),
                    'longitude' => floatval($param['longitude']),
                    'latitude' => floatval($param['latitude']),
                    //'shop_pic' => '',
                    //'environment' => '',
                    //'shop_describe' => '',
                    'status' => config('code.status_enable'),
                    'create_time' => time()
                ];

                // 店铺业务员
                $salesman = Db::name('user_salesman')->field('id')->where(['company_id' => $this->adminUser['company_id'], 'parent_id' => 0, 'role_id' => 6])->find();

                // 以赞助商电话号码作为店家所属用户电话号码
                $phone = trim($param['phone']);
                $shopkeeperUser = model('User')->where(['phone' => $phone])->find();
                if (!empty($shopkeeperUser)) { // 用户存在，店家角色不存在时创建店家角色、店铺
                    $shopkeeper = Db::name('user_shopkeeper')->where(['user_id' => $shopkeeperUser['user_id']])->find();
                    if (empty($shopkeeper)) {
                        // 创建店家角色
                        $shopkeeperData = [
                            'user_id' => $shopkeeperUser['user_id'],
                            'salesman_id' => isset($salesman['id']) ? $salesman['id'] : 0, // TODO：店铺业务员ID
                            'status' => config('code.status_enable'),
                            'create_time' => time()
                        ];
                        $res[1] = $shopkeeperID = Db::name('user_shopkeeper')->insertGetId($shopkeeperData);

                        // 更新用户角色集合role_ids（新增店家角色）
                        //$user = model('User')->field('role_ids')->find($shopkeeperUser['user_id']);
                        $roleIds = explode(',', $shopkeeperUser['role_ids']);
                        if (!in_array(3, $roleIds)) {
                            array_push($roleIds, 3); // 新增店家角色
                            $userData['role_ids'] = implode(',', $roleIds);
                            $res[2] = Db::name('user')->where(['user_id' => $shopkeeperUser['user_id']])->update($userData);
                        }

                        // 创建店铺
                        $shopData['user_id'] = $shopkeeperUser['user_id']; // 店家所属用户ID
                        $shopData['shopkeeper_id'] = $shopkeeperID; // 店铺所属店家ID
                        // validate验证数据合法性
                        $validate = validate('Shop');
                        if (!$validate->check($shopData)) {
                            return show(config('code.error'), $validate->getError(), '', 403);
                        }
                        $res[3] = $shopId = Db::name('shop')->insertGetId($shopData);
                    } else { // 店家存在时，若店铺不存在则创建店铺
                        $shopMap = [
                            'shop_name' => trim($param['shop_name']),
                            'user_id' => $shopkeeperUser['user_id'],
                            'shopkeeper_id' => $shopkeeper['id'],
                            //'longitude' => floatval($param['longitude']),
                            //'latitude' => floatval($param['latitude'])
                        ];
                        $shop = Db::name('shop')->where($shopMap)->find();
                        if (empty($shop)) {
                            // 创建店铺
                            $shopData['user_id'] = $shopkeeperUser['user_id']; // 店家所属用户ID
                            $shopData['shopkeeper_id'] = $shopkeeper['id']; // 店铺所属店家ID
                            // validate验证数据合法性
                            $validate = validate('Shop');
                            if (!$validate->check($shopData)) {
                                return show(config('code.error'), $validate->getError(), '', 403);
                            }
                            $res[4] = $shopId = Db::name('shop')->insertGetId($shopData);
                        } else {
                            $shopId = $shop['shop_id'];
                        }
                    }
                } else { // 店家所属用户不存在，店家一定不存在，则创建店家所属用户、店家及店铺
                    // 创建店家所属用户
                    $shopkeeperUserData = [
                        'user_name' => !empty($param['sponsor']) ? trim($param['sponsor']) : 'Sustock-' . $phone, // 定义默认用户名
                        'role_ids' => 3, // 用户角色ID
                        'phone' => $phone,
                        'phone_verified' => 1,
                        'password' => IAuth::encrypt($phone),
                        'status' => config('code.status_enable'),
                        'create_time' => time(), // 创建时间
                        'create_ip' => request()->ip() // 创建IP
                    ];
                    $res[5] = $shopkeeperUserID = Db::name('user')->strict(true)->insertGetId($shopkeeperUserData);

                    // 创建店家
                    $shopkeeperData = [
                        'user_id' => $shopkeeperUserID,
                        'salesman_id' => isset($salesman['id']) ? $salesman['id'] : 0, // TODO：店铺业务员ID
                        'status' => config('code.status_enable'),
                        'create_time' => time()
                    ];
                    $res[6] = $shopkeeperID = Db::name('user_shopkeeper')->insertGetId($shopkeeperData);

                    // 创建店铺
                    $shopData['user_id'] = $shopkeeperUserID; // 店家所属用户ID
                    $shopData['shopkeeper_id'] = $shopkeeperID; // 店铺所属店家ID
                    // validate验证数据合法性
                    $validate = validate('Shop');
                    if (!$validate->check($shopData)) {
                        return show(config('code.error'), $validate->getError(), '', 403);
                    }
                    $res[7] = $shopId = Db::name('shop')->insertGetId($shopData);
                }
                
                // 将奖品绑定赞助商店铺ID
                $res[8] = Db::name('act_prize')->where(['prize_id' => $prizeId])->update(['shop_id' => $shopId]);

                // 任意一个表写入失败都会抛出异常，TODO：是否可以不做该判断
                if (in_array(0, $res)) {
                    return show(config('code.error'), '新增失败', $res, 403);
                }

                // 提交事务
                Db::commit();
                return show(config('code.success'), '新增成功', '', 201);
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return show(config('code.error'), '请求异常' . $e->getMessage(), $e->getMessage(), 500);
            }
            /* 手动控制事务 e */
        }
    }

    /**
     * 显示指定的活动奖品资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            try {
                $data = model('ActPrize')->find($id);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            if ($data) {
                return show(config('code.success'), 'ok', $data);
            }
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 保存更新的活动奖品资源
     * @param Request $request
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function update(Request $request, $id)
    {
        // 判断为PUT请求
        if (!request()->isPut()) {
            return show(config('code.error'), '请求不合法', '', 400);
        }

        // 传入的参数
        $param = input('param.');

        /* 手动控制事务 s */
        // 启动事务
        Db::startTrans();
        try {
            $res = [];

            // 更新奖品信息
            $actPrizeData = [];
            if (!empty($param['prize_name'])) {
                $actPrizeData['prize_name'] = trim($param['prize_name']);
            }
            if (!empty($param['prize_pic'])) {
                $actPrizeData['prize_pic'] = trim($param['prize_pic']);
            }
            if (isset($param['act_id'])) {
                $actPrizeData['act_id'] = (int)$param['act_id'];
            }
            if (isset($param['prize_type'])) {
                $actPrizeData['prize_type'] = (int)$param['prize_type'];
            }
            if (!empty($param['quantity'])) {
                $actPrizeData['quantity'] = trim($param['quantity']);
            }
            if (!empty($param['level'])) {
                $actPrizeData['level'] = (int)$param['level'];
            }
            if (!empty($param['percentage'])) {
                $actPrizeData['percentage'] = trim($param['percentage']);
            }
            if (!empty($param['sponsor'])) {
                $actPrizeData['sponsor'] = trim($param['sponsor']);
            }
            if (!empty($param['phone'])) {
                $actPrizeData['phone'] = trim($param['phone']);
            }
            if (isset($param['is_sponsor_address'])) {
                $actPrizeData['is_sponsor_address'] = intval($param['is_sponsor_address']);
            }
            if (!empty($param['address'])) {
                $actPrizeData['address'] = trim($param['address']);
            }
            if (!empty($param['longitude'])) {
                $actPrizeData['longitude'] = floatval($param['longitude']);
            }
            if (!empty($param['latitude'])) {
                $actPrizeData['latitude'] = floatval($param['latitude']);
            }
            if (isset($param['status'])) {
                $actPrizeData['status'] = (int)$param['status'];
            }

            if (empty($actPrizeData)) {
                return show(config('code.error'), '数据不合法', '', 404);
            }

            // 更新
            try {
                $res[0] = Db::name('act_prize')->where(['prize_id' => $id])->update($actPrizeData); // 更新
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            // 任意一个表写入失败都会抛出异常，TODO：是否可以不做该判断
            if (in_array(0, $res)) {
                return show(config('code.error'), '更新失败', $res, 403);
            }

            // 提交事务
            Db::commit();
            return show(config('code.success'), '更新成功', '', 201);
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return show(config('code.error'), '请求异常' . $e->getMessage(),  $e->getMessage(), 500);
        }
        /* 手动控制事务 e */
    }

    /**
     * 删除指定新闻资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function delete($id)
    {
        // 判断为DELETE请求
        if (!request()->isDelete()) {
            return show(config('code.error'), '请求不合法', '', 400);
        }

        // 显示指定的新闻资源
        try {
            $data = model('News')->find($id);
            //return show(config('code.success'), 'ok', $data);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), 500, config('code.error'));
        }

        // 判断数据是否存在
        if ($data['news_id'] != $id) {
            return show(config('code.error'), '数据不存在');
        }

        // 判断删除条件：新闻状态
        if (in_array($data['status'], [1, 2, 4])) {
            return show(config('code.error'), '删除失败：新闻待审核、审核通过或已发布，禁止删除', '');
        }

        // 软删除
        if ($data['is_delete'] != config('code.is_delete')) {
            // 捕获异常
            try {
                $result = model('News')->softDelete('news_id', $id);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            if (!$result) {
                return show(config('code.error'), '移除失败', '');
            } else {
                return show(config('code.success'), '移除成功', '');
            }
        }

        // 真删除
        if ($data['is_delete'] == config('code.is_delete')) {
            $result = model('News')->destroy($id);
            if (!$result) {
                return show(config('code.error'), '删除失败', '');
            } else {
                // 删除文件
                @unlink(ROOT_PATH . 'public' . DS . $data['thumb']);

                return show(config('code.success'), '删除成功', '');
            }
        }
    }

    /**
     * 活动奖品等级列表（不分页，用于 Select 选择器等）
     * @return \think\response\Json
     */
    public function actPrizeLevelList()
    {
        $actPrizeLevel = config('activity.act_prize_level');
        $data = []; // 定义二维数组列表
        // 处理数据，将一维数组转成二维数组
        foreach ($actPrizeLevel as $key => $value) {
            $data[] = ['level_id' => $key, 'level_name' => $value];
        }

        return show(config('code.success'), 'OK', $data);
    }
}