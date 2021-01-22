<?php


namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Request;

/**
 * admin模块商品分类控制器类
 * Class Admin
 * @package app\admin\controller
 */
class GoodsType extends Base
{

    /**
     * 显示商品分类列表
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
            if (!empty($param['type_name'])) {
                $map['type_name'] = ['like', '%' . trim($param['type_name']) . '%'];
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = model('TgGoodsType')->getGoodsType($map, (int)$this->size);
            if (!$data) {
                return show(config('code.error'), 'Not Found', '', 404);
            }
            $status = config('code.status');
            try {
                foreach ($data as $key => $value) {
                    // 处理数据
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
     * 新增商品分类
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
                $id = model('TgGoodsType')->add($data, 'id'); // 新增
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
     * 保存更新的商品分类
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
        if (!empty($param['type_name'])) {
            $data['type_name'] = trim($param['type_name']);
        }
        if (isset($param['parent_id'])) {
            $data['parent_id'] = trim($param['parent_id']);
        }

        if (empty($data)) {
            return show(config('code.error'), '数据不合法', '', 404);
        }

        // 更新
        try {
            $result = model('TgGoodsType')->save($data, ['id' => $id]); // 更新
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
     * 禁用商品分类
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

        try {
            $data = model('TgGoodsType')->find($id);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), 500, config('code.error'));
        }

        // 判断数据是否存在
        if ($data['id'] != $id) {
            return show(config('code.error'), '数据不存在');
        }

        $status = 1;
        if($data['status'] == 1){
            $status = 0;
        }

        try {
            $result = model('TgGoodsType')->save(['status' => $status],['id' => $id]);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), 500, config('code.error'));
        }

        if (!$result) {
            return show(config('code.error'), '操作失败', '');
        } else {
            return show(config('code.success'), '操作成功', ['status' => $status]);
        }
    }
    /**
     * 显示商品分类列表树
     * @return \think\response\Json
     */
    public function GoodsTypeTree(){
        // 判断为GET请求
        if (request()->isGet()) {

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = model('TgGoodsType')->getGoodsTypeTree();

            return show(config('code.success'), 'OK', $data);
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }
}