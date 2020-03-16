<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Request;

/**
 * admin模块Auth权限认证用户组控制器类
 * Class AuthGroup
 * @package app\admin\controller
 */
class AuthGroup extends Base
{
    /**
     * 显示Auth用户组资源列表
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
            // Auth用户组ID：供应商总管理员用户组（角色）可以查看所有Auth用户组，其他区域供应商管理员只能查看自有和通用Auth用户组
            if ($this->companyUser['company_id'] != 1) {
                $groupIds = model('AuthGroup')->getAuthGroupIdsByUserId($this->companyUser['user_id']);
                $map['ag.id'] = ['in', $groupIds];
            }
            if (!empty($param['title'])) {
                $map['ag.title'] = ['like', '%' . $param['title'] . '%'];
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = model('AuthGroup')->getAuthGroup($map, $this->size);
            $status = config('code.status');
            foreach ($data as $key => $value) {
                $data[$key]['status_msg'] = $status[$value['status']];
            }
            return show(config('code.success'), 'OK', $data);
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 获取Auth用户组列表树
     * @return \think\response\Json
     */
    public function authGroupTree()
    {
        // 传入的参数
        $param = input('param.');

        // 查询条件
        $map = [];
        if ($this->companyUser['company_id'] != 1) { // Auth用户组ID
            $authGroupIds = model('AuthGroup')->getAuthGroupIdsByUserId($this->companyUser['user_id']);
            if (isset($param['parent_id'])) { // Auth用户组上级ID
                $param['parent_id'] = intval($param['parent_id']);
                $authGroupIds[] = $param['parent_id'];
                $authGroupIds = array_unique($authGroupIds);
            }
            $map['id'] = ['in', $authGroupIds];
        }

        // 获取商品类别列表树，用于页面下拉框列表
        try {
            $data = model('AuthGroup')->field('id, title, type')->where($map)->select(); // TODO：待处理，暂时这样写
        } catch (\Exception $e) {
            return show(config('code.error'), '网络忙，请重试', [], 500); // $e->getMessage()
        }

        if ($data) {
            // 处理数据
            foreach ($data as $key => $value) {
                $data[$key]['title'] = $value['title'] . '（' . ($value['type'] == 0 ? '私有角色' : '通用角色') . '）';
                /*if ($value['level'] != 0) {
                    // level 用于定义 title 前面的空位符的长度
                    $data[$key]['title'] = '└' . str_repeat('─', $value['level'] * 1). ' ' . $value['title']; // str_repeat(string,repeat) 函数把字符串重复指定的次数
                }*/
            }
        }

        return show(config('code.success'), 'OK', $data);
    }

    /**
     * 保存新建的Auth用户组资源
     * @param Request $request
     * @return \think\response\Json
     * @throws ApiException
     */
    public function save(Request $request)
    {
        // 判断为POST请求
        if (request()->isPost()) {
            // 传入的数据
            $data = input('post.');

            // validate验证
            $validate = validate('AuthGroup');
            if (!$validate->check($data)) {
                return show(config('code.error'), $validate->getError(), [], 403);
            }

            // 处理数据
            $data['status'] = isset($data['status']) ? $data['status'] : config('code.status_disable');
            $data['rules'] = isset($data['rules']) ? implode(',', $data['rules'] = [1,2,3,4]) : '';

            // 新增
            // 捕获异常
            try {
                $id = model('AuthGroup')->add($data, 'id'); // 新增
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', '', 500);
            }
            // 判断是否新增成功：获取id
            if ($id) {
                return show(config('code.success'), '角色新增成功', [], 201);
            } else {
                return show(config('code.error'), '角色新增失败', [], 403);
            }
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 显示指定的Auth用户组资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            try {
                $data = model('AuthGroup')->find($id);
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
     * 保存更新的Auth用户组资源
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

        // validate验证
        $validate = validate('AuthGroup');
        if (!$validate->check($param, [], '')) {
            return show(config('code.error'), $validate->getError(), [], 403);
        }

        // 判断数据是否存在
        if (!empty($param['title'])) {
            $data['title'] = $param['title'];
        }
        if (isset($param['status'])) { // 不能用 !empty() ，否则 status = 0 时也判断为空
            $data['status'] = input('param.status', null, 'intval');
        }
        if (isset($param['parent_id'])) { // 上级ID
            $data['parent_id'] = input('param.parent_id', null, 'intval');
        }
        if (isset($param['type'])) { // 角色类型
            $data['type'] = input('param.type', null, 'intval');
        }
        if (isset($param['auth_rules'])) { // 授权配置下级权限
            $data['auth_rules'] = input('param.auth_rules', null, 'intval');
        }
        if (!empty($param['rules'])) {
            $data['rules'] = implode(',', $param['rules']);
        }

        if (empty($data)) {
            return show(config('code.error'), '数据不合法', [], 404);
        }

        // 更新
        try {
            $result = model('AuthGroup')->save($data, ['id' => $id]); // 更新
        } catch (\Exception $e) {
            return show(config('code.error'), '网络忙，请重试', '', 500);
        }
        if (false === $result) {
            return show(config('code.error'), '更新失败', [], 403);
        } else {
            return show(config('code.success'), '更新成功', [], 201);
        }
    }

    /**
     * 配置Auth用户组权限规则
     * @param Request $request
     * @param $id
     * @return \think\response\Json
     */
    public function configAuthGroupRule(Request $request, $id)
    {
        // 判断为PUT请求
        if (!request()->isPut()) {
            return show(config('code.error'), '请求不合法', '', 400);
        }

        // 传入的参数
        $param = input('param.');

        // 判断参数是否存在
        if (!empty($param['rules'])) { // 用户组拥有的权限规则
            $data['rules'] = implode(',', $param['rules']);
        }

        if (empty($data)) {
            return show(config('code.error'), '数据不合法', '', 404);
        }

        // 更新
        try {
            $result = model('AuthGroup')->save($data, ['id' => $id]); // 更新
        } catch (\Exception $e) {
            return show(config('code.error'), '网络忙，请重试', '', 500);
        }
        if (false === $result) {
            return show(config('code.error'), '权限规则配置失败', '', 403);
        } else {
            return show(config('code.success'), '权限规则配置成功', '', 201);
        }
    }

    /**
     * 删除指定Auth用户组资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function delete($id)
    {
        // 判断为DELETE请求
        if (request()->isDelete()) {
            // 获取指定的用户组
            try {
                $data = model('AuthGroup')->find($id);
                //return show(config('code.success'), 'ok', $data);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', '', 500);
                //throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            // 判断数据是否存在
            if ($data['id'] != $id) {
                return show(config('code.error'), '数据不存在', '', 404);
            }

            // 判断删除条件
            // 判断是否存在下级用户组
            $authGroupList = model('AuthGroup')->where(['parent_id' => $id])->select();
            if (!empty($authGroupList)) {
                return show(config('code.error'), '删除失败：存在下级角色', '', 403);
            }
            // 判断用户组状态
            if ($data['status'] == config('code.status_enable')) { // 启用
                return show(config('code.error'), '删除失败：角色已启用', '', 403);
            }
            // 判断用户组规则是否为空
            if (!empty($data['rules'])) {
                return show(config('code.error'), '删除失败：角色规则不为空', '', 403);
            }

            // 真删除
            try {
                $result = model('AuthGroup')->destroy($id);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', '', 500);
            }
            if (!$result) {
                return show(config('code.error'), '删除失败', '', 403);
            } else {
                return show(config('code.success'), '删除成功');
            }
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }
}
