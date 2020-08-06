<?php

namespace app\api\controller;

use think\Controller;
use think\Request;
use think\Db;

/**
 * api模块客户端活动中奖纪录控制器类
 * Class ActRaffle
 * @package app\api\controller
 */
class ActRaffle extends AuthBase
{
    /**
     * 统计奖品领取状态数量
     * @return \think\response\Json
     * @throws \think\Exception
     */
    public function rafflePrizeCount()
    {
        // 判断为GET请求
        if (!request()->isGet()) {
            return show(config('code.error'), '请求不合法', '', 400);
        }

        // 传入的参数
        $param = input('param.');
        $shopIds = [];
        if(isset($param['user_id']) && !empty($param['user_id'])) {
            // 获取店铺ID集合
            try {
                $shopIds = Db::name('shop')->where(['user_id' => (int)$param['user_id']])->column('shop_id');
            } catch (\Exception $e) {
                return show(config('code.error'), '请求异常', $e->getMessage(), 500);
            }
        }

        // 统计奖品领取状态数量
        // 未领取
        $map0 = [];
        $map0['prize_status'] = 0;
        if($shopIds) {
            $map0['shop_id'] = ['in', $shopIds];
        }
        $rafflePrizeCount0 = Db::name('act_raffle')->where($map0)->count('prize_status');

        // 已领取
        $map1 = [];
        $map1['prize_status'] = 1;
        if($shopIds) {
            $map1['shop_id'] = ['in', $shopIds];
        }
        $rafflePrizeCount1 = Db::name('act_raffle')->where($map1)->count('prize_status');

        $rafflePrizeCount = ['rafflePrizeCount0' => $rafflePrizeCount0, 'rafflePrizeCount1' => $rafflePrizeCount1];

        return show(config('code.success'), 'OK', $rafflePrizeCount);
    }

    /**
     * 获取奖品领取状态列表
     * @return \think\response\Json
     */
    public function rafflePrizeList()
    {
        // 判断为GET请求
        if (!request()->isGet()) {
            return show(config('code.error'), '请求不合法', '', 400);
        }

        // 传入的参数
        $param = input('param.');

        // 查询条件
        $map = [];
        if (isset($param['prize_status'])) {
            $map['r.prize_status'] = (int)$param['prize_status'];
        }

        $shopIds = [];
        if(isset($param['user_id']) && !empty($param['user_id'])) {
            // 获取店铺ID集合
            try {
                $shopIds = Db::name('shop')->where(['user_id' => (int)$param['user_id']])->column('shop_id');
            } catch (\Exception $e) {
                return show(config('code.error'), '请求异常', $e->getMessage(), 500);
            }
        }
        if($shopIds) {
            $map['r.shop_id'] = ['in', $shopIds];
        }

        // 获取奖品领取状态列表
        $rafflePrizeList = Db::name('act_raffle')->alias('r')
            ->field('r.*, s.shop_name')
            ->join('__SHOP__ s', 's.shop_id = r.shop_id', 'LEFT')
            ->where($map)
            ->select();

        return show(config('code.success'), 'OK', $rafflePrizeList);
    }

    /**
     * 更新领奖状态
     * @return \think\response\Json
     * @throws \think\Exception
     */
    public function updatePrizeStatus()
    {
        // 判断为PUT请求
        if (request()->isPut()) {
            // 传入的参数
            $param = input('param.');

            // 判断数据是否存在
            $data = [];
            if (isset($param['prize_status'])) {
                $data['prize_status'] = (int)$param['prize_status'];
                if ($data['prize_status'] != 1) {
                    $data['prize_status'] = 1;
                }
            }

            if (empty($data)) {
                return show(config('code.error'), '数据不合法', '', 404);
            }

            $res = Db::name('act_raffle')->where(['raffle_id' => (int)$param['raffle_id']])->update($data);
            if ($res) {
                return show(config('code.success'), '奖品发放成功', '', 201);
            }
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }
}