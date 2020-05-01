<?php

namespace app\api\controller;

use app\common\lib\Aes;
use app\common\lib\exception\ApiException;
use app\common\lib\IAuth;
use think\Controller;
use think\Model;
use think\Request;
use think\Db;

/**
 * api模块客户端用户控制器类
 * Class User
 * @package app\api\controller\v1
 */
class User extends AuthBase
{


    /**
     * 获取用户角色信息
     */
    public function getRole(){
        $form=input();
        $match['user_id']=$form['user_id'];
        $userlist=Db::name('user')->where($match)->field('role_ids')->find();
        if(!empty($userlist)){
            $message['status']=1;
            $message['data']=$userlist;
            return json($message);
        }else{
            $message['status']=0;
            $message['words']='角色获取失败';
            return json($message);
        }
    }


    /**
     * 申请成为店铺合作者
     */
    public function applyShop(){
        $form=input();
       
        //通过邀请码查询业务员id
        $matchcode['invitation_code']=$form['invitation_code'];
        $matchcode['role_id']=6;
        $salesmanlist=Db::name('user_salesman')->where($matchcode)->find();
        //验证邀请码是否存在
        if(!empty($salesmanlist)){
            $data['salesman_id']=$salesmanlist['id'];
        }else{
            $message['status']=0;
            $message['words']='认证码不正确';
            return json($message);
        }

         //判断是否重复申请
         $matchuserid['user_id']=$form['user_id'];
         $partnerlist=Db::name('user_shopkeeper')->where($matchuserid)->find();


         if( !empty($partnerlist) && ($partnerlist['status']==0) ){
            $message['status']=0;
            $message['words']='该账号被禁用';
            return json($message);
         } 


         if( !empty($partnerlist) && ($partnerlist['status']==1) ){
            $message['status']=0;
            $message['words']='已经是合作者';
            return json($message);
         } 

         if( !empty($partnerlist) && ($partnerlist['status']==2) ){
            $message['status']=0;
            $message['words']='已申请，正在审核中...';
            return json($message);
         } 

         if( !empty($partnerlist) && ($partnerlist['status']==3) ){
            $message['status']=0;
            $message['words']='该账号不支持该业务';
            return json($message);
         }

        // 入库操作
        /* 手动控制事务 s */
        // 启动事务
        Db::startTrans();
        try {
            // 新增广告屏合作商
            $data['user_id'] = $form['user_id'];
            $data['create_time'] = time();
            $data['status'] = 2;
            $res[0] = $id = Db::name('user_partner')->insert($data);

            // 更新用户角色集合role_ids
            $user = model('User')->field('role_ids')->find($form['user_id']);
            $roleIds = explode(',', $user['role_ids']);
            if (!in_array(2, $roleIds)) {
                array_push($roleIds, 2); // 新增广告屏合作商角色
            }
            $data1['role_ids'] = implode(',', $roleIds);
            $res[1] = Db::name('user')->where(['user_id' => $form['user_id']])->update($data1);

            // 任意一个表写入失败都会抛出异常，TODO：是否可以不做该判断
            if (in_array(0, $res)) {
                return show(config('code.error'), '申请失败', '', 403);
            }

            // 提交事务
            Db::commit();
            return show(config('code.success'), '申请成功', '', 201);
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return show(config('code.error'), '网络忙，请重试', '', 500);
        }
        /* 手动控制事务 e */
    }







    /**
     * 申请成为广告屏合作者
     */
    public function applyPartner(){
         $form=input();
       
        //通过邀请码查询业务员id
        $matchcode['invitation_code']=$form['invitation_code'];
        $matchcode['role_id']=4;
        $salesmanlist=Db::name('user_salesman')->where($matchcode)->find();
        //验证邀请码是否存在
        if(!empty($salesmanlist)){
            $data['salesman_id']=$salesmanlist['id'];
        }else{
            $message['status']=0;
            $message['words']='认证码不正确';
            return json($message);
        }

         //判断是否重复申请
         $matchuserid['user_id']=$form['user_id'];
         $partnerlist=Db::name('user_partner')->where($matchuserid)->find();


         if( !empty($partnerlist) && ($partnerlist['status']==0) ){
            $message['status']=0;
            $message['words']='该账号被禁用';
            return json($message);
         } 


         if( !empty($partnerlist) && ($partnerlist['status']==1) ){
            $message['status']=0;
            $message['words']='已经是合作者';
            return json($message);
         } 

         if( !empty($partnerlist) && ($partnerlist['status']==2) ){
            $message['status']=0;
            $message['words']='已申请，正在审核中...';
            return json($message);
         } 

         if( !empty($partnerlist) && ($partnerlist['status']==3) ){
            $message['status']=0;
            $message['words']='该账号不支持该业务';
            return json($message);
         }

        // 入库操作
        /* 手动控制事务 s */
        // 启动事务
        Db::startTrans();
        try {
            // 新增广告屏合作商
            $data['user_id'] = $form['user_id'];
            $data['create_time'] = time();
            $data['status'] = 2;
            $res[0] = $id = Db::name('user_partner')->insert($data);

            // 更新用户角色集合role_ids
            $user = model('User')->field('role_ids')->find($form['user_id']);
            $roleIds = explode(',', $user['role_ids']);
            if (!in_array(2, $roleIds)) {
                array_push($roleIds, 2); // 新增广告屏合作商角色
            }
            $data1['role_ids'] = implode(',', $roleIds);
            $res[1] = Db::name('user')->where(['user_id' => $form['user_id']])->update($data1);

            // 任意一个表写入失败都会抛出异常，TODO：是否可以不做该判断
            if (in_array(0, $res)) {
                return show(config('code.error'), '申请失败', '', 403);
            }

            // 提交事务
            Db::commit();
            return show(config('code.success'), '申请成功', '', 201);
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return show(config('code.error'), '网络忙，请重试', '', 500);
        }
        /* 手动控制事务 e */
    }



    /**
     * 显示指定的用户资源
     * 用户的基本信息非常隐私，需要加密处理
     * @param int $id
     * @return \think\Response
     */
    public function read($id = 0)
    {
        // 处理数据：获取用户角色集合
        $roleIds = $this->user['role_ids']; // 用户角色ID集合
        // 查询条件
        $map = [
            'id' => ['in', $roleIds],
            'status' => config('code.status_enable')
        ];
        $userRole = model('UserRole')->field('id role_id, title role_title')->where($map)->select();
        foreach($userRole as $key => $value) {
            switch($value['role_id']){
                case 2: // 广告屏合作商
                    $userPartner = Db::name('user_partner')->where(['user_id' => $this->user['user_id']])->find();
                    $userRole[$key]['user_role'] = $userPartner;
                    break;
                case 3: // 店家
                    $userShopkeeper = Db::name('user_shopkeeper')->where(['user_id' => $this->user['user_id']])->find();
                    $userRole[$key]['user_role'] = $userShopkeeper;
                    break;
                case 4 || 5 || 6: // 业务员
                    $userSalesman = Db::name('user_salesman')->where(['uid' => $this->user['user_id'], 'role_id' => $value['role_id']])->find();
                    $userRole[$key]['user_role'] = $userSalesman;
                    break;
                default:
                    // 其他情况默认执行代码
            }
        }
        $this->user['user_roles'] = $userRole;

        // AES加密
        $aes = new Aes(); // 实例化Aes
        $data = $aes->encrypt($this->user);
        //$data = Aes::encrypt($this->user);

        return show(config('code.success'), 'OK', $data);
    }

    /**
     * 保存更新的用户资源
     *
     * @param Request $request
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function update(Request $request, $id)
    {
        // 判断为PUT请求
        if (request()->isPut()) {
            // 传入的参数
            $param = input('param.');

            // TODO validate验证
            /*$validate = validate('User');
            if (!$validate->check($param, [], 'update')) { // update为验证场景
                return show(config('code.error'), $validate->getError(), [], 403);
            }*/

            // 判断传入的参数是否存在
            $data = [];
            if (!empty($param['file'])) { // 头像
                $data['avatar'] = trim($param['file']);
            }
            if (!empty($param['user_name'])) { // 用户名 // TODO 如果用户名是唯一性的，在表单输入用户名的时候做用户名唯一性验证接口
                $data['user_name'] = trim($param['user_name']);
            }
            if (isset($param['gender'])) { // 性别
                $data['gender'] = $param['gender'];
            }
            if (!empty($param['signature'])) { // 个性签名
                $data['signature'] = trim($param['signature']);
            }
            if (!empty($param['password'])) { // 密码 更新密码的接口
                // TODO 密码在传输过程中需要（AES）加密
                $data['password'] = IAuth::encrypt($param['password']);
            }

            if (empty($data)) {
                return show(config('code.error'), '数据不合法', [], 404);
            }

            // 更新用户
            try { // 捕获异常
                $result = model('User')->save($data, ['user_id' => $this->user->user_id]); // 更新
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
            if ($result) {
                return show(config('code.success'), '更新成功', [], 201);
            } else {
                return show(config('code.error'), '更新失败', [], 403);
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }
}
