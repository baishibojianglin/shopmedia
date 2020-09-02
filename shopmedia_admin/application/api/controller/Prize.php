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
        $param = input();

        // 确定该店铺今日能否再中奖
        // $actRaffleMap['shop_id'] = $param['shop_id'];
        // $shopprize = Db::name('act_raffle')->where($actRaffleMap)->whereTime('raffle_time', 'today')->count();
        // if($shopprize > 3){  //一个店一天最多抽3个奖品
        //     $data['status'] = 0;
        //     return json($data);
        // }

        // 一台广告屏最多抽中6个奖品（不限制奖品种类和抽奖时间）
        /*$actRaffleMap['device_id'] = $param['device_id'];
        $actRaffleCount = Db::name('act_raffle')->where($actRaffleMap)->count();
        if($actRaffleCount > 6){
            $data['status'] = 0;
            return json($data);
        }*/

        // 当奖品数量为零时，不再抽中奖品
        $actPrizeMap['prize_type'] = 1;
        $prizeSum = Db::name('act_prize')->where($actPrizeMap)->sum('quantity');
        if($prizeSum <= 0){
            $data['status'] = 0;
            return json($data);
        }

        // 首次关注微信公众号参与抽奖时必中奖：通过判断openid是否已经存在中奖记录表里，不存在则让该用户一定中奖（20200825）
        $actPrizeCount = 1;
        if (isset($param['openid']) && !empty($param['openid'])) {
            // 判断该微信用户曾经是否中奖
            $actPrizeCount = Db::name('act_raffle')->where(['openid' => $param['openid']])->count('openid');
        }

        // 设置中奖概率1/5
        $aim = rand(1,5);

        // 判断是否能中奖
        if($aim == 2 || $actPrizeCount == 0){
            // 根据传入的设备 device_id 获取店铺 shop_id，然后判断该店铺是否提供只能在本店铺抽奖、领奖的奖品
            $device = Db::name('device')->field('device_id, shop_id')->find($param['device_id']);
            if (!empty($device) && $device['shop_id']) {
                $prizeMap0 = [
                    'shop_id' => $device['shop_id'],
                    'is_sponsor_address' => 1,
                    'is_sponsor_raffle' => 1,
                    'status' => 1
                ];
                $prize0 = Db::name('act_prize')->where($prizeMap0)->find();
            }

            // 查询奖品中可用的列表
            if (isset($prize0) && !empty($prize0)) { // 含只能在本店铺抽奖的奖品
                $matchprize['status'] = 1;
                $matchprize['is_sponsor_raffle'] = 0;
                $prizelist = Db::name('act_prize')->field('prize_id')->where($matchprize)->order('sort', 'asc')->limit(7)->select();
                array_push($prizelist, $prize0);
            } else { // 不含只能在本店铺抽奖的奖品
                $matchprize['status'] = 1;
                $matchprize['is_sponsor_raffle'] = 0;
                $prizelist = Db::name('act_prize')->field('prize_id')->where($matchprize)->order('sort', 'asc')->limit(8)->select();
            }

            // 随机选择一个奖品
            $num_id = array_rand($prizelist, 1);
            $prize = $prizelist[$num_id];
            $matchprizeaim['prize_id'] = $prize['prize_id'];
            $prizeaim = Db::name('act_prize')->field('prize_id, act_id, prize_name, sponsor, phone, shop_id, address, is_sponsor_address')->where($matchprizeaim)->find();
            $data['prize'] = $prizeaim;
            $data['num_id'] = $num_id;
            $data['status'] = 1;
        }else{ // 只抽中积分
            $matchprizeaim['prize_id'] = 7; // TODO：必须为积分类型奖品的ID或ID集合
            $matchprizeaim['prize_type'] = 3; // 奖品类型：3积分
            $prizeaim = Db::name('act_prize')->field('prize_id, act_id, prize_name, sponsor, phone, shop_id, address, is_sponsor_address')->where($matchprizeaim)->find();
            $data['prize'] = $prizeaim;
            $data['num_id'] = 6; // TODO：必须与客户端积分位置一致，从0开始计数
            $data['status'] = 1;
        }

        // 获取店铺信息
        // 判断是否到赞助商处领奖
        if (!empty($data['prize']) && $data['prize']['is_sponsor_address'] == 1 && $data['prize']['shop_id']) {
            $shopMap['s.shop_id'] = $data['prize']['shop_id'];
        } else {
            $shopMap['d.device_id'] = $param['device_id'];
        }

        $shop = Db::name('shop')->alias('s')
            ->field('s.shop_id, s.shop_name, s.address, s.longitude, s.latitude, rp.region_name province, rc.region_name city, rco.region_name county, rt.region_name town')
            ->join('__DEVICE__ d', 'd.shop_id = s.shop_id', 'LEFT') // 广告屏设备
            ->join('__REGION__ rp', 's.province_id = rp.region_id', 'LEFT') // 区域（省份）
            ->join('__REGION__ rc', 's.city_id = rc.region_id', 'LEFT') // 区域（城市）
            ->join('__REGION__ rco', 's.county_id = rco.region_id', 'LEFT') // 区域（区县）
            ->join('__REGION__ rt', 's.town_id = rt.region_id', 'LEFT') // 区域（乡镇街道）
            ->where($shopMap)
            ->find();
        $data['shop'] = $shop;

        return json($data);
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
        if (!isset($data['device_id']) || empty(trim($data['device_id'])) || trim($data['device_id']) == 'null' || trim($data['device_id']) == '') {
            return show(config('code.error'), '打开微信扫一扫店铺广告屏上二维码参与抽奖', '', 403);
        }
        if (!isset($data['create_time']) || date('Y-m-d') != date('Y-m-d', $data['create_time'])) {
            return show(config('code.error'), '链接已过期，请重新扫描店铺广告屏上二维码参与抽奖', '', 403);
        }
        // 设备是否存在
        $device = Db::name('device')->where(['device_id' => (int)$data['device_id'], 'status' => config('code.status_enable')])->find();
        if (empty($device)) {
            return show(config('code.error'), '设备故障、下线或不存在', '', 404);
        }
        // 是否获取微信用户信息
        if (!isset($data['openid']) || empty($data['openid'])) {
            return show(config('code.error'), '获取微信用户失败', '', 404);
        }
        // 该用户在该店铺今日是否已经抽奖（注意：每个微信用户每天只能在一家店铺抽一次奖）
        $todayRaffleLogCount = Db::name('act_raffle_log')->where(['device_id' => (int)$data['device_id'], 'oauth' => 'wx', 'openid' => $data['openid']])->whereTime('raffle_time', 'today')->count();
        if ($todayRaffleLogCount > 0) {
            return show(config('code.error'), '你今日在该店铺广告屏已经抽过奖了，请扫描其他店铺广告屏。每天都可以参与哦', ['today_raffle' => $todayRaffleLogCount], 403);
        }

        $data['raffle_time'] = time(); // 抽奖时间
        $data['oauth'] = 'wx'; // 抽奖者第三方来源
        $res = Db::name('act_raffle_log')->strict(false)->insertGetId($data);
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
        $shopId = (int)$param['shop_id'];
        $deviceId = (int)$param['device_id'];
        $prizeId = (int)$param['prize_id'];

        // 获取奖品信息
        $actPrize = Db::name('act_prize')->field('prize_type, quantity, status')->find($prizeId);
        // 判断奖品数量是否为零
        if ($actPrize['quantity'] <= 0 || $actPrize['status'] != 1) {
            return show(config('code.error'), '很遗憾，奖品已领完！', '', 400);
        }

        // 获取广告主业务员
        $shop = Db::name('shop')->alias('s')
            ->field('s.shopkeeper_id, us.salesman_id, usm.company_id')
            ->join('__USER_SHOPKEEPER__ us', 'us.id = s.shopkeeper_id')
            ->join('__USER_SALESMAN__ usm', 'usm.id = us.salesman_id')
            ->where(['s.shop_id' => $shopId])
            ->find();
        if ($shop) {
            $salesman = Db::name('user_salesman')->field('id')->where(['company_id' => $shop['company_id'], 'parent_id' => 0, 'role_id' => 5])->find();
        }

        // 入库操作
        /* 手动控制事务 s */
        // 启动事务
        Db::startTrans();
        try {
            $time = time(); // 抽奖时间

            // 获取抽奖记录，如果不存在则添加记录
            $todayRaffleLog = Db::name('act_raffle_log')->where(['device_id' => $deviceId, 'oauth' => 'wx', 'openid' => $param['openid']])->whereTime('raffle_time', 'today')->find();
            if (empty($todayRaffleLog)) {
                $raffleLogData['device_id'] = $deviceId;
                $raffleLogData['raffle_time'] = $time; // 抽奖时间
                $raffleLogData['oauth'] = 'wx'; // 抽奖者第三方来源
                $raffleLogData['openid'] = $param['openid']; // 抽奖者第三方唯一标识
                $res[0] = $raffleLogId = Db::name('act_raffle_log')->strict(true)->insertGetId($raffleLogData);
            } else {
                $raffleLogId = $todayRaffleLog['raffle_log_id'];
            }

            // 一个用户一天只能在一台广告机设备抽一次奖，当然最多抽中一次奖
            $todayRaffle = Db::name('act_raffle')->where(['device_id' => $deviceId, 'oauth' => 'wx', 'openid' => $param['openid']])->whereTime('raffle_time', 'today')->find();
            if (empty($todayRaffle)) {
                // 新增中奖者信息
                $raffleData['act_id'] = (int)$param['act_id'];
                $raffleData['prize_id'] = $prizeId;
                $raffleData['prize_name'] = $param['prize_name'];
                $raffleData['device_id'] = $deviceId;
                $raffleData['raffle_time'] = isset($todayRaffleLog['raffle_time']) ? $todayRaffleLog['raffle_time'] : $time;
                $raffleData['prizewinner'] = trim($param['phone']); // isset($param['prizewinner']) ? trim($param['prizewinner']) : trim($param['phone']);
                $raffleData['phone'] = trim($param['phone']);
                $raffleData['oauth'] = 'wx';
                $raffleData['openid'] = $param['openid'];
                $res[1] = $raffleID = Db::name('act_raffle')->strict(true)->insertGetId($raffleData);
            } else {
                return show(config('code.error'), '你今日在该店铺广告屏已经抽过奖了，请扫描其他店铺广告屏。每天都可以参与哦', ['today_raffle' => $todayRaffle], 403);
            }

            // 更新抽奖记录表的中奖状态
            if ($raffleID) {
                $res[2] = Db::name('act_raffle_log')->where(['raffle_log_id' => $raffleLogId])->update(['raffle_id' => $raffleID]);
            }

            // 根据奖品类型，做相应操作：①当奖品为实物时，减少活动奖品（实物）数量；②当奖品为积分时（$actPrize['prize_type'] == 3），不减少活动奖品（积分）数量，TODO：在店家发放积分奖品时（api/ActRaffle/updatePrizeStatus方法），调整用户表 user 积分信息，记录用户积分变动日志 user_integrals_log
            // ①当奖品为实物时，减少活动奖品（实物）数量
            if ($actPrize['prize_type'] == 1) {
                $res[3] = Db::name('act_prize')->where(['prize_id' => $prizeId])->setDec('quantity', 1);

                // 获取减少后的奖品信息
                $actPrize1 = Db::name('act_prize')->field('quantity, status')->find($prizeId);
                // 当奖品数量为零时，下架该奖品
                if ($actPrize1['quantity'] <= 0) {
                    $res[4] = Db::name('act_prize')->where(['prize_id' => $prizeId])->update(['status' => 0]);
                }
            }


            // 根据传入的中奖电话号码获取用户信息
            $user = Db::name('user')->where(['phone' => trim($param['phone'])])->find();
            // 判断 用户表表user 是否存在该用户，如果不存在则创建用户，并将 user_id 绑定到 第三方授权登录表user_oauth
            // 创建用户
            if (empty($user)) {
                $userData['user_name'] = trim($param['phone']); // isset($param['prizewinner']) ? trim($param['prizewinner']) : 'sustock-' . trim($param['phone']); // 定义默认用户名
                $userData['role_ids'] = 7; // 用户角色ID
                $userData['avatar'] = isset($param['headimgurl']) ? $param['headimgurl'] : '';
                $userData['phone'] = trim($param['phone']);
                $userData['phone_verified'] = 1; // 手机号已验证
                $userData['password'] = IAuth::encrypt(trim($param['phone']));
                $userData['status'] = config('code.status_enable');
                $userData['create_time'] = time(); // 创建时间
                $userData['create_ip'] = request()->ip(); // 创建IP
                $res[5] = $userId = Db::name('user')->strict(false)->insertGetId($userData); // 新增数据并返回主键值

                // 默认创建广告主角色
                $advertiserData['user_id'] = $userId;
                $advertiserData['salesman_id'] = isset($salesman['id']) ? $salesman['id'] : 0; // TODO：业务员ID
                $advertiserData['status'] = config('code.status_enable');
                $advertiserData['create_time'] = time(); // 创建时间
                $res[6] = Db::name('user_advertiser')->insert($advertiserData);
            } else {
                $userId = $user['user_id'];
                $updateUserData['avatar'] = isset($param['headimgurl']) ? $param['headimgurl'] : '';
                $res[7] = Db::name('user')->where(['user_id' => $userId])->update($updateUserData) === false ? 0 : true;

                // 如果广告主不存在则创建
                $advertiser = Db::name('user_advertiser')->where(['user_id' => $userId])->find();
                if (empty($advertiser)) {
                    $advertiserData['user_id'] = $userId;
                    $advertiserData['salesman_id'] = isset($salesman['id']) ? $salesman['id'] : 0; // TODO：业务员ID
                    $advertiserData['status'] = config('code.status_enable');
                    $advertiserData['create_time'] = time(); // 创建时间
                    $res[8] = Db::name('user_advertiser')->insert($advertiserData);
                }
            }
            // 将 user_id 绑定到 第三方授权登录表user_oauth
            $userOauth = Db::name('user_oauth')->where(['oauth' => 'wx', 'openid' => $param['openid']])->find();
            if (!$userOauth['user_id'] || $userOauth['user_id'] != $userId) {
                $raffle = Db::name('act_raffle')->where(['oauth' => 'wx', 'openid' => $param['openid']])->find();
                if ($raffle['phone'] == trim($param['phone'])) { // TODO：可以不做该判断，因为入参 phone 是一致的
                    $userOauthData['user_id'] = $userId;
                    $res[9] = Db::name('user_oauth')->where(['oauth' => 'wx', 'openid' => $param['openid']])->update($userOauthData);
                }
            }


            // 任意一个表写入失败都会抛出异常，TODO：是否可以不做该判断
            if (in_array(0, $res)) {
                return show(config('code.error'), '提交失败', $res, 403);
            }

            // 提交事务
            Db::commit();
            return show(config('code.success'), '提交成功', '', 201);
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return show(config('code.error'), '请求异常' . $e->getMessage(), $e->getMessage(), 500);
        }
    }
}
