<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Db;
use think\Request;

/**
 * admin模块用户类型控制器类
 * Class UserType
 * @package app\admin\controller
 */
class UserType extends Base
{
    /**
     * 显示用户类型资源列表
     * @return \think\response\Json
     */
    public function index()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的数据
            $param = input('param.');
            if (isset($param['size'])) { // 每页条数
                $param['size'] = intval($param['size']);
            }

            // 查询条件
            $map = [];
            if (!empty($param['type_name'])) { // 用户类型名称
                $map['type_name'] = ['like', '%' . $param['type_name'] . '%'];
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            try {
                $data = model('UserType')->getUserType($map, $this->size);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', '', 500); // $e->getMessage()
            }
            $status = config('code.status');
            foreach ($data as $key => $value) {
                $data[$key]['status_msg'] = $status[$value['status']]; // 定义状态信息
            }
            return show(config('code.success'), 'OK', $data);
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 显示指定的用户类型资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 获取用户类型信息
            try {
                $data = model('UserType')->find($id);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', '', 500);
            }

            if ($data) {
                // 处理数据
                // 定义status_msg
                $status = config('code.status');
                $data['status_msg'] = $status[$data['status']];

                return show(config('code.success'), 'ok', $data);
            }
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 保存更新的用户类型资源
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

        // 传入的数据
        $param = input('param.');

        // TODO：validate验证

        // 判断数据是否存在
        $data = [];
        if (isset($param['parent_comm_ratio'])) { // 上级用户统一提成比例
            $data['parent_comm_ratio'] = trim($param['parent_comm_ratio']);
        }
        if (isset($param['status'])) { // 状态
            $data['status'] = $param['status'];
        }

        if (empty($data)) {
            return show(config('code.error'), '数据不合法', '', 404);
        }

        try {
            $result =model('UserType')->update($data, ['type_id' => $id]);
        } catch(\Exception $e) {
            throw new ApiException('网络忙，请重试', 500, config('code.error')); // $e->getMessage()
        }
        if (false === $result) {
            return show(config('code.error'), '更新失败', '', 403);
        } else {
            return show(config('code.success'), '更新成功', '', 201);
        }
    }
}
