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
            if ($this->adminUser['company_id'] != config('admin.platform_company_id')) {
                $groupIds = model('AuthGroup')->getAuthGroupIdsByUserId($this->adminUser['id']);
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
     * 获取Auth用户组列表树（以Auth用户组所属分公司ID 作 Select 选择器分组）
     * @return \think\response\Json
     */
    public function authGroupTree()
    {
        // 传入的参数
        $param = input('param.');

        // 1.获取Auth用户组所属分公司ID集合
        $map1 = []; // 查询条件
        $map1['status'] = config('code.status_enable'); // 启用状态
        $authGroupCompanyIds = model('AuthGroup')->where($map1)->column('company_id');
        $authGroupCompanyIds = array_keys(array_flip($authGroupCompanyIds)); // 数组去重

        // 2.通过Auth用户组所属分公司ID集合获取分公司列表
        $map2 = [];
        $map2['company_id'] = ['in', $authGroupCompanyIds];
        if (isset($param['company_id'])) { // 所属分公司ID
            $map2['company_id'] = intval($param['company_id']);
        }
        $companyList = model('Company')->field('company_id, company_name')->where($map2)->select();

        // 3.通过指定的分公司ID获取Auth用户组列表，并组装API接口数据$data
        $map3 = [];
        $map3['status'] = config('code.status_enable'); // 启用状态
        $data = []; // 定义API接口数据
        // 当Auth用户组属于分公司时
        if ($companyList && $authGroupCompanyIds) {
            // 处理数据
            foreach ($companyList as $key => $value) {
                foreach ($authGroupCompanyIds as $k => $v) {
                    if ($value['company_id'] == $v) {
                        // 定义 Select 选择器的分组名 label
                        $data[$key]['label'] = $value['company_name'];

                        // 通过指定的分公司ID获取Auth用户组列表
                        $map3['company_id'] = $value['company_id'];
                        $authGroupList = model('AuthGroup')->field('id, title, company_id')->where($map3)->select();
                        // 定义 Select 选择器 options
                        $data[$key]['options'] = $authGroupList;
                    }
                }
            }

            // 获取超级管理员角色
            $authGroupList0 = model('AuthGroup')->field('id, title, company_id')->where(['id' => 1, 'company_id' => 0])->select();
            // 向数组 $data 头部追加总平台的 Select 选择器分组属性 label、options 值
            array_unshift($data, [
                'label' => '公司总平台', // 定义 Select 选择器的分组名 label
                'options' => $authGroupList0 // 定义 Select 选择器 options
            ]);
        }
        // 当Auth用户组属于总平台且总平台管理员登录时
        if (isset($param['company_id']) && $param['company_id'] == config('admin.platform_company_id')) {
            if (in_array(config('admin.platform_company_id'), $authGroupCompanyIds) && $this->adminUser['company_id'] == config('admin.platform_company_id')) {
                // 通过指定的分公司ID获取Auth用户组列表
                $map3['company_id'] = config('admin.platform_company_id');
                $authGroupList = model('AuthGroup')->field('id, title, company_id')->where($map3)->select();

                // 向数组 $data 头部追加总平台的 Select 选择器分组属性 label、options 值
                array_unshift($data, [
                    'label' => '公司总平台', // 定义 Select 选择器的分组名 label
                    'options' => $authGroupList // 定义 Select 选择器 options
                ]);
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
                return show(config('code.error'), '请求异常', '', 500);
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
//                $data = model('AuthGroup')->alias('ag')->field('ag.*, c.company_name')->join('__COMPANY__ c', 'ag.company_id = c.company_id', 'LEFT')->find($id);
                $data = model('AuthGroup')->alias('ag')->field('ag.*')->find($id);
            } catch (\Exception $e) {
                return show(config('code.error'), '请求异常', '', 500);
            }

            if ($data) {
                // 处理数据
                // 定义status_msg
                $status = config('code.status');
                $data['status_msg'] = $status[$data['status']];

                /*if ($data['company_id'] == config('admin.platform_company_id')) {
                    $data[''];
                }*/

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
            return show(config('code.error'), '请求异常', '', 500);
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
            // 用户组拥有的规则id
            $data['rules'] = array_reduce($param['rules'], 'array_merge', array()); // 二维数组转一维数组
            $data['rules'] = implode(',', $data['rules']);

            // 全选与半选的Auth权限规则id
            $data['checked_half_rules'] = json_encode([
                'checked' => $param['rules'][0], // 全选
                'half' => $param['rules'][1] // 半选
            ]);
        }

        if (empty($data)) {
            return show(config('code.error'), '数据不合法', '', 404);
        }

        // 更新
        try {
            $result = model('AuthGroup')->save($data, ['id' => $id]); // 更新
        } catch (\Exception $e) {
            return show(config('code.error'), '请求异常', '', 500);
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
                return show(config('code.error'), '请求异常', '', 500);
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
                return show(config('code.error'), '请求异常', '', 500);
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
