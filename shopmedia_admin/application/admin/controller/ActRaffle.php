<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Request;

/**
 * admin模块活动中奖纪录控制器类
 * Class ActRaffle
 * @package app\admin\controller
 */
class ActRaffle extends Base
{
    /**
     * 显示活动中奖纪录资源列表
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
            if (!empty($param['phone'])) {
                $map['ar.phone'] = ['like', '%' . trim($param['phone']) . '%'];
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            try {
                $data = model('ActRaffle')->getActRaffle($map, (int)$this->size);
            } catch (\Exception $e) {
                return show(config('code.error'), '请求异常' . $e->getMessage(), '', 500);
            }
            if (!$data) {
                return show(config('code.error'), 'Not Found', '', 404);
            }

            foreach ($data as $key => $value) {
                // 处理数据
                $data[$key]['raffle_time'] = date('Y-m-d H:i:s', $value['raffle_time']);
            }

            return show(config('code.success'), 'OK', $data);
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 显示指定的活动中奖纪录资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            try {
                $data = model('ActPrize')->find($id);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            if ($data) {
                return show(config('code.success'), 'ok', $data);
            }
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 保存更新的活动中奖纪录资源
     * @param Request $request
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function update(Request $request, $id)
    {
        // 判断为PUT请求
        if (!request()->isPut()) {
            return show(config('code.error'), '请求不合法', '', 400);
        }

        // 传入的参数
        $param = input('param.');

        // 判断数据是否存在
        $data = [];
        if (!empty($param['prize_name'])) {
            $data['prize_name'] = trim($param['prize_name']);
        }
        if (isset($param['act_id'])) {
            $data['act_id'] = (int)$param['act_id'];
        }
        if (!empty($param['quantity'])) {
            $data['quantity'] = trim($param['quantity']);
        }
        if (!empty($param['level'])) {
            $data['level'] = (int)$param['level'];
        }
        if (!empty($param['percentage'])) {
            $data['percentage'] = trim($param['percentage']);
        }
        if (!empty($param['sponsor'])) {
            $data['sponsor'] = trim($param['sponsor']);
        }
        if (!empty($param['phone'])) {
            $data['phone'] = trim($param['phone']);
        }
        if (isset($param['status'])) {
            $data['status'] = (int)trim($param['status']);
        }

        if (empty($data)) {
            return show(config('code.error'), '数据不合法', '', 404);
        }

        // 更新
        try {
            $result = model('ActPrize')->save($data, ['prize_id' => $id]); // 更新
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