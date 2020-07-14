<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Request;

/**
 * admin模块活动奖品控制器类
 * Class ActPrize
 * @package app\admin\controller
 */
class ActPrize extends Base
{
    /**
     * 显示活动奖品资源列表
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
            if (!empty($param['prize_name'])) {
                $map['ap.prize_name'] = ['like', '%' . trim($param['prize_name']) . '%'];
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = model('ActPrize')->getActPrize($map, (int)$this->size);
            if (!$data) {
                return show(config('code.error'), 'Not Found', '', 404);
            }

            $actPrizeLevel = config('activity.act_prize_level');
            $status = [0 => '下架', 1 => '正常'];
            foreach ($data as $key => $value) {
                // 处理数据
                $data[$key]['quantity'] = $data[$key]['type'] == 1 ? (int)$data[$key]['quantity'] : $data[$key]['quantity'];
                $data[$key]['level_name'] = $actPrizeLevel[$value['level']];
                $data[$key]['status_msg'] = $status[$value['status']];
            }

            return show(config('code.success'), 'OK', $data);
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 保存新建的活动奖品资源
     * @param Request $request
     * @return \think\response\Json
     * @throws ApiException
     */
    public function save(Request $request)
    {
        // 判断为POST请求
        if (request()->isPost()) {
            // 传入的参数
            $data = input('post.');

            // TODO：validate验证
            /*$validate = validate('');
            if (!$validate->check($data)) {
                return show(config('code.error'), $validate->getError(), [], 403);
            }*/

            // 入库操作
            // 捕获异常
            try {
                $id = model('ActPrize')->add($data, 'prize_id'); // 新增
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
            // 判断是否新增成功：获取id
            if ($id) {
                return show(config('code.success'), '新增成功', '', 201);
            } else {
                return show(config('code.error'), '新增失败', '', 403);
            }
        }
    }

    /**
     * 显示指定的活动奖品资源
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
     * 保存更新的活动奖品资源
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
        if (isset($param['is_sponsor_address'])) {
            $data['is_sponsor_address'] = intval($param['is_sponsor_address']);
        }
        if (!empty($param['address'])) {
            $data['address'] = trim($param['address']);
        }
        if (!empty($param['longitude'])) {
            $data['longitude'] = floatval($param['longitude']);
        }
        if (!empty($param['latitude'])) {
            $data['latitude'] = floatval($param['latitude']);
        }
        if (isset($param['status'])) {
            $data['status'] = (int)$param['status'];
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

    /**
     * 删除指定新闻资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function delete($id)
    {
        // 判断为DELETE请求
        if (!request()->isDelete()) {
            return show(config('code.error'), '请求不合法', '', 400);
        }

        // 显示指定的新闻资源
        try {
            $data = model('News')->find($id);
            //return show(config('code.success'), 'ok', $data);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), 500, config('code.error'));
        }

        // 判断数据是否存在
        if ($data['news_id'] != $id) {
            return show(config('code.error'), '数据不存在');
        }

        // 判断删除条件：新闻状态
        if (in_array($data['status'], [1, 2, 4])) {
            return show(config('code.error'), '删除失败：新闻待审核、审核通过或已发布，禁止删除', '');
        }

        // 软删除
        if ($data['is_delete'] != config('code.is_delete')) {
            // 捕获异常
            try {
                $result = model('News')->softDelete('news_id', $id);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            if (!$result) {
                return show(config('code.error'), '移除失败', '');
            } else {
                return show(config('code.success'), '移除成功', '');
            }
        }

        // 真删除
        if ($data['is_delete'] == config('code.is_delete')) {
            $result = model('News')->destroy($id);
            if (!$result) {
                return show(config('code.error'), '删除失败', '');
            } else {
                // 删除文件
                @unlink(ROOT_PATH . 'public' . DS . $data['thumb']);

                return show(config('code.success'), '删除成功', '');
            }
        }
    }

    /**
     * 活动奖品等级列表（不分页，用于 Select 选择器等）
     * @return \think\response\Json
     */
    public function actPrizeLevelList()
    {
        $actPrizeLevel = config('activity.act_prize_level');
        $data = []; // 定义二维数组列表
        // 处理数据，将一维数组转成二维数组
        foreach ($actPrizeLevel as $key => $value) {
            $data[] = ['level_id' => $key, 'level_name' => $value];
        }

        return show(config('code.success'), 'OK', $data);
    }
}