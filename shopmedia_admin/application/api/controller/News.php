<?php

namespace app\api\controller;

/**
 * api模块客户端新闻控制器类
 * Class News
 * @package app\api\controller
 */
class News extends Common
{
    /**
     * 新闻列表
     * @return \think\response\Json
     */
    public function index()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的参数
            $param = input('param.');

            // 查询条件
            $map = ['status' => 4, 'is_delete' => config('code.not_delete')];

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 根据传入的新闻ID加载更多
            if (!empty($param['minId'])) {
                // 获取新闻最大（倒序时为最小）ID（除 $map['news_id'] 外的其他 $map 条件必须带上）
                $maxId = model('News')->getMin($map, 'news_id');

                // 判断传入的新闻ID是否为最大（倒序时为最小）ID，即数据是否全部加载
                if ($param['minId'] == $maxId) {
                    return show(config('code.error'), '加载完成', ['maxId' => $maxId], 404);
                }

                // 获取大于（倒序时为小于）传入的新闻ID的集合
                $map['news_id'] = ['lt', $param['minId']];
            }

            // 获取新闻列表数据 模式二：page当前页，size每页条数，from每页从第几条开始 => 'limit from,size'
            $newsList = model('News')->getNewsByCondition($map, $this->from, $this->size);
            /*// 获取新闻列表数据总数
            $total = model('News')->getNewsCountByCondition($map);
            // 总页数：结合 总数 + size => 有多少页
            $pages = ceil($total / $this->size); // 1.1 => 2*/

            if (!$newsList) {
                return show(config('code.error'), 'Not Found', '', 404);
            }
            foreach ($newsList as $key => $value) {
                // 处理数据
                $value['publish_time'] = date('Y-m-d H:i:s', $value['publish_time']); // 新闻发布时间
            }
            $data = [
                /*'total' => $total,
                'pages' => $pages,*/
                'data' => $newsList
            ];
            return show(config('code.success'), 'OK', $data);
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 新闻详情
     * @param $id
     * @return \think\response\Json
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 获取指定的新闻资源
            try {
                $data = model('News')->field(true)->find($id);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', '', 500);
            }

            if ($data) {
                // 处理数据
                $data['publish_time'] = date('Y-m-d H:i:s', $data['publish_time']); // 新闻发布时间

                return show(config('code.success'), 'ok', $data);
            }
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }
}