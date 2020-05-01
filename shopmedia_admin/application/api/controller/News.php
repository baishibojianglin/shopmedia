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
     * @return \think\response\View
     */
    public function index0()
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

    /**
     * 新闻详情
     * @param $news_id
     * @return \think\response\View
     */
    public function newsDetail($news_id)
    {
        // 多语言
        $language = session('language') == 'en' ? 'en' : 'zh';

        // 新闻详情
        $newsData = model('News')->find($news_id);
        $newsData['publish_time'] = date('Y-m-d', $newsData['publish_time']); // 新闻发布时间

        // 上一篇 `SELECT * FROM news WHERE news_id<$news_id ORDER BY news_id DESC LIMIT 1;`
        $previousNewsData = model('News')->field('news_id, title')->where(['status' => 4, 'news_id' => ['<', $news_id], 'language' => $language])->order(['news_id' => 'desc'])->limit(1)->select();
        // 下一篇 `SELECT * FROM news WHERE news_id>$news_id ORDER BY news_id ASC LIMIT 1;`
        $nextNewsData = model('News')->field('news_id, title')->where(['status' => 4, 'news_id' => ['>', $news_id], 'language' => $language])->order(['news_id' => 'asc'])->limit(1)->select();

        $template = session('language') == 'en' ? 'en/news/news_detail' : '';
        return view($template, [
            'newsData' => $newsData,
            'previousNewsData' => $previousNewsData,
            'nextNewsData' => $nextNewsData
        ]);
    }
}