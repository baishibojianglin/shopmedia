<?php

namespace app\api\controller;

use think\Controller;
use think\Request;

/**
 * api模块店铺类别管理控制器类
 * Class shopCate
 * @package app\api\controller
 */
class shopCate extends Common
{
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
            $data[] = ['cate_id' => $key, 'cate_name' => $value];
        }

        return show(config('code.success'), 'OK', $data);
    }

    /**
     * 店铺环境列表（不分页，用于 Select 选择器等）
     * @return \think\response\Json
     */
    public function shopEnviroment()
    {
        $shopEnviroment= config('code.shop_enviroment'); // 店铺类别
        return show(config('code.success'), 'OK', $shopEnviroment);
    }


}