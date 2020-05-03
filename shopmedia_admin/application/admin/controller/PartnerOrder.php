<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Db;
use think\Request;

/**
 * admin模块用户（广告屏合作商）订单控制器类
 * Class PartnerOrder
 * @package app\admin\controller
 */
class PartnerOrder extends Base
{
    /**
     * 显示订单资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的参数
            $param = input('param.');
            if (isset($param['size'])) { // 每页条数
                $param['size'] = intval($param['size']);
            }

            // 查询条件
            $map = [];
            if (!empty($param['order_sn'])) { // 订单编号
                $map['o.order_sn'] = ['like', '%' . trim($param['order_sn']) . '%'];
            }
            if (!empty($param['user_name'])) { // 用户（广告屏合作商）名称
                $map['u.user_name'] = ['like', '%' . trim($param['user_name']) . '%'];
            }
            if (!empty($param['phone'])) { // 用户（广告屏合作商）电话
                $map['u.phone'] = ['like', '%' . trim($param['phone']) . '%'];
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取订单列表数据
            try {
                $data = model('PartnerOrder')->getPartnerOrder($map, $this->size);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', [], 500); // $e->getMessage()
            }

            if ($data) {
                // 处理数据
                $orderStatus = config('code.order_status'); // 订单状态
                $payStatus = config('code.pay_status'); // 支付状态
                foreach ($data as $key => $value) {
                    $data[$key]['order_status_msg'] = $orderStatus[$value['order_status']]; // 定义订单状态信息
                    $data[$key]['order_time'] = $value['order_time'] ? date('Y-m-d H:i:s', $value['order_time']) : ''; // 下单时间
                    $data[$key]['pay_status_msg'] = $payStatus[$value['pay_status']]; // 定义支付状态信息
                    $data[$key]['pay_time'] = $value['pay_time'] ? date('Y-m-d H:i:s', $value['pay_time']) : ''; // 支付时间
                }

                return show(config('code.success'), 'OK', $data);
            } else {
                return show(config('code.error'), 'Not Found', $data, 404);
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }

    /**
     * 保存新建的订单资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        // 判断为POST请求
        if(request()->isPost()){
            $data = input('post.');

            $data['order_sn'] = model('PartnerOrder')->getOrderSn(); // 生成唯一订单编号 order_sn
            $data['order_time'] = time();
            //$data['order_status'] = 0;
            $data['order_price'] = (isset($data['order_price']) && $data['order_price'] != 0) ? $data['order_price'] : $data['device_price'];

            // 获取广告屏合作商对应用户信息
            $userPartner = Db::name('user_partner')->alias('up')
                ->field('up.id partner_id, up.user_id, u.user_name, u.phone')
                ->join('__USER__ u', 'up.user_id = u.user_id')
                ->where(['up.id' => $data['partner_id']])
                ->find();
            if (!$userPartner) {
                return show(config('code.error'), '广告屏合作商不存在', '', 404);
            }
            $data['user_id'] = $userPartner['user_id'];
            $data['phone'] = $userPartner['phone'];

            // 获取广告屏信息
            $device = model('Device')->field('device_id, sale_price')->find($data['device_id']);
            if (!$device) {
                return show(config('code.error'), '广告屏不存在', '', 404);
            }
            $data['device_price'] = $device['sale_price'];

            // validate验证数据合法性
            $validate = validate('PartnerOrder');
            if (!$validate->check($data)) {
                return show(config('code.error'), $validate->getError(), '', 403);
            }

            $data1 = [
                'device_id' => $data['device_id'],
                'partner_id' => $data['partner_id'],
                'user_id' => $data['user_id']
            ];
            // validate验证数据合法性
            $validate1 = validate('PartnerDevice');
            if (!$validate1->check($data1)) {
                return show(config('code.error'), $validate1->getError(), '', 403);
            }

            // 入库操作
            // 当订单已支付完成时，同时新增该订单对应的合作商广告屏数据
            if (isset($data['order_status']) && $data['order_status'] == 1 && isset($data['pay_status']) &&  $data['pay_status'] == 1) {
                /* 手动控制事务 s */
                // 启动事务
                Db::startTrans();
                try {
                    // 更新支付状态和订单状态
                    $data['pay_time'] = time();
                    $res[0] = Db::name('partner_order')->insert($data);

                    // 新增该订单对应的合作商广告屏数据
                    $res[1] = Db::name('partner_device')->insert($data1);

                    // 任意一个表写入失败都会抛出异常，TODO：是否可以不做该判断
                    if (in_array(0, $res)) {
                        return show(config('code.error'), '订单新增失败', '', 403);
                    }

                    // 提交事务
                    Db::commit();
                    return show(config('code.success'), '订单已支付完成，合作商广告屏新增成功', $data['order_sn'], 201);
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                    return show(config('code.error'), '网络忙，请重试', '', 500);
                }
                /* 手动控制事务 e */
            } else {
                try {
                    $id = model('PartnerOrder')->add($data, 'order_id');
                } catch (\Exception $e) {
                    return show(config('code.error'), '网络忙，请重试', [], 500); // $e->getMessage()
                }
                if ($id) {
                    return show(config('code.success'), '订单新增成功', $data['order_sn'], 201);
                } else {
                    return show(config('code.error'), '订单新增失败', [], 403);
                }
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }

    /**
     * 显示指定的订单资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            try {
                $data = model('PartnerOrder')->find($id);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', [], 500); // $e->getMessage()
            }

            if ($data) {
                return show(config('code.success'), 'ok', $data);
            } else {
                return show(config('code.error'), 'Not Found', $data, 404);
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }

    /**
     * 保存更新的订单资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        // 判断为PUT请求
        if (request()->isPut()) {
            // 传入的数据
            $param = input('param.');

            // validate验证数据合法性：判断是审核状态还是更新其他数据
            /*$validate = validate('PartnerOrder');
            $rules = [];
            $scene = 'update';
            if (!$validate->check($param, $rules, $scene)) {
                return show(config('code.error'), $validate->getError(), [], 403);
            }*/

            // 判断数据是否存在
            $data = [];
            if (!empty($param['order_price'])) { // 订单价格
                $data['order_price'] = trim($param['order_price']);
            }
            if (isset($param['order_status'])) { // 订单状态
                $data['order_status'] = input('param.order_status', null, 'intval');
            }
            if (isset($param['pay_status'])) { // 支付状态
                $data['pay_status'] = input('param.pay_status', null, 'intval');
                if ($data['pay_status'] == 1) {
                    $data['pay_time'] = time();
                }
            }

            if (empty($data)) {
                return show(config('code.error'), '数据不合法', [], 404);
            }

            // 获取指定的合作商订单
            $partnerOrder = model('PartnerOrder')->find($id);
            $data1 = [
                'device_id' => $partnerOrder['device_id'],
                'partner_id' => $partnerOrder['partner_id'],
                'user_id' => $partnerOrder['user_id']
            ];
            // validate验证数据合法性
            $validate = validate('PartnerDevice');
            if (!$validate->check($data1)) {
                return show(config('code.error'), $validate->getError(), '', 403);
            }

            // 获取广告屏合作商业务员ID
            $userPartner = Db::name('user_partner')->field('salesman_id')->find($partnerOrder['partner_id']);
            $salesmanID = $userPartner['salesman_id'];
            // 获取广告屏合作商业务员原始用户ID
            $userSalesman = Db::name('user_salesman')->field('uid')->find($salesmanID);

            // 当订单已支付完成时，同时新增该订单对应的合作商广告屏数据
            if ($data['order_status'] == 1 && $data['pay_status'] == 1) {
                /* 手动控制事务 s */
                // 启动事务
                Db::startTrans();
                try {
                    // 更新支付状态和订单状态
                    $res[0] = Db::name('partner_order')->where(['order_id' => $id])->update($data);

                    // 新增该订单对应的合作商广告屏数据
                    $res[1] = Db::name('partner_device')->insert($data1);

                    /* 广告屏合作商业务员售卖广告屏提成（规定每台广告屏提成￥50） s */
                    $salesmanCommission = 50;
                    // 更新广告屏合作商业务员余额、收入
                    $res[2] = Db::name('user_salesman')->where(['id' => $salesmanID])->setInc('money', $salesmanCommission);
                    $res[3] = Db::name('user_salesman')->where(['id' => $salesmanID])->setInc('income', $salesmanCommission);
                    // 更新广告屏合作商业务员原始用户余额、收入
                    $res[4] = Db::name('user')->where(['user_id' => $userSalesman['uid']])->setInc('money', $salesmanCommission);
                    $res[5] = Db::name('user')->where(['user_id' => $userSalesman['uid']])->setInc('income', $salesmanCommission);
                    /* 广告屏合作商业务员售卖广告屏提成（规定每台广告屏提成￥50） e */

                    // 任意一个表写入失败都会抛出异常，TODO：是否可以不做该判断
                    if (in_array(0, $res)) {
                        return show(config('code.error'), '订单状态更新失败', '', 403);
                    }

                    // 提交事务
                    Db::commit();
                    return show(config('code.success'), '订单已支付完成，合作商广告屏新增成功', '', 201);
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                    return show(config('code.error'), '网络忙，请重试', '', 500);
                }
                /* 手动控制事务 e */
            } else {
                try {
                    $result = model('PartnerOrder')->save($data, ['order_id' => $id]); // 更新
                } catch (\Exception $e) {
                    return show(config('code.error'), '网络忙，请重试', [], 500); // $e->getMessage()
                }
                if (false === $result) {
                    return show(config('code.error'), '更新失败', [], 403);
                } else {
                    return show(config('code.success'), '更新成功', [], 201);
                }
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }

    /**
     * 删除指定订单资源
     *
     * @param  int $id
     * @return \think\Response
     * @throws ApiException
     */
    public function delete($id)
    {
        // 判断为DELETE请求
        if (request()->isDelete()) {
            // 显示指定的订单资源
            try {
                $data = model('PartnerOrder')->find($id);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', '', 500);
                //throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            // 判断数据是否存在
            if ($data['order_id'] != $id) {
                return show(config('code.error'), '数据不存在', '', 404);
            }

            // 判断删除条件
            // 判断订单状态
            if ($data['order_status'] != 2) { // 未取消
                return show(config('code.error'), '删除失败：订单未取消', '', 403);
            }

            // 软删除
            if ($data['is_delete'] != config('code.is_delete')) {
                // 捕获异常
                try {
                    $result = model('PartnerOrder')->softDelete('order_id', $id);
                } catch (\Exception $e) {
                    throw new ApiException($e->getMessage(), 500, config('code.error'));
                }

                if (!$result) {
                    return show(config('code.error'), '移除失败', '', 403);
                } else {
                    return show(config('code.success'), '移除成功', '');
                }
            }

            // 真删除
            /*try {
                $result = model('PartnerOrder')->destroy($id);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', '', 500);
            }
            if (!$result) {
                return show(config('code.error'), '删除失败', '', 403);
            } else {
                return show(config('code.success'), '删除成功');
            }*/
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }
}
