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