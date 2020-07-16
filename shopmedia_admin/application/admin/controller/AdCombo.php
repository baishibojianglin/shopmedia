<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Request;

/**
 * admin模块广告套餐控制器类
 * Class AdCombo
 * @package app\admin\controller
 */
class AdCombo extends Base
{
    /**
     * 显示广告套餐资源列表
     * @return \think\response\Json
     */
    public function index()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的参数
            $param = input('param.');

            // 查询条件
            $map = [];

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = model('AdCombo')->getAdCombo($map, (int)$this->size);
            if (!$data) {
                return show(config('code.error'), 'Not Found', '', 404);
            }

            $adType = config('ad.ad_type');
            $status = config('code.status');
            foreach ($data as $key => $value) {
                // 处理数据
                $data[$key]['ad_type_name'] = $adType[$value['ad_type']];
                $data[$key]['status_msg'] = $status[$value['status']];
            }

            return show(config('code.success'), 'OK', $data);
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }
}