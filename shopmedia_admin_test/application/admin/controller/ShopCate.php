<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

/**
 * admin模块店铺类别管理控制器类
 * Class shopCate
 * @package app\admin\controller
 */
class shopCate extends Base
{
    /**
     * 显示店铺类别资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        
    }

    /**
     * 店铺类别列表（不分页，用于 Select 选择器等）
     * @return \think\response\Json
     */
    public function shopCateList()
    {
        $shopCate = config('ad.ad_cate'); // 店铺类别
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
    public function shopEnvironment()
    {
        $shopEnvironment = config('code.shop_environment'); // 店铺周边环境
        $data = []; // 定义二维数组列表
        // 处理数据，将一维数组转成二维数组
        foreach ($shopEnvironment as $key => $value) {
            $data[] = ['en_id' => $key, 'en_name' => $value];
        }
        return show(config('code.success'), 'OK', $data);
    }
}