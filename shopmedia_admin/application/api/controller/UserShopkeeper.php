<?php

namespace app\api\controller;

use think\Controller;
use think\Request;
use think\Db;

/**
 * api模块客户端用户（店家）控制器类
 * Class UserShopkeeper
 * @package app\api\controller
 */
class UserShopkeeper extends AuthBase
{
    /**
     * 获取账号店铺合作角色是否可用
     * @return \think\response\Json
     */
    public function shopRole(){
        $form=input();
        $match['user_id']=$form['user_id'];
        $userlist=Db::name('user_shopkeeper')->where($match)->field('status')->find();
        if(!empty($userlist)){
            $message['status']=1;
            $message['data']=$userlist;
            return json($message);
        }else{
            $message['status']=0;
            $message['words']='获取失败';
            return json($message);
        }
    }

    /**
     * 显示店家店铺列表
     * @return \think\response\Json
     */
    public function shopList()
    {
       $form=input();
       $matchuserid['user_id']=$form['user_id'];
       $shoplist=Db::name('shop')->where($matchuserid)->field('shop_id,shop_name,address,party_b_signature,longitude,latitude')->select();//查询用户的店铺列表
       $data=[];//初始化返回前台数据
       if(!empty($shoplist)){   
           foreach ($shoplist as $key => $value) {  //每个店铺类的设备列表
               $matchshop['shop_id']=$value['shop_id'];
               $devicelist= Db::name('device')->where($matchshop)->field('device_id,today_income,total_income,sale_price')->select();
               $list['shop']=$value;//店铺信息
               $list['device']=$devicelist;//设备信息
               array_push($data,$list);
           }  
        }    
       return json($data); 
    }
}