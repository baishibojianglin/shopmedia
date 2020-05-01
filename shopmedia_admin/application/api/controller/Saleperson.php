<?php

namespace app\api\controller;

use app\common\lib\Aes;
use app\common\lib\exception\ApiException;
use app\common\lib\IAuth;
use think\Controller;
use think\Request;
use think\Db;

/**
 * api模块客户端用户控制器类
 * Class User
 * @package app\api\controller\v1
 */

class Saleperson extends AuthBase
{
    /**
     * 获取广告屏位置信息
     * @return \think\response\Json
     */
    public function getMarkers(){
        $devicelist=Db::name('device')->select();
        
        if(!empty($devicelist)){
            $message['data']=$devicelist;
            $message['status']=1;
            $message['words']='获取成功';
        }else{
            $message['status']=0;
            $message['words']='获取失败';
        }
        return json($message);
    }

    /**
     * 获取广告屏详细信息
     * @return \think\response\Json
     */
    public function DeviceDetail(){
        $form=input();
        $match['device_id']=$form['device_id'];
        $devicelist=Db::name('device')->where($match)->find();
        
        if(!empty($devicelist)){
            $message['data']=$devicelist;
            $message['status']=1;
            $message['words']='获取成功';
        }else{
            $message['status']=0;
            $message['words']='获取失败';
        }
        return json($message);
    }

    /**
     * 店铺类别列表（不分页，用于 Select 选择器等）
     * @return \think\response\Json
     */
    public function shopCateList()
    {
        $shopCate = config('code.shop_cate'); // 店铺类别
        $data = []; // 定义二维数组列表
        // 处理数据，将一维数组转成二维数组
        foreach ($shopCate as $key => $value) {
            $data[$key]['cate_id'] = $key;
            $data[$key]['cate_name'] = $value;
        }
        return show(config('code.success'), 'OK', $data);
    }

}