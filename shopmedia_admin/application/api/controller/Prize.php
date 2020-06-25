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
        $form=input();
        //获取店铺信息
        $matchshop['shop_id']=$form['shop_id'];
        $shoplist=Db::name('shop')->where($matchshop)->field('shop_name')->find();
        $data['shop_name']=$shoplist['shop_name'];

        //确定该店铺今日能否再中奖
        $shopprize=Db::name('act_raffle')->where($matchshop)->whereTime('raffle_time', 'today')->count();
        if($shopprize>3){  //一个店一天最多抽3个奖品
            $data['status']=0;
            return json($data);
        }
        

        //设置中奖概率1/5
        $aim=rand(1,2);

        //判断是否能中奖
        if( $aim==2){ 
            //查询奖品中可用的列表
            $matchprize['status']=1;
            $prizelist=Db::name('act_prize')->where($matchprize)->field('prize_id')->select(); 
            //随机选择一个奖品
            $prize=$prizelist[array_rand($prizelist,1)]; 
            $matchprizeaim['prize_id']=$prize['prize_id'];
            $prizeaim=Db::name('act_prize')->where($matchprizeaim)->field('prize_id,prize_name,sponsor')->find(); 
            $data['prize']=$prizeaim;
            $data['status']=1;
            return json($data);
        }else{
            $data['status']=0;
            return json($data);
        }

        

    }





}
