<?php

namespace app\api\controller;

use think\Controller;

/**
 * api模块客户端区域管理控制器类
 * Class Region
 * @package app\admin\controller
 */
class Region extends AuthBase
{
    /**
     * 懒加载区域树形数据（参考admin/Region/lazyLoadRegionTree）
     * @return \think\response\Json
     */
    public function lazyLoadRegionTree()
    {
        // 判断为GET请求
        if (!request()->isGet()) {
            return show(config('code.error'), '请求不合法', '', 400);
        }

        // 传入的参数
        $param = input('param.');

        // 查询条件
        $map['parent_id'] = $param['parent_id'];

        // 查询
        try {
            $data = model('Region')->field('region_id, region_name, level, parent_id')->where($map)->cache(true, 10)->select();
        } catch (\Exception $e) {
            return show(config('code.error'), '网络忙，请重试', '', 500); // $e->getMessage()
        }

        if ($data) {
            foreach ($data as $key => $value) {
                // 判断是否存在下级区域
                $sonRegionCount = model('Region')->field('region_id')->where(['parent_id' => $value['region_id']])->count();
                $data[$key]['children_count'] = $sonRegionCount; // 定义 children_count
            }

            return show(config('code.success'), 'OK', $data);
        } else {
            return show(config('code.error'), 'Not Found', '', 404);
        }
    }

    /**
     * 获取区域列表数据（用于级联选择器等）
     * @return \think\response\Json
     */
    public function getRegionList()
    {
        $param = input('param.');
        $map['parent_id'] = $param['parent_id'];
        $regionList = model('Region')->where($map)->cache(true, 10)->select();

        if(!empty($regionList)){
            return show(config('code.success'), 'OK', $regionList);
        }else{
            return show(config('code.error'), 'Not Found', [], 404);
        }
    }
}
