<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

/**
 * admin模块广告类别管理控制器类
 * Class AdCate
 * @package app\admin\controller
 */
class AdCate extends Base
{
    /**
     * 广告类别列表（不分页，用于 Select 选择器等）
     * @return \think\response\Json
     */
    public function adCateList()
    {
        $adCate = config('code.ad_cate'); // 广告类别
        $data = []; // 定义二维数组列表
        // 处理数据，将一维数组转成二维数组
        foreach ($adCate as $key => $value) {
            $data[] = ['cate_id' => $key, 'cate_name' => $value];
        }

        return show(config('code.success'), 'OK', $data);
    }
}