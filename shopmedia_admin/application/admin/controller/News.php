<?php

namespace app\admin\controller;

/**
 * admin模块新闻控制器类
 * Class News
 * @package app\admin\controller
 */
class News extends Base
{
    /**
     * 新闻列表
     * @return \think\response\Json
     */
    public function index()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的数据
            $param = input('param.');

            // 查询条件
            $map = [
                'status' => 4,
                'is_delete' => config('code.not_delete')
            ];

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            $newsList = model('News')->getNews($map);
            if (!$newsList) {
                return show(config('code.error'), 'Not Found', '', 404);
            }
            foreach ($newsList as $key => $value) {
                // 处理数据
                $value['publish_time'] = date('Y-m-d', $value['publish_time']); // 新闻发布时间
            }

            return show(config('code.success'), 'OK', $newsList);
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }
}