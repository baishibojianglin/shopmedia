<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Db;
use think\Request;

/**
 * admin模块用户（广告屏合作商）控制器类
 * Class UserPartner
 * @package app\admin\controller
 */
class UserPartner extends Base
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
            if (isset($param['status'])) { // 状态
                $map['up.status'] = intval($param['status']);
            }
            if (isset($param['is_delete'])) { // 是否删除
                $map['up.is_delete'] = $param['is_delete'];
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            try {
                $data = model('User')->getUserPartner($map, $this->size);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', '', 500); // $e->getMessage()
            }
            $status = config('code.status');
            foreach ($data as $key => $value) {
                $data[$key]['status'] = $value['status'] == config('code.status_enable') ? $value['partner_status'] : config('code.status_disable'); // 启用状态
                $data[$key]['status_msg'] = $status[$data[$key]['status']]; // 定义启用状态信息
                @$data[$key]['login_time'] = $value['login_time'] ? date('Y-m-d H:i:s', $value['login_time']) : ''; // 登录时间
            }
            return show(config('code.success'), 'OK', $data);
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 广告屏合作商列表（不分页，用于 Select 选择器等）
     * @return \think\response\Json
     */
    public function userPartnerList()
    {
        if (!request()->isGet()) {
            return show(config('code.error'), '请求不合法', '', 400);
        }

        $data = Db::name('user_partner')->alias('up')
            ->field('up.id partner_id, up.user_id, u.user_name, u.phone')
            ->join('__USER__ u', 'up.user_id = u.user_id')
            ->select();
        return show(config('code.success'), 'OK', $data);
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

            // validate验证
            $validate = validate('CompanyUser');
            if (!$validate->check($data)) {
                return show(config('code.error'), $validate->getError(), '', 403);
            }

            // 处理数据
            // 供应商ID：1.平台登录时，通过下拉框选择获取供应商ID；2.供应商账户登录时，新增的下级供应商账户的所属供应商ID为当前登录账户对应的供应商ID
            if ($this->adminUser['company_id'] != config('admin.platform_company_id')) {
                $data['company_id'] = $this->adminUser['company_id'];
            }

            // 上级ID：1.平台只能新增每个供应商的总账户，该供应商的总账户上级ID为平台管理员user_id；2.供应商账户新增下级账户时，TODO：parent_id待处理
            if ($this->adminUser['company_id'] == 1) { // 平台账户登录时
                // 当平台管理员登录时
                if ($this->adminUser['parent_id'] == 0) {
                    $data['parent_id'] = $this->adminUser['user_id'];
                }
                // 当平台非管理员账户登录时
                if ($this->adminUser['parent_id'] == 1) {
                    $data['parent_id'] = $this->adminUser['parent_id'];
                }
            } else { // 供应商账户登录时
                $data['parent_id'] = $this->adminUser['user_id']; // TODO：parent_id待处理
            }

            $data['password'] = md5($data['password']); // 密码
            $data['status'] = isset($data['status']) ? $data['status'] : config('code.status_disable'); // 状态
            $data['create_time'] = time(); // 创建时间
            $data['create_ip'] = request()->ip(); // 创建IP

            /* 手动控制事务 s */
            // 启动事务
            Db::startTrans();
            try {
                // 新增供应商账户
                $res[0] = $userId = Db::name('company_user')->strict(false)->insertGetId($data); // 新增数据并返回主键值

                // 绑定供应商账户角色
                $data1 = ['uid' => $userId, 'group_id' => $data['group_id']];
                $res[1] = Db::name('auth_group_access')->insert($data1);

                // 任意一个表写入失败都会抛出异常，TODO：是否可以不做该判断
                if (in_array(0, $res)) {
                    return show(config('code.error'), '供应商账户新增失败', '', 403);
                }

                // 提交事务
                Db::commit();
                return show(config('code.success'), '供应商账户新增成功', '', 201);
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
            $user = model('User')->field('password', true)->find($id);

            // 查询条件
            $map = [
                'up.user_id' => $id,
                'up.role_id' => ['in', $user['role_ids']]
            ];

            // 获取广告屏合作商信息
            try {
                $data = Db::name('user_partner')->alias('up')->field('up.user_id, up.role_id, up.money, up.income, up.cash, up.status, u.user_name, u.role_ids, u.phone, u.avatar')->join('__USER__ u', 'up.user_id = u.user_id', 'INNER')->where($map)->find();
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

        // TODO：validate验证

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
        if (isset($param['status'])) { // 状态
            $data['status'] = $param['status'];
        }
        // 当为还原软删除的数据时
        if (isset($param['is_delete']) && $param['is_delete'] == config('code.is_delete')) {
            $data['is_delete'] = config('code.not_delete');
        }

        if (empty($data)) {
            return show(config('code.error'), '数据不合法', '', 404);
        }

        try {
            $result = Db::name('user_partner')->where(['user_id' => $id])->update($data);
        } catch(\Exception $e) {
            throw new ApiException('网络忙，请重试', 500, config('code.error')); // $e->getMessage()
        }
        if (false === $result) {
            return show(config('code.error'), '更新失败', '', 403);
        } else {
            return show(config('code.success'), '更新成功', '', 201);
        }
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
            // 显示指定的广告屏合作商
            try {
                $data = Db::name('user_partner')->where(['user_id' => $id])->find();
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
            if ($data['status'] == config('code.status_enable')) { // 启用
                return show(config('code.error'), '删除失败：广告屏合作商已启用', '', 403);
            }
            if (!empty($data['device_ids'])) { // 已合作的广告屏、份额及协议
                return show(config('code.error'), '删除失败：广告屏合作商已合作广告屏', '', 403);
            }

            // 软删除
            if ($data['is_delete'] != config('code.is_delete')) {
                // 捕获异常
                try {
                    $result = Db::name('user_partner')->where(['user_id' => $id])->update(['is_delete' => config('code.is_delete')]);
                } catch (\Exception $e) {
                    throw new ApiException($e->getMessage(), 500, config('code.error'));
                }

                if (!$result) {
                    return show(config('code.error'), '移除失败', '', 403);
                } else {
                    return show(config('code.success'), '移除成功', '');
                }
            }

            // 真删除
            try {
                $result = Db::name('user_partner')->where(['user_id' => $id])->delete();
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
