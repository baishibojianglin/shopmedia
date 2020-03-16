<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

/**
 * admin模块商品类别管理控制器类
 * Class GoodsCate
 * @package app\admin\controller
 */
class GoodsCate extends Base
{
    /**
     * 显示商品类别资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的参数
            $param = input('param.');
            if (isset($param['size'])) { // 每页条数
                $param['size'] = intval($param['size']);
            }

            // 查询条件
            $map = [];
            if (!empty($param['cate_name'])) { // 商品类别名称
                $map['gc.cate_name'] = ['like', '%' . trim($param['cate_name']) . '%'];
            }
            if (isset($param['parent_id']) && $param['parent_id'] != '') { // 上级ID
                $map['gc.parent_id'] = intval($param['parent_id']);
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取商品类别列表数据
            try {
                $data = model('GoodsCate')->getGoodsCate($map, $this->size);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', [], 500); // $e->getMessage()
            }

            if ($data) {
                // 处理数据
                $auditStatus = config('code.audit_status'); // 审核状态：0待审核，1通过，2驳回
                $isOnSale = config('code.is_on_sale'); // 是否上架：0下架，1上架
                foreach ($data as $key => $value) {
                    $data[$key]['audit_status_msg'] = $auditStatus[$value['audit_status']]; // 定义审核状态信息
                    $data[$key]['audit_time'] = $value['audit_time'] ? date('Y-m-d H:i:s', $value['audit_time']) : ''; // 审核时间
                    $data[$key]['parent_name'] = $value['parent_id'] == 0 ? '（一级类别）' : $value['parent_name']; // 上级类别名称
                    $data[$key]['is_on_sale_msg'] = $isOnSale[$value['is_on_sale']]; // 是否上架状态信息
                }

                return show(config('code.success'), 'OK', $data);
            } else {
                return show(config('code.error'), 'Not Found', $data, 404);
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }

    /**
     * 获取商品类别列表树
     * @return \think\response\Json
     */
    public function goodsCateTree()
    {
        // 获取商品类别列表树，用于页面下拉框列表
        try {
            $data = model('GoodsCate')->getGoodsCateTree();
        } catch (\Exception $e) {
            return show(config('code.error'), '网络忙，请重试', [], 500); // $e->getMessage()
        }

        if ($data) {
            // 处理数据
            foreach ($data as $key => $value) {
                if ($value['level'] != 0) {
                    // level 用于定义 title 前面的空位符的长度
                    $data[$key]['cate_name'] = '└' . str_repeat('─', $value['level'] * 1). ' ' . $value['cate_name']; // str_repeat(string,repeat) 函数把字符串重复指定的次数
                }
            }
        }

        return show(config('code.success'), 'OK', $data);
    }

    /**
     * 保存新建的商品类别资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        // 判断为POST请求
        if(request()->isPost()){
            $data = input('post.');
            $data['company_user_id'] = $this->companyUser['user_id']; // 创建者(平台管理员)ID

            // validate验证数据合法性
            $validate = validate('GoodsCate');
            if (!$validate->check($data)) {
                return show(config('code.error'), $validate->getError(), [], 403);
            }

            // 入库操作
            try {
                $id = model('GoodsCate')->add($data, 'cate_id');
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', [], 500); // $e->getMessage()
            }
            if ($id) {
                return show(config('code.success'), '新增成功', ['cate_id' => $id], 201);
            } else {
                return show(config('code.error'), '新增失败', [], 403);
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }

    /**
     * 显示指定的商品类别资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            try {
                $data = model('GoodsCate')->find($id);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', [], 500); // $e->getMessage()
            }

            if ($data) {
                return show(config('code.success'), 'ok', $data);
            } else {
                return show(config('code.error'), 'Not Found', $data, 404);
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }

    /**
     * 保存更新的商品类别资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        // 判断为PUT请求
        if (request()->isPut()) {
            // 传入的数据
            $param = input('param.');

            // validate验证数据合法性：判断是审核状态还是更新其他数据
            $validate = validate('GoodsCate');
            $rules = [];
            $scene = 'update';
            if (isset($param['audit_status'])) { // 审核操作
                $rules = ['audit_status' => 'require'];
                $scene = [];
            }
            if (isset($param['is_on_sale'])) { // 是否上架操作
                $rules = ['is_on_sale' => 'require'];
                $scene = [];
            }
            if (!$validate->check($param, $rules, $scene)) {
                return show(config('code.error'), $validate->getError(), [], 403);
            }

            // 判断数据是否存在
            $data = [];
            if (!empty($param['cate_name'])) { // 商品类别名称
                $data['cate_name'] = trim($param['cate_name']);
            }
            if (isset($param['parent_id'])) { // 上级类别ID
                if ($param['parent_id'] == $id) {
                    return show(config('code.error'), '不能选择自身作为上级', [], 403);
                }
                $data['parent_id'] = input('param.parent_id', null, 'intval');
            }
            if (isset($param['sort'])) { // 排序
                $data['sort'] = input('param.sort', null, 'intval');
            }
            if (isset($param['audit_status'])) { // 审核状态
                $data['audit_status'] = input('param.audit_status', null, 'intval');
                $data['audit_id'] = $this->companyUser['user_id'];
                $data['audit_time'] = time();
            }
            if (isset($param['is_on_sale'])) { // 是否上架
                $data['is_on_sale'] = input('param.is_on_sale', null, 'intval');
                $data['is_on_sale'] = $data['is_on_sale'] ? 0 : 1;
            }

            if (empty($data)) {
                return show(config('code.error'), '数据不合法', [], 404);
            }

            // 更新
            try {
                $result = model('GoodsCate')->save($data, ['cate_id' => $id]); // 更新
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', [], 500); // $e->getMessage()
            }
            if (false === $result) {
                return show(config('code.error'), '更新失败', [], 403);
            } else {
                return show(config('code.success'), '更新成功', [], 201);
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }

    /**
     * 删除指定商品类别资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        // 判断为DELETE请求
        if (request()->isDelete()) {
            // 获取指定的商品类别
            try {
                $data = model('GoodsCate')->find($id);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', [], 500); // $e->getMessage()
            }

            // 判断数据是否存在
            if ($data['cate_id'] != $id) {
                return show(config('code.error'), '数据不存在', [], 404);
            }

            // 判断删除条件
            // 判断是否存在下级商品类别
            $goodsCateList = model('GoodsCate')->where(['parent_id' => $id])->select();
            if (!empty($goodsCateList)) {
                return show(config('code.error'), '删除失败：存在下级商品类别', [], 403);
            }
            // 判断商品类别审核状态
            if ($data['audit_status'] == config('code.status_enable')) { // 审核通过
                return show(config('code.error'), '删除失败：商品类别已审核通过', [], 403);
            }

            // 真删除
            try { // 捕获异常
                $result = model('GoodsCate')->destroy($id);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', [], 500); // $e->getMessage()
            }
            if (!$result) {
                return show(config('code.error'), '删除失败', [], 403);
            } else {
                return show(config('code.success'), '删除成功');
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }
}
