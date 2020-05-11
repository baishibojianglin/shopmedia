<?php

namespace app\api\controller;

use think\Controller;
use think\Db;
use think\Model;
use think\Request;

/**
 * api模块客户端用户（广告屏合作商）订单控制器类
 * Class PartnerOrder
 * @package app\api\controller
 */
class PartnerOrder extends AuthBase
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
        if(request()->isPost()){
            // 传入的参数
            $data = input('post.');

            // 判断数据是否存在
            if (empty($data['user_name'])) {
                return show(config('code.error'), '乙方名称不能为空', '', 401);
            }

            // 处理数据
            $data['order_sn'] = model('PartnerOrder')->getOrderSn(); // 生成唯一订单编号 order_sn
            $data['order_time'] = time();
            //$data['order_status'] = 0;
            $data['order_price'] = $data['party_b_investment']; // 订单价格 == 乙方出资金额
            $data['party_b_name'] = trim($data['user_name']); // 合同乙方名称
            $data['party_a_share'] = $data['party_a_share'] / 100; // 广告屏甲方占股
            $data['party_b_share'] = $data['party_b_share'] / 100; // 广告屏乙方占股

            // 获取广告屏合作商信息
            $partnerMap = []; // 查询条件
            if (isset($data['user_id']) && isset($data['role_id']) && intval($data['role_id']) == 2) {
                $partnerMap['user_id'] = intval($data['user_id']);
                $partnerMap['role_id'] = intval($data['role_id']);
                $partner = Db::name('user_partner')->field('id')->where($partnerMap)->find();
                $data['partner_id'] = $partner['id'];
            }

            // validate验证数据合法性
            $validate = validate('PartnerOrder');
            if (!$validate->check($data)) {
                return show(config('code.error'), $validate->getError(), '', 403);
            }

            // 入库操作
            if ($data['partner_id']) { // 广告屏合作商存在时，只创建订单
                try {
                    // 新增订单
                    $id = Db::name('partner_order')->strict(false)->insertGetId($data);
                    // 更新用户名称为签约名称
                    //@model('User')->allowField(true)->save(['user_name' => trim($data['user_name'])], ['user_id' => intval($data['user_id'])]);
                } catch (\Exception $e) {
                    return show(config('code.error'), $e->getCode().'网络忙，请重试'.$e->getMessage(), '', 500); // $e->getMessage()
                }
                if ($id) {
                    return show(config('code.success'), '下单成功', $data['order_sn'], 201);
                } else {
                    return show(config('code.error'), '下单失败', '', 403);
                }
            } else { // 广告屏合作商不存在时，同时创建广告屏合作商和订单
                /* 手动控制事务 s */
                // 启动事务
                Db::startTrans();
                try {
                    // 创建广告屏合作商
                    $salesman = Db::name('user_salesman')->field('id')->where(['role_id' => 4, 'company_id' => 0, 'parent_id' => 0])->find(); // 获取广告屏合作商业务员
                    $partnerData['user_id'] = intval($data['user_id']);
                    $partnerData['salesman_id'] = $salesman['id'];
                    $partnerData['role_id'] = 2;
                    $partnerData['status'] = config('code.status_enable');
                    $partnerData['create_time'] = time();
                    $res[0] = $partnerID = Db::name('user_partner')->strict(true)->insertGetId($partnerData);

                    // 创建订单
                    // 新增订单
                    $data['partner_id'] = $partnerID;
                    $res[1] = Db::name('partner_order')->strict(false)->insertGetId($data);
                    // 更新用户名称为签约名称
                    //@model('User')->allowField(true)->save(['user_name' => trim($data['user_name'])], ['user_id' => intval($data['user_id'])]);

                    // 任意一个表写入失败都会抛出异常，TODO：是否可以不做该判断
                    if (in_array(0, $res)) {
                        return show(config('code.error'), '下单失败', '', 403);
                    }

                    // 提交事务
                    Db::commit();
                    return show(config('code.success'), '下单成功', '', 201);
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                    return show(config('code.error'), '网络忙，请重试', '', 500);
                }
                /* 手动控制事务 e */
            }
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
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
            // 传入的参数
            $param = input('param.');

            // 查询条件
            $map = [];
            if (isset($param['device_id']) && $param['device_id'] != 0) {
                // 传入广告屏合作商ID
                if (isset($param['partner_id']) && $param['partner_id'] != 0) {
                    $map['partner_id'] = intval($param['partner_id']);
                    $map['device_id'] = intval($param['device_id']);
                }
                // 传入广告屏合作商所属用户ID
                if (isset($param['user_id']) && $param['user_id'] != 0) {
                    $map['user_id'] = intval($param['user_id']);
                    $map['device_id'] = intval($param['device_id']);
                }
            }

            try {
                if (!empty($id)) {
                    $data = Db::name('partner_order')->find($id);
                }
                if ($map) {
                    $data = Db::name('partner_order')->where($map)->find();
                }
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', '', 500); // $e->getMessage()
            }

            if ($data) {
                // 处理数据
                $data['order_time'] = date('Y-m-d H:i:s', $data['order_time']); // 下单时间
                return show(config('code.success'), 'ok', $data);
            } else {
                return show(config('code.error'), 'Not Found', $data, 404);
            }
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }
}
