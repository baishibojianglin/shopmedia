<?php

namespace app\api\controller;
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
        $actRaffleMap['shop_id'] = $form['shop_id'];
        $shopprize = Db::name('act_raffle')->where($actRaffleMap)->whereTime('raffle_time', 'today')->count();
        if($shopprize > 3){  //一个店一天最多抽3个奖品
            $data['status'] = 0;
            return json($data);
        }

        //设置中奖概率1/5
        $aim=rand(1,2);

        //判断是否能中奖
        if( $aim==2){ 
            //查询奖品中可用的列表
            $matchprize['status']=1;
            $prizelist=Db::name('act_prize')->field('prize_id')->where($matchprize)->select();
            //随机选择一个奖品
            $prize=$prizelist[array_rand($prizelist,1)];
            $matchprizeaim['prize_id']=$prize['prize_id'];
            $prizeaim=Db::name('act_prize')->field('prize_id, act_id, prize_name, sponsor')->where($matchprizeaim)->find();
            $data['prize']=$prizeaim;
            $data['status']=1;
            return json($data);
        }else{
            $data['status']=0;
            return json($data);
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
        $data = input('post.');

        // 判断奖品数量是否为零
        $actPrize = Db::name('act_prize')->field('quantity, status')->find($data['prize_id']);
        if ($actPrize['quantity'] <= 0 || $actPrize['status'] != 1) {
            return show(config('code.error'), '很遗憾，奖品已领完！', '', 400);
        }

        // 入库操作
        /* 手动控制事务 s */
        // 启动事务
        Db::startTrans();
        try {
            // 新增中奖者信息
            $data['raffle_time'] = time();
            $data['prizewinner'] = $data['phone'];
            $res[0] = $raffleID = Db::name('act_raffle')->strict(true)->insertGetId($data);

            // 减少活动奖品数量
            $res[1] = Db::name('act_prize')->where(['prize_id' => $data['prize_id']])->setDec('quantity', 1);

            // 获取减少后的奖品信息
            $actPrize1 = Db::name('act_prize')->field('quantity, status')->find($data['prize_id']);
            // 当奖品数量为零时，下架该奖品
            if ($actPrize1['quantity'] <= 0) {
                $res[2] = Db::name('act_prize')->where(['prize_id' => $data['prize_id']])->update(['status' => 0]);
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
            return show(config('code.error'), '请求异常', '', 500);
        }
    }
}
