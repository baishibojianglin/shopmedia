<?php

namespace app\api\controller;

use think\Controller;
use think\Db;

/**
 * api模块客户端广告套餐控制器类
 * Class AdCombo
 * @package app\api\controller
 */
class AdCombo extends AuthBase
{
    /**
     * 广告套餐列表（不分页，用于 Select 选择器等）
     * @return \think\response\Json
     */
    public function AdComboList()
    {
        $data = Db::name('ad_combo')->where(['status' => 1])->select();

        $adType = config('ad.ad_type'); // 广告类型
        foreach ($data as $key => $value) {
            // 处理数据
            $data[$key]['ad_type_name'] = $adType[$value['ad_type']];
        }

        return show(config('code.success'), 'OK', $data);
    }
}