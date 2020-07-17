<?php

namespace app\api\controller;
use think\Controller;
use think\Request;
use think\Db;


class Adcase extends Controller
{
    /**
     * 获取广告案例列表
     * @return \think\response\Json
     */
    public function getCase()
    {
       
       $match['status']=1;
       $adcase= Db::name('ad_case')->where($match)->select();
       return json($adcase);


    }

 
}
