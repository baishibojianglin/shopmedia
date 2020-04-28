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
     * 获取用户对应的业务员信息
     */
    public function applyPartner(){
         $form=input();
       
        //通过邀请码查询业务员id
        $matchcode['invitation_code']=$form['invitation_code'];
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

         if( !empty($partnerlist) && ($partnerlist['audit_status']==2) ){
            $message['status']=0;
            $message['words']='该账号此业务被冻结';
            return json($message);
         } 

         if( !empty($partnerlist) && ($partnerlist['status']==0) ){
            $message['status']=0;
            $message['words']='该账号此业务被禁用';
            return json($message);
         } 


         if( !empty($partnerlist) && ($partnerlist['audit_status']==1) ){
            $message['status']=0;
            $message['words']='已完成申请';
            return json($message);
         } 

         if( !empty($partnerlist) && ($partnerlist['audit_status']==0) ){
            $message['status']=0;
            $message['words']='已申请，正在审核中...';
            return json($message);
         } 


        //入库
        $data['user_id']=$form['user_id'];
        $data['create_time']=time();
        $data['status']=1;
        $data['audit_status']=0;
        $id=Db::name('user_partner')->insert($data); 


        if($id>0){
            $message['status']=1;
            $message['words']='申请成功';
        }else{
            $message['status']=0;
            $message['words']='申请失败';
        }
        return json($message);



    }



    /**
     * 显示指定的用户资源
     * 用户的基本信息非常隐私，需要加密处理
     * @param int $id
     * @return \think\Response
     */
    public function read($id = 0)
    {
        // 处理数据
        // 用户角色
        $roleIds = $this->user['role_ids'];
        $userRole = model('UserRole')->where(['id' => ['in', $roleIds]])->column('id, title');
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
