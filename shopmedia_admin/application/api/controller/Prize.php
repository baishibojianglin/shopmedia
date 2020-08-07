<?php

namespace app\api\controller;
use app\common\lib\IAuth;
use think\Controller;
use think\Request;
use think\Db;

/**
 * api模块客户端广告屏控制器类
 * Class Device
 * @package app\api\controller
 */
class Prize extends Controller
{
    /**
     * 获取中奖奖品信息
     * @return \think\response\Json
     */
    public function getPrize()
    {
        $form = input();

        //获取店铺信息
        $shopMap['s.shop_id'] = $form['shop_id'];
        $shop = Db::name('shop')->alias('s')
            ->field('s.shop_id, s.shop_name, s.address, s.longitude, s.latitude, rp.region_name province, rc.region_name city, rco.region_name county, rt.region_name town')
            ->join('__REGION__ rp', 's.province_id = rp.region_id', 'LEFT') // 区域（省份）
            ->join('__REGION__ rc', 's.city_id = rc.region_id', 'LEFT') // 区域（城市）
            ->join('__REGION__ rco', 's.county_id = rco.region_id', 'LEFT') // 区域（区县）
            ->join('__REGION__ rt', 's.town_id = rt.region_id', 'LEFT') // 区域（乡镇街道）
            ->where($shopMap)
            ->find();
        $data['shop'] = $shop;

        //确定该店铺今日能否再中奖
        // $actRaffleMap['shop_id'] = $form['shop_id'];
        // $shopprize = Db::name('act_raffle')->where($actRaffleMap)->whereTime('raffle_time', 'today')->count();
        // if($shopprize > 3){  //一个店一天最多抽3个奖品
        //     $data['status'] = 0;
        //     return json($data);
        // }

        //设置中奖概率1/2
        $aim=rand(1,2);

        //判断是否能中奖
        if( $aim==2 || $aim==1){ 
            //查询奖品中可用的列表
            $matchprize['status']=1;
            $prizelist=Db::name('act_prize')->field('prize_id')->where($matchprize)->limit(8)->select();
            //随机选择一个奖品
            $num_id=array_rand($prizelist,1);
            $prize=$prizelist[$num_id];
            $matchprizeaim['prize_id']=$prize['prize_id'];
            $prizeaim=Db::name('act_prize')->field('prize_id, act_id, prize_name, sponsor, phone, address, is_sponsor_address')->where($matchprizeaim)->find();
            $data['prize']=$prizeaim;
            $data['num_id']=$num_id;
            $data['status']=1;
            return json($data);
        }else{
            $data['status']=0;
            return json($data);
        }
    }

    /**
     * 记录抽奖信息
     * @return \think\response\Json
     * @throws \think\Exception
     */
    public function recordRaffleLog()
    {
        // 判断为POST请求
        if(!request()->isPost()){
            return show(config('code.error'), '请求不合法', '', 400);
        }

        // 传入的参数
        $data = input('post.');

        // 判断参数是否合法
        if (!isset($data['shop_id']) || empty(trim($data['shop_id'])) || trim($data['shop_id']) == 'null' || trim($data['shop_id']) == '') {
            return show(config('code.error'), '扫描店铺广告屏上二维码参与抽奖', '', 403);
        }
        // 店铺是否存在
        $shop = Db::name('shop')->where(['shop_id' => (int)$data['shop_id'], 'status' => config('code.status_enable')])->find();
        if (empty($shop)) {
            return show(config('code.error'), '店铺被禁用或不存在', '', 404);
        }
        // 是否获取微信用户信息
        if (!isset($data['openid']) || empty($data['openid'])) {
            return show(config('code.error'), '获取微信用户失败', '', 404);
        }
        // 该用户在该店铺今日是否已经抽奖（注意：每个微信用户每天只能在一家店铺抽一次奖）
        $todayRaffleLogCount = Db::name('act_raffle_log')->where(['shop_id' => (int)$data['shop_id'], 'oauth' => 'wx', 'openid' => $data['openid']])->whereTime('raffle_time', 'today')->count();
        if ($todayRaffleLogCount > 0) {
            return show(config('code.error'), '你今日在该店铺已经抽过奖了，明日再来吧', ['today_raffle' => $todayRaffleLogCount], 403);
        }

        $data['raffle_time'] = time(); // 抽奖时间
        $data['oauth'] = 'wx'; // 抽奖者第三方来源
        $res = Db::name('act_raffle_log')->insertGetId($data);
        if ($res) {
            return show(config('code.success'), '已参与抽奖', '', 201);
        } else {
            return show(config('code.error'), '提交失败', '', 403);
        }
    }
    
    /**
     * 提交领奖信息
     * @return \think\response\Json
     */
    public function winnerInfo()
    {
        // 判断为POST请求
        if(!request()->isPost()){
            return show(config('code.error'), '请求不合法', '', 400);
        }

        // 传入的参数
        $param = input('post.');

        // 判断奖品数量是否为零
        $actPrize = Db::name('act_prize')->field('quantity, status')->find($param['prize_id']);
        if ($actPrize['quantity'] <= 0 || $actPrize['status'] != 1) {
            return show(config('code.error'), '很遗憾，奖品已领完！', '', 400);
        }

        // 入库操作
        /* 手动控制事务 s */
        // 启动事务
        Db::startTrans();
        try {
            $time = time(); // 抽奖时间

            // 获取抽奖记录，如果不存在则添加记录
            $todayRaffleLog = Db::name('act_raffle_log')->where(['shop_id' => (int)$param['shop_id'], 'oauth' => 'wx', 'openid' => $param['openid']])->whereTime('raffle_time', 'today')->find();
            if (empty($todayRaffleLog)) {
                $raffleLogData['shop_id'] = (int)$param['shop_id'];
                $raffleLogData['raffle_time'] = $time; // 抽奖时间
                $raffleLogData['oauth'] = 'wx'; // 抽奖者第三方来源
                $raffleLogData['openid'] = $param['openid']; // 抽奖者第三方唯一标识
                $res[0] = Db::name('act_raffle_log')->strict(true)->insertGetId($raffleLogData);
            }

            // 新增中奖者信息
            $raffleData['act_id'] = (int)$param['act_id'];
            $raffleData['prize_id'] = (int)$param['prize_id'];
            $raffleData['prize_name'] = $param['prize_name'];
            $raffleData['shop_id'] = (int)$param['shop_id'];
            $raffleData['raffle_time'] = isset($todayRaffleLog['raffle_time']) ? $todayRaffleLog['raffle_time'] : $time;
            $raffleData['prizewinner'] = isset($param['prizewinner']) ? trim($param['prizewinner']) : trim($param['phone']);
            $raffleData['phone'] = trim($param['phone']);
            $raffleData['oauth'] = 'wx';
            $raffleData['openid'] = $param['openid'];
            $res[1] = $raffleID = Db::name('act_raffle')->strict(true)->insertGetId($raffleData);

            /*// 更新抽奖记录表的中奖状态
            if ($raffleID) {
                $res[3] = Db::name('act_raffle_log')->where(['prize_id' => $param['prize_id']])->update(['status' => 0]);
            }*/

            // 减少活动奖品数量
            $res[2] = Db::name('act_prize')->where(['prize_id' => $param['prize_id']])->setDec('quantity', 1);

            // 获取减少后的奖品信息
            $actPrize1 = Db::name('act_prize')->field('quantity, status')->find($param['prize_id']);
            // 当奖品数量为零时，下架该奖品
            if ($actPrize1['quantity'] <= 0) {
                $res[3] = Db::name('act_prize')->where(['prize_id' => $param['prize_id']])->update(['status' => 0]);
            }


            // 判断 用户表表user 是否存在该用户，如果不存在则创建用户，并将 user_id 绑定到 第三方授权登录表user_oauth
            // 创建用户
            $user = Db::name('user')->where(['phone' => trim($param['phone'])])->find();
            if (empty($user)) {
                $userData['user_name'] = isset($param['prizewinner']) ? trim($param['prizewinner']) : 'sustock-' . trim($param['phone']); // 定义默认用户名
                $userData['phone'] = trim($param['phone']);
                $userData['phone_verified'] = 1; // 手机号已验证
                $userData['password'] = IAuth::encrypt(trim($param['phone']));
                $userData['status'] = config('code.status_enable');
                $userData['create_time'] = time(); // 创建时间
                $userData['create_ip'] = request()->ip(); // 创建IP
                $res[4] = $userId = Db::name('user')->strict(false)->insertGetId($userData); // 新增数据并返回主键值
            } else {
                $userId = $user['user_id'];
            }
            // 将 user_id 绑定到 第三方授权登录表user_oauth
            $userOauth = Db::name('user_oauth')->where(['oauth' => 'wx', 'openid' => $param['openid']])->find();
            if (!$userOauth['user_id'] || $userOauth['user_id'] != $userId) {
                $raffle = Db::name('act_raffle')->where(['oauth' => 'wx', 'openid' => $param['openid']])->find();
                if ($raffle['phone'] == trim($param['phone'])) { // TODO：可以不做该判断，因为入参 phone 是一致的
                    $userOauthData['user_id'] = $userId;
                    $res[5] = Db::name('user_oauth')->where(['oauth' => 'wx', 'openid' => $param['openid']])->update($userOauthData);
                }
            }

            // 任意一个表写入失败都会抛出异常，TODO：是否可以不做该判断
            if (in_array(0, $res)) {
                return show(config('code.error'), '提交失败', '', 403);
            }

            // 提交事务
            Db::commit();
            return show(config('code.success'), '提交成功', '', 201);
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return show(config('code.error'), '请求异常', $e->getMessage(), 500);
        }
    }
}
