<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Db;
use think\Request;

/**
 * admin模块管理员控制器类
 * Class Admin
 * @package app\admin\controller
 */
class Admin extends Base
{
    /**
     * 显示管理员资源列表
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
            if ($this->adminUser['company_id'] != config('admin.platform_company_id')) { // 平台可以查看所有账户，分公司只能查看自有账户
                $map['a.company_id'] = $this->adminUser['company_id'];
            }
            if (!empty($param['account'])) { // 管理员账号
                $map['a.account'] = ['like', '%' . $param['account'] . '%'];
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            try {
                $data = model('Admin')->getAdmin($map, $this->size);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', '', 500); // $e->getMessage()
            }
            $status = config('code.status');
            foreach ($data as $key => $value) {
                $data[$key]['status_msg'] = $status[$value['status']]; // 定义状态信息
                $data[$key]['login_time'] = $value['login_time'] ? date('Y-m-d H:i:s', $value['login_time']) : ''; // 登录时间
            }
            return show(config('code.success'), 'OK', $data);
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 保存新建的管理员资源
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
            $validate = validate('Admin');
            if (!$validate->check($data)) {
                return show(config('code.error'), $validate->getError(), '', 403);
            }

            // 处理数据
            // 分公司ID：1.平台登录时，通过下拉框选择获取分公司ID；2.分公司管理员登录时，新增的分公司管理员所属的分公司ID为当前登录管理员对应的分公司ID
            if ($this->adminUser['company_id'] != config('admin.platform_company_id')) {
                $data['company_id'] = $this->adminUser['company_id'];
            }

            // 上级ID：1.平台只能新增每个分公司的总管理员，该分公司的总管理员上级ID为平台管理员id；2.分公司管理员新增下级账户时，TODO：parent_id待处理
            if ($this->adminUser['company_id'] == config('admin.platform_company_id')) { // 平台账户登录时
                // 当平台管理员登录时
                if ($this->adminUser['parent_id'] == 0) {
                    $data['parent_id'] = $this->adminUser['id'];
                }
                // 当平台非管理员账户登录时
                if ($this->adminUser['parent_id'] == 1) {
                    $data['parent_id'] = $this->adminUser['parent_id'];
                }
            } else { // 分公司管理员登录时
                $data['parent_id'] = $this->adminUser['id']; // TODO：parent_id待处理
            }

            $data['password'] = md5($data['password']); // 密码
            $data['status'] = isset($data['status']) ? $data['status'] : config('code.status_disable'); // 状态
            $data['create_time'] = time(); // 创建时间
            $data['create_ip'] = request()->ip(); // 创建IP

            /* 手动控制事务 s */
            // 启动事务
            Db::startTrans();
            try {
                // 新增管理员
                $res[0] = $userId = Db::name('admin')->strict(false)->insertGetId($data); // 新增数据并返回主键值

                // 绑定管理员角色
                $data1 = ['uid' => $userId, 'group_id' => $data['group_id']];
                $res[1] = Db::name('auth_group_access')->insert($data1);

                // 任意一个表写入失败都会抛出异常，TODO：是否可以不做该判断
                if (in_array(0, $res)) {
                    return show(config('code.error'), '新增失败', '', 403);
                }

                // 提交事务
                Db::commit();
                return show(config('code.success'), '新增成功', '', 201);
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return show(config('code.error'), '网络忙，请重试', '', 500);
            }
            /* 手动控制事务 e */
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 显示指定的管理员资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            try {
                $data = model('Admin')->alias('a')->field('a.id, a.account, a.phone, a.company_id, a.status, aga.group_id')->join('__AUTH_GROUP_ACCESS__ aga', 'a.id = aga.uid', 'LEFT')->find($id);
            } catch (\Exception $e) {
                return show(config('code.error'), '请求异常', '', 500);
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
     * 保存更新的管理员资源
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
        $validate = validate('Admin');
        if (!$validate->check($param, [], 'update')) {
            return show(config('code.error'), $validate->getError(), '', 403);
        }

        // 判断数据是否存在
        $data = [];
        if (!empty($param['user_name'])) { // 供应商账户名称
            $data['user_name'] = trim($param['user_name']);

            // 忽略唯一(unique)类型字段user_name对自身数据的唯一性验证
            $_data = model('Admin')->where(['id' => ['neq', $id], 'user_name' => $data['user_name']])->find();
            if ($_data) {
                return show(config('code.error'), '供应商账户名称已存在', '', 403);
            }
        }
        if (!empty($param['avatar'])) {  // 供应商账户证件照
            $data['avatar'] = trim($param['avatar']);
        }
        if (!empty($param['account'])) { // 供应商账户号
            $data['account'] = trim($param['account']);

            // 忽略唯一(unique)类型字段account对自身数据的唯一性验证
            $_data = model('Admin')->where(['id' => ['neq', $id], 'account' => $data['account']])->find();
            if ($_data) {
                return show(config('code.error'), '供应商账户号已存在', '', 403);
            }
        }
        if (!empty($param['password'])) { // 密码
            $data['password'] = md5($param['password']);
        }
        if (!empty($param['phone'])) { // 电话号码
            $data['phone'] = trim($param['phone']);

            // 忽略唯一(unique)类型字段phone对自身数据的唯一性验证
            $_data = model('Admin')->where(['id' => ['neq', $id], 'phone' => $data['phone']])->find();
            if ($_data) {
                return show(config('code.error'), '供应商账户电话号码已存在', '', 403);
            }
        }
        if (isset($param['ratio'])) { // 提成比例
            $data['ratio'] = trim($param['ratio']);
        }
        if (isset($param['status'])) { // 状态，不能用 !empty() ，否则 status = 0 时也判断为空
            $data['status'] = input('param.status', null, 'intval');

            // 供应商账户已登录时，不能禁用
            if ($this->adminUser['id'] == $id && $data['status'] == config('code.status_disable')) {
                return show(config('code.error'), '供应商账户已登录，不能禁用', '', 403);
            }
        }
        if (!empty($param['company_id'])) { // 供应商ID
            $data['company_id'] = $param['company_id'];
        }
        if (!empty($param['group_id'])) { // 角色ID
            $data['group_id'] = $param['group_id'];
        }

        if (empty($data)) {
            return show(config('code.error'), '数据不合法', '', 404);
        }

        /* 手动控制事务 s */
        // 启动事务
        Db::startTrans();
        try {
            // 更新供应商账户
            $res[0] = Db::name('admin')->strict(false)->where(['id' => $id])->update($data);
            $res[0] = $res[0] === false ? 0 : true;
            //$result = model('Admin')->allowField(true)->save($data, ['id' => $id]); // 更新

            // 查询供应商是否已经绑定角色，TODO：暂时只考虑“账户-角色”一对一关系
            $authGroupAccess = model('AuthGroupAccess')->where(['uid' => $id])->find();
            if ($authGroupAccess) {
                // 更新供应商账户角色
                $res[1] = Db::name('auth_group_access')->strict(false)->where(['uid' => $id])->update(['group_id' => $data['group_id']]);
                $res[1] = $res[1] === false ? 0 : true;
            } else {
                // 绑定供应商账户角色
                $data1 = ['uid' => $id, 'group_id' => $data['group_id']];
                $res[1] = Db::name('auth_group_access')->insert($data1);
            }

            // 任意一个表写入失败都会抛出异常，TODO：是否可以不做该判断
            if (in_array(0, $res)) {
                return show(config('code.error'), '更新失败', '', 403);
            }

            // 提交事务
            Db::commit();
            return show(config('code.success'), '更新成功', '', 201);
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return show(config('code.error'), '网络忙，请重试', '', 500);
        }
        /* 手动控制事务 e */
    }

    /**
     * 删除指定管理员资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function delete($id)
    {
        // 判断为DELETE请求
        if (request()->isDelete()) {
            // 显示指定的店鋪比赛场次模板
            try {
                $data = model('Admin')->find($id);
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
            // 判断是否存在下级供应商账户
            $adminList = model('Admin')->where(['parent_id' => $id])->select();
            if (!empty($adminList)) {
                return show(config('code.error'), '删除失败：存在下级供应商账户', '', 403);
            }
            // 判断供应商账户状态
            if ($data['status'] == config('code.status_enable')) { // 启用
                return show(config('code.error'), '删除失败：供应商账户已启用', '', 403);
            }

            /* 手动控制事务 s */
            // 启动事务
            Db::startTrans();
            try {
                // 真删除指定供应商账户
                $res[0] = Db::name('admin')->where(['id' => $id])->delete();
                //$result = model('Admin')->destroy($id);

                // 真删除指定供应商账户角色
                $res[1] = Db::name('auth_group_access')->where(['uid' => $id])->delete();

                // 任意一个表写入失败都会抛出异常，TODO：是否可以不做该判断
                if (in_array(0, $res)) {
                    return show(config('code.error'), '删除失败', '', 403);
                }

                // 提交事务
                Db::commit();
                return show(config('code.success'), '删除成功');
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return show(config('code.error'), '网络忙，请重试', '', 500);
            }
            /* 手动控制事务 e */
            /*// 真删除
            try {
                $result = model('Admin')->destroy($id);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', '', 500);
            }
            if (!$result) {
                return show(config('code.error'), '删除失败', '', 403);
            } else {
                return show(config('code.success'), '删除成功');
            }*/
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }
}
