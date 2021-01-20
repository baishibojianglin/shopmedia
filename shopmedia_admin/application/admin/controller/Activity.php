<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Request;

/**
 * admin模块活动控制器类
 * Class Activity
 * @package app\admin\controller
 */
class Activity extends Base
{
    /**
     * 显示活动资源列表
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
            if (!empty($param['act_name'])) {
                $map['act_name'] = ['like', '%' . trim($param['act_name']) . '%'];
            }
            if (isset($param['is_delete'])) {
                $map['is_delete'] = $param['is_delete'];
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = model('Activity')->getActivity($map, (int)$this->size);
            if (!$data) {
                return show(config('code.error'), 'Not Found', '', 404);
            }
            $status = config('code.status');
            try {
            foreach ($data as $key => $value) {
                // 处理数据
                $data[$key]['start_time'] = date('Y-m-d H:i:s', $value['start_time']); // 活动开始时间
                $data[$key]['end_time'] = date('Y-m-d H:i:s', $value['end_time']); // 活动结束时间
                $data[$key]['create_time'] = date('Y-m-d H:i:s', $value['create_time']); // 活动创建时间
                $data[$key]['status_msg'] = $status[$value['status']]; // 定义status_msg
            }
            } catch (\Exception $e) {
                return show(config('code.error'), $e->getMessage(), '');
            }
            return show(config('code.success'), 'OK', $data);
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 获取活动列表（不分页，用于 Select 选择器等）
     * @return \think\response\Json
     */
    public function activityList()
    {
        // 判断为GET请求
        if (!request()->isGet()) {
            return show(config('code.error'), '请求不合法', '', 400);
        }

        // 查询条件
        $map = [];
        $map['status'] = config('code.status_enable');
        $map['is_delete'] = config('code.not_delete');

        $data = model('Activity')->where($map)->select();

        return show(config('code.success'), 'OK', $data);
    }

    /**
     * 保存新建的活动资源
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

            // 处理数据
            if (isset($data['act_datetime']) && !empty($data['act_datetime'])) {
                $data['start_time'] = strtotime($data['act_datetime'][0]); // 活动开始时间
                $data['end_time'] = strtotime($data['act_datetime'][1]); // 活动结束时间
            }

            // 入库操作
            // 捕获异常
            try {
                $id = model('Activity')->add($data, 'act_id'); // 新增
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
     * 显示指定的活动资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            try {
                $data = model('Activity')->find($id);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            if ($data) {
                // 处理数据
                // 定义status_msg
                $status = config('code.status');
                $data['status_msg'] = $status[$data['status']];
                $data['act_datetime'] = [date('Y-m-d H:i:s', $data['start_time']), date('Y-m-d H:i:s', $data['end_time'])];

                return show(config('code.success'), 'ok', $data);
            }
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 保存更新的新闻资源
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

        // 获取更新成功前的新闻缩略图thumb、发布时间publish_time
        $news = model('News')->field('thumb, publish_time')->find($id);

        // validate验证
        /*$validate = validate('News');
        if (!$validate->check($param, [], '')) {
            return show(config('code.error'), $validate->getError(), [], 403);
        }*/

        // 判断数据是否存在
        $data = [];
        if (!empty($param['title'])) {
            $data['title'] = trim($param['title']);
        }
        if (!empty($param['author'])) {
            $data['author'] = trim($param['author']);
        }
        if (isset($param['cate_id'])) {
            $data['cate_id'] = $param['cate_id'];
        }
        if (!empty($param['keywords'])) {
            $data['keywords'] = trim($param['keywords']);
        }
        if (!empty($param['brief'])) {
            $data['brief'] = trim($param['brief']);
        }
        if (!empty($param['thumb'])) {
            $data['thumb'] = trim($param['thumb']);
        }
        if (!empty($param['content'])) {
            $data['content'] = $param['content'];
        }
        if (isset($param['status'])) { // 不能用 !empty() ，否则 status = 0 时也判断为空
            $data['status'] = input('param.status', null, 'intval');

            // 发布时间
            if ($param['status'] == 4 && $news['publish_time'] == 0) {
                $data['publish_time'] = time();
            }
        }

        if (empty($data)) {
            return show(config('code.error'), '数据不合法', '', 404);
        }

        // 更新
        try {
            $result = model('News')->save($data, ['news_id' => $id]); // 更新
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), 500, config('code.error'));
        }
        if (false === $result) {
            return show(config('code.error'), '更新失败', '', 403);
        } else {
            // 删除更新成功前的新闻缩略图thumb文件
            if (!empty($param['thumb']) && trim($param['thumb']) != $news['thumb']) {
                // 删除文件
                @unlink(ROOT_PATH . 'public' . DS . $news['thumb']);
            }

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
}