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
        $actRaffleIds = [];
        if(isset($param['user_id']) && !empty($param['user_id'])) {
            // 获取设备ID集合
            try {
                $deviceIds = Db::name('device')->alias('d')
                    ->join('__SHOP__ s', 's.shop_id = d.shop_id')
                    ->where(['s.user_id' => (int)$param['user_id']])
                    ->column('device_id');
            } catch (\Exception $e) {
                return show(config('code.error'), '请求异常', $e->getMessage(), 500);
            }

            $actRaffleIds0 = [];
            if ($deviceIds) {
                $actRaffleMap0 = ['device_id' => ['in', $deviceIds]];
                $actRaffleIds0 = Db::name('act_raffle')
                    ->where($actRaffleMap0)
                    ->column('raffle_id');
            }

            // 获取到赞助商店铺领取奖品的中奖纪录
            $actRaffleMap1 = [
                'ap.is_sponsor_address' => 1,
                's.user_id' => (int)$param['user_id']
            ];
            $actRaffleIds1 = Db::name('act_raffle')->alias('ar')
                //->field('ar.raffle_id, ap.shop_id, ap.is_sponsor_address')
                ->join('__ACT_PRIZE__ ap', 'ap.prize_id = ar.prize_id')
                ->join('__SHOP__ s', 's.shop_id = ap.shop_id')
                ->where($actRaffleMap1)
                ->column('ar.raffle_id');

            $actRaffleIds = array_reduce([$actRaffleIds0, $actRaffleIds1], 'array_merge', array());
        }

        // 统计奖品领取状态数量
        // 未领取
        $map0 = [];
        $map0['prize_status'] = 0;
        $map0['raffle_id'] = ['in', $actRaffleIds];
        $rafflePrizeCount0 = Db::name('act_raffle')->where($map0)->count('prize_status');

        // 已领取
        $map1 = [];
        $map1['prize_status'] = 1;
        $map1['raffle_id'] = ['in', $actRaffleIds];
        $rafflePrizeCount1 = Db::name('act_raffle')->where($map1)->count('prize_status');

        $rafflePrizeCount = [
            'rafflePrizeCount0' => isset($rafflePrizeCount0) ? $rafflePrizeCount0 : 0,
            'rafflePrizeCount1' => isset($rafflePrizeCount1) ? $rafflePrizeCount1 : 0
        ];

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

        $deviceIds = [];
        $actRaffleIds = [];
        if(isset($param['user_id']) && !empty($param['user_id'])) {
            // 获取设备ID集合
            try {
                $deviceIds = Db::name('device')->alias('d')
                    ->join('__SHOP__ s', 's.shop_id = d.shop_id')
                    ->where(['s.user_id' => (int)$param['user_id']])
                    ->column('device_id');
            } catch (\Exception $e) {
                return show(config('code.error'), '请求异常', $e->getMessage(), 500);
            }

            $actRaffleIds0 = [];
            if ($deviceIds) {
                $actRaffleMap0 = ['device_id' => ['in', $deviceIds]];
                $actRaffleIds0 = Db::name('act_raffle')
                    ->where($actRaffleMap0)
                    ->column('raffle_id');
            }

            // 获取到赞助商店铺领取奖品的中奖纪录
            $actRaffleMap1 = [
                'ap.is_sponsor_address' => 1,
                's.user_id' => (int)$param['user_id']
            ];
            $actRaffleIds1 = Db::name('act_raffle')->alias('ar')
                //->field('ar.raffle_id, ap.shop_id, ap.is_sponsor_address')
                ->join('__ACT_PRIZE__ ap', 'ap.prize_id = ar.prize_id')
                ->join('__SHOP__ s', 's.shop_id = ap.shop_id')
                ->where($actRaffleMap1)
                ->column('ar.raffle_id');

            $actRaffleIds = array_reduce([$actRaffleIds0, $actRaffleIds1], 'array_merge', array());
        }

        if ($actRaffleIds) {
            $map['r.raffle_id'] = ['in', $actRaffleIds];
        }

        // 获取奖品领取状态列表
        $rafflePrizeList = Db::name('act_raffle')->alias('r')
            ->field('r.*, s.shop_name, p.prize_type, p.prize_pic, p.quantity, p.is_sponsor_address')
            ->join('__DEVICE__ d', 'd.device_id = r.device_id', 'LEFT')
            ->join('__SHOP__ s', 's.shop_id = d.shop_id', 'LEFT')
            ->join('__ACT_PRIZE__ p', 'p.prize_id = r.prize_id', 'LEFT')
            ->where($map)
            ->select();

        // 处理数据
        foreach($rafflePrizeList as $key => $value) {
            $rafflePrizeList[$key]['raffle_time'] = isset($value['raffle_time']) ? date('Y-m-d H:i:s', $value['raffle_time']) : '';
        }

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
            $raffleId = (int)$param['raffle_id']; // 中奖纪录ID

            // 获取活动中奖纪录
            $actRaffle = Db::name('act_raffle')->alias('ar')
                ->field('ar.raffle_id, ar.prize_id, ar.phone, ap.prize_type, ap.prize_name, ap.quantity, s.user_id shop_user_id')
                ->join('__ACT_PRIZE__ ap', 'ap.prize_id = ar.prize_id')
                ->join('__DEVICE__ d', 'd.device_id = ar.device_id')
                ->join('__SHOP__ s', 's.shop_id = d.shop_id')
                ->find($raffleId);

            // 获取用户信息
            $user = Db::name('user')->field('user_id')->where(['phone' => $actRaffle['phone']])->find();
            $userId = $user['user_id'];

            // 入库操作
            /* 手动控制事务 s */
            // 启动事务
            Db::startTrans();
            try {
                $res = [];

                // 当奖品为积分时（prize_type == 3），不减少活动奖品（积分）数量，在店家发放积分奖品时，调整用户表 user 积分信息，记录用户积分变动日志 user_integrals_log
                if ($actRaffle['prize_type'] == 3) {
                    // 调整用户表 user 积分信息
                    $changeIntegrals = $actRaffle['quantity'];
                    $res[0] = Db::name('user')->where(['user_id' => $userId])->setInc('get_integrals', $changeIntegrals); // 用户累计获得积分
                    $res[1] = Db::name('user')->where(['user_id' => $userId])->setInc('rest_integrals', $changeIntegrals); // 剩余积分

                    // 记录用户积分变动日志 user_integrals_log
                    // 获取变动前最新用户积分变动日志
                    $userIntegralsLog = Db::name('user_integrals_log')->where(['user_id' => $userId])->limit(1)->order('log_time desc')->select();
                    $beforeIntegrals = isset($userIntegralsLog[0]['after_integrals']) ? $userIntegralsLog[0]['after_integrals'] : 0; // 变动前积分
                    $userIntegralsLogData['user_id'] = $userId;
                    $userIntegralsLogData['change_integrals'] = $changeIntegrals; // 变动积分
                    $userIntegralsLogData['before_integrals'] = $beforeIntegrals; // 变动前积分
                    $userIntegralsLogData['after_integrals'] = $beforeIntegrals + $changeIntegrals; // 变动后积分
                    $userIntegralsLogData['log_content'] = '微信扫码中奖，店家发放积分'; // 日志内容
                    $userIntegralsLogData['log_time'] = time(); // 积分变动时间
                    $userIntegralsLogData['operator_type'] = 2; // 操作人类型：0用户，1平台管理员，2店家
                    $userIntegralsLogData['operator_id'] = $actRaffle['shop_user_id']; // 操作人对应用户ID
                    $res[2] = Db::name('user_integrals_log')->insert($userIntegralsLogData);
                }

                // 更新领奖状态
                $raffleData = [];
                if (isset($param['prize_status'])) {
                    $raffleData['prize_status'] = (int)$param['prize_status'];
                    if ($raffleData['prize_status'] != 1) {
                        $raffleData['prize_status'] = 1;
                    }
                }
                if (empty($raffleData)) {
                    return show(config('code.error'), '数据不合法', '', 404);
                }
                $res[3] = Db::name('act_raffle')->where(['raffle_id' => (int)$param['raffle_id']])->update($raffleData);

                // 任意一个表写入失败都会抛出异常，TODO：是否可以不做该判断
                if (in_array(0, $res)) {
                    return show(config('code.error'), '提交失败', $res, 403);
                }

                // 提交事务
                Db::commit();
                return show(config('code.success'), '奖品发放成功', '', 201);
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return show(config('code.error'), '请求异常', $e->getMessage(), 500);
            }
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }
}