<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Request;

/**
 * admin模块用户反馈控制器类
 * Class News
 * @package app\admin\controller
 */
class Feedback extends Base
{
    /**
     * 显示用户反馈资源列表
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
            if (!empty($param['keywords'])) {
                $map['u.user_name|u.phone|fb.content'] = ['like', '%' . trim($param['keywords']) . '%'];
            }
            if (isset($param['status'])) {
                $map['fb.status'] = intval($param['status']);
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = model('Feedback')->getFeedback($map, (int)$this->size);
            if (!$data) {
                return show(config('code.error'), 'Not Found', '', 404);
            }
            $feedbackStatus = config('code.feedback_status');
            foreach ($data as $key => $value) {
                // 处理数据
                $data[$key]['status_msg'] = $feedbackStatus[$value['status']]; // 定义status_msg
            }

            return show(config('code.success'), 'OK', $data);
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 显示指定的用户反馈资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            try {
                $data = model('Feedback')->alias('fb')->field('fb.*, u.user_name, u.phone')->join('__USER__ u', 'fb.user_id = u.user_id')->find($id);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            if ($data) {
                // 处理数据
                // 定义status_msg
                $feedbackStatus = config('code.feedback_status');
                $data['status_msg'] = $feedbackStatus[$data['status']];

                return show(config('code.success'), 'ok', $data);
            }
        }
    }

    /**
     * 保存更新的用户反馈资源
     * @param Request $request
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function update(Request $request, $id)
    {
        // 传入的参数
        $param = input('param.');

        // 判断数据是否存在
        $data = [];
        // 用户反馈处理状态
        if (isset($param['status'])) { // 不能用 !empty() ，否则 status = 0 时也判断为空
            $data['status'] = input('param.status', null, 'intval');
        }

        if (empty($data)) {
            return show(config('code.error'), '数据不合法', '', 404);
        }

        // 更新
        try {
            $result = model('Feedback')->save($data, ['id' => $id]); // 更新
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), 500, config('code.error'));
        }
        if (false === $result) {
            return show(config('code.error'), '更新失败', '', 403);
        } else {
            return show(config('code.success'), '更新成功', '', 201);
        }
    }
}