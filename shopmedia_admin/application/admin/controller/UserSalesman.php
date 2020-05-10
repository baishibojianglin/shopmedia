<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use app\common\lib\IAuth;
use think\Controller;
use think\Db;
use think\Request;

/**
 * admin模块用户（业务员）控制器类
 * Class UserSalesman
 * @package app\admin\controller
 */
class UserSalesman extends Base
{
    /**
     * 显示用户资源列表
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
            $map['ur.status'] = config('code.status_enable'); // 启用角色
            /*if ($this->adminUser['company_id'] != config('admin.platform_company_id')) { // 平台可以查看所有账户，供应商只能查看自有账户
                $map['cu.company_id'] = $this->adminUser['company_id'];
            }*/
            if (!empty($param['user_name'])) { // 用户名称
                $map['u.user_name'] = ['like', '%' . $param['user_name'] . '%'];
            }
            if (!empty($param['parent_name'])) { // 上级用户名称
                $map['pu.user_name'] = ['like', '%' . $param['parent_name'] . '%'];
            }
            if (isset($param['role_id']) && $param['role_id'] != null) { // 用户角色ID
                // 如果必须满足同时存在多个角色，下面两个条件缺一不可
                // 条件1. role_id集合。如果只需存在一个或多个角色（user_role 表 role_id 字段），只写该条件 $map['us.role_id']，忽略 $map['u.user_id']
                $map['us.role_id'] = ['in', $param['role_id']];

                // 条件2. user_id集合
                $userIds = []; // 定义用户ID集合
                // 1.查询业务员信息获取 uid
                $userSalesmanList = Db::name('user_salesman')->field('uid,role_id')->where(['role_id' => ['in', $param['role_id']]])->select();
                // 2.通过uid获取 user 表 role_ids 字段值
                if ($userSalesmanList) {
                    foreach ($userSalesmanList as $k => $v) {
                        $userIds[$k] = $v['uid'];
                        $user = model('User')->field('user_id, role_ids')->where(['user_id' => $v['uid']])->find();
                        if ($user) {
                            $roleIds = explode(',', $user['role_ids']);
                            foreach ($param['role_id'] as $k1 => $v1) {
                                if (!in_array($v1, $roleIds)) {
                                    unset($userIds[$k]);
                                    //return show(config('code.error'), 'not found', '', 404);
                                }
                            }
                        }
                    }
                }
                $map['u.user_id'] = ['in', array_unique($userIds)];
            }
            if (isset($param['status']) && $param['status'] != null) { // 角色状态
                $map['us.status'] = intval($param['status']);
                /*if (intval($param['status']) == config('code.status_enable')) { // 启用
                    $map['u.status&us.status'] = config('code.status_enable');
                } else { // 禁用
                    $map['u.status|us.status'] = config('code.status_disable');
                }*/
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            try {
                $data = model('User')->getUserSalesman($map, $this->size);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', '', 500); // $e->getMessage()
            }
            $status = config('code.status');
            $isAuth = config('code.is_auth');
            foreach ($data as $key => $value) {
                // TODO：判断用户类型
                /*if (!in_array($value['role_id'], explode(',', $value['role_ids']))) {
                    return show(config('code.error'), '数据不存在', '', 404);
                }*/

                $data[$key]['status'] = $value['status'] == config('code.status_enable') ? $value['us_status'] : config('code.status_disable'); // 状态
                $data[$key]['status_msg'] = $status[$data[$key]['status']]; // 定义状态信息
                $data[$key]['auth_son_ratio_msg'] = $isAuth[$data[$key]['auth_son_ratio']]; // 定义授权配置下级提成比例状态信息
                $data[$key]['auth_open_user_msg'] = $isAuth[$data[$key]['auth_open_user']]; // 定义授权开通目标客户状态信息
                @$data[$key]['login_time'] = $value['login_time'] ? date('Y-m-d H:i:s', $value['login_time']) : ''; // 登录时间
            }
            return show(config('code.success'), 'OK', $data);
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 保存新建的用户资源
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
            $data['user_name'] = 'Sustock-' . trim($data['phone']); // 用户名

            // validate验证
            $validate = validate('User');
            if (!$validate->check($data)) {
                return show(config('code.error'), $validate->getError(), '', 403);
            }

            // 处理数据
            $data['user_name'] = 'Sustock-' . trim($data['phone']); // 用户名
            $data['password'] = IAuth::encrypt($data['password']); // 密码
            $data['role_ids'] = $data['role_id']; // 用户角色ID集合
            $data['status'] = isset($data['status']) ? $data['status'] : config('code.status_disable'); // 状态
            $data['create_time'] = time(); // 创建时间
            $data['create_ip'] = request()->ip(); // 创建IP

            /* 手动控制事务 s */
            // 启动事务
            Db::startTrans();
            try {
                // 新增原始用户
                $res[0] = $userId = Db::name('user')->strict(false)->insertGetId($data); // 新增数据并返回主键值

                // 新增业务员角色
                $data1 = [
                    'uid' => $userId,
                    'role_id' => $data['role_id'],
                    'status' => $data['status']
                ];

                // （下级业务员）邀请码
                // 获取（下级业务员）邀请码集合
                $sonInvitationCodes = Db::name('user_salesman')->column('son_invitation_code');
                // 生成唯一（下级业务员）邀请码，加前缀 1 用于区别于（目标客户）邀请码（两种邀请码也必须不同）
                $data1['son_invitation_code'] = uniqueRand('1', 10000, 99999, $sonInvitationCodes);

                // （目标客户）邀请码
                // 获取（下级业务员）邀请码集合
                $invitationCodes = Db::name('user_salesman')->column('invitation_code');
                // 生成唯一（目标客户）邀请码，加前缀 2 用于区别于（下级业务员）邀请码（两种邀请码也必须不同）
                $data1['invitation_code'] = uniqueRand('2', 10000, 99999, $invitationCodes);

                // TODO：分公司ID：1.平台登录时，通过下拉框选择获取分公司ID；2.分公司账户登录时，分公司ID为当前登录账户对应的分公司ID
                /*if ($this->adminUser['company_id'] != config('admin.platform_company_id')) {
                    $data1['company_id'] = $this->adminUser['company_id'];
                }*/
                $res[1] = Db::name('user_salesman')->insert($data1);

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
                return show(config('code.error'), '网络忙，请重试'.$e->getMessage(), '', 500);
            }
            /* 手动控制事务 e */
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 显示指定的用户资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 获取原始用户信息
            $userSalesman = Db::name('user_salesman')->field('uid')->find($id);
            $user = model('User')->field('password', true)->find($userSalesman['uid']);

            // 获取业务员信息
            try {
                $data = Db::name('user_salesman')->alias('us')
                    ->field('us.uid, us.role_id, us.money, us.income, us.cash, us.status, us.parent_comm_ratio, us.auth_son_ratio, us.comm_ratio, us.auth_open_user, u.user_name, u.role_ids, u.phone, u.avatar, u.status user_status, ur.title')
                    ->join('__USER__ u', 'us.uid = u.user_id', 'INNER')
                    ->join('__USER_ROLE__ ur', 'us.role_id = ur.id', 'INNER')
                    ->where(['us.id' => $id, 'us.role_id' => ['in', $user['role_ids']]])
                    ->find();
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
     * 保存更新的用户资源
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
        /*$validate = validate('');
        if (!$validate->check($param, [], 'update')) {
            return show(config('code.error'), $validate->getError(), '', 403);
        }*/

        // 判断数据是否存在
        $data = [];
        if (isset($param['money'])) { // 余额
            $data['money'] = trim($param['money']);
        }
        if (isset($param['income'])) { // 收益
            $data['income'] = trim($param['income']);
        }
        if (isset($param['cash'])) { // 提现
            $data['cash'] = trim($param['cash']);
        }
        if (isset($param['comm_ratio'])) { // 业务员提成比例
            $data['comm_ratio'] = trim($param['comm_ratio']);
        }
        if (isset($param['parent_comm_ratio'])) { // 向上级业务员提成比例
            $data['parent_comm_ratio'] = trim($param['parent_comm_ratio']);
        }
        if (isset($param['auth_son_ratio'])) { // 授权配置下级提成比例
            $data['auth_son_ratio'] = trim($param['auth_son_ratio']);
        }
        if (isset($param['auth_open_user'])) { // 授权开通目标客户
            $data['auth_open_user'] = trim($param['auth_open_user']);
        }
        /*if (isset($param['user_status'])) { // 用户状态
            $data['user_status'] = intval($param['user_status']);
        }*/
        if (isset($param['status'])) { // 角色状态
            $data['status'] = intval($param['status']);
        }
        // 当为还原软删除的数据时
        if (isset($param['is_delete']) && $param['is_delete'] == config('code.is_delete')) {
            $data['is_delete'] = config('code.not_delete');
        }

        if (empty($data)) {
            return show(config('code.error'), '数据不合法', '', 404);
        }

        /* 手动控制事务 s */
        // 启动事务
        Db::startTrans();
        try {
            // 更新业务员角色信息
            $res[0] = Db::name('user_salesman')->strict(false)->where(['id' => $id])->update($data);
            $res[0] = $res[0] === false ? 0 : true;

            // 获取用户ID
            $userSalesman = Db::name('user_salesman')->field('uid')->find($id);
            if ($userSalesman['uid']) {
                // 更新原始用户信息
                if ($data['status'] == config('code.status_enable')) {
                    $res[1] = Db::name('user')->strict(false)->where(['user_id' => $userSalesman['uid']])->update(['status' => $data['status']]);
                    $res[1] = $res[1] === false ? 0 : true;
                }
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
            //throw new ApiException('网络忙，请重试', 500, config('code.error')); // $e->getMessage()
        }
        /* 手动控制事务 e */
    }

    /**
     * 删除指定用户资源
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
                $data = model('User')->find($id);
                //return show(config('code.success'), 'ok', $data);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', '', 500);
                //throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            // 判断数据是否存在
            if ($data['user_id'] != $id) {
                return show(config('code.error'), '数据不存在', '', 404);
            }

            // 判断删除条件
            // 判断是否存在下级供应商账户
            $userList = model('User')->where(['parent_id' => $id])->select();
            if (!empty($userList)) {
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
                $res[0] = Db::name('company_user')->where(['user_id' => $id])->delete();
                //$result = model('User')->destroy($id);

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
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }
}
