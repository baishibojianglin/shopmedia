<?php

namespace app\api\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Db;
use think\Model;

/**
 * api模块客户端用户（业务员）控制器类
 * Class UserSalesman
 * @package app\api\controller
 */
class UserSalesman extends AuthBase
{


   /**
   *获取业务员角色状态
   */
   public function getRoleStatus(){
        $form=input();
        $match['uid']=$form['user_id'];
        $list=Db::name('user_salesman')->where($match)->field('status')->find();
        $data['status']=$list['status'];
        return show(config('code.success'), 'OK', $data);

   }




    /**
     * 获取收入
     * @return \think\Response
     */
    public function getMoney()
    {
        $form=input();
        $match['user_id']=$form['user_id'];
        $userlist=Db::name('user')->where($match)->field('income,money,cash,role_ids')->find();
        if(!empty($userlist)){
            $value['income']=$userlist['income'];
            $value['money']=$userlist['money'];
            $value['cash']=$userlist['cash'];
            $value['role_ids']=$userlist['role_ids'];
            $message['data']=$value;
            $message['status']=1;
            return json($message);
        }else{
            $message['status']=0;
            $message['words']='获取失败';
            return json($message);
        }

    }



    /**
     * 获取业务员推广广告屏的收入
     * @return \think\Response
     */
    public function getMoneyDevice()
    {
        $form=input();
        $match['salesman_id']=$form['user_id'];
        $sumincome=0;
        $userlist=Db::name('user_partner')->where($match)->field('income')->select();
        //未开单
        if(empty($userlist)){
            $message['status']=1;
            $message['data']=$sumincome;
            return json($message);
        }

        foreach($userlist as $key=>$value){
            $sumincome += $value['income'];
        }
        
        $message['status']=1;
        $message['data']=$sumincome;
        return json($message);


    }


    /**
     * 获取业务员推广店铺的收入
     * @return \think\Response
     */
    public function getMoneyShop()
    {
        $form=input();
        $match['salesman_id']=$form['user_id'];
        $userlist=Db::name('user_shopkeeper')->where($match)->field('income')->select();
        $sumincome=0;
        foreach($userlist as $key=>$value){
            $sumincome += $value['income'];
        }
        if(!empty($userlist)){
            $message['data']=$sumincome;
            $message['status']=1;
            return json($message);
        }else{
            $message['status']=0;
            $message['words']='获取失败';
            return json($message);
        }


    }





    /**
     * 获取指定的广告屏合作商业务员
     *
     * @return \think\Response
     */
    public function partnerSalesman()
    {
        // 判断为GET请求
        if (!request()->isGet()) {
            return show(config('code.error'), '请求不合法', '', 400);
        }

        // 传入的参数
        $param = input('param.');

        // 获取广告屏合作商信息
        $partnerMap = []; // 查询条件
        if (isset($param['user_id'])) {
            $partnerMap['user_id'] = intval($param['user_id']);
        }
        if (isset($param['role_id']) && intval($param['role_id']) == 2) {
            $partnerMap['role_id'] = intval($param['role_id']);
        }
        $partner = Db::name('user_partner')->field('salesman_id')->where($partnerMap)->find();

        // 获取广告屏合作商业务员信息
        if ($partner['salesman_id']) {
            try {
                $data = Db::name('user_salesman')->alias('us')
                    ->field('u.user_name, u.phone')
                    ->join('__USER__ u', 'us.uid = u.user_id', 'LEFT')
                    ->where(['us.id' => $partner['salesman_id']])
                    ->find();
            } catch (\Exception $e) {
                return show(config('code.error'), $e->getMessage(), '', 500);
                //throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
        }
        if (!$data) {
            return show(config('code.error'), '业务员不存在', '', 404);
        }

        return show(config('code.success'), 'OK', $data);
    }

    /**
     * 申请成为业务员
     * @return \think\response\Json
     */
    public function applySalesman()
    {
        // 判断为POST请求
        if (!request()->isPost()) {
            return show(config('code.error'), '请求不合法', '', 400);
        }

        // 传入的参数
        $param = input('param.');
        $param['user_id'] = intval($param['user_id']);
        $param['role_id'] = intval($param['role_id']);

        // 判断传入的参数是否存在合法
        // 判断是否是下级业务员邀请码
        $parentSalesmanMap['son_invitation_code'] = trim($param['invitation_code']);
        $parentSalesman = Db::name('user_salesman')->where($parentSalesmanMap)->find();
        if (!$parentSalesman) {
            return show(config('code.error'), '认证码错误', '', 401);
        }

        // 判断角色是否匹配
        if ($param['role_id'] != $parentSalesman['role_id']) {
            return show(config('code.error'), '认证码错误，业务员类型不匹配', '', 401);
        }

        // 判断是否重复申请该业务员
        $salesmanMap['uid'] = $param['user_id'];
        $salesmanMap['role_id'] = $param['role_id'];
        $salesman = Db::name('user_salesman')->where($salesmanMap)->find();
        if ($salesman) {
            switch ($salesman['status']) {
                case config('code.status_disable'):
                    return show(config('code.error'), '业务员已存在，账号被禁用', '', 403);
                    break;
                case config('code.status_enable'):
                    return show(config('code.error'), '业务员已存在，不能重复申请', '', 403);
                    break;
                case config('code.status_pending'):
                    return show(config('code.error'), '业务员已存在，正在审核中…', '', 403);
                    break;
                case config('code.status_reject'):
                    return show(config('code.error'), '业务员申请未通过', '', 403);
                    break;
                default:
                    // 默认执行
            }
        }

        // 入库操作
        /* 手动控制事务 s */
        // 启动事务
        Db::startTrans();
        try {
            // 新增业务员

            // （下级业务员）邀请码
            // 获取（下级业务员）邀请码集合
            $sonInvitationCodes = Db::name('user_salesman')->column('son_invitation_code');
            // 生成唯一（下级业务员）邀请码，加前缀 1 用于区别于（目标客户）邀请码（两种邀请码也必须不同）
            $data['son_invitation_code'] = uniqueRand('1', 10000, 99999, $sonInvitationCodes);

            // （目标客户）邀请码
            // 获取（下级业务员）邀请码集合
            $invitationCodes = Db::name('user_salesman')->column('invitation_code');
            // 生成唯一（目标客户）邀请码，加前缀 2 用于区别于（下级业务员）邀请码（两种邀请码也必须不同）
            $data['invitation_code'] = uniqueRand('2', 10000, 99999, $invitationCodes);

            $data['uid'] = $param['user_id'];
            $data['role_id'] = $param['role_id'];
            $data['company_id'] = $parentSalesman['company_id'];
            $data['parent_id'] = $parentSalesman['id'];
            $data['create_time'] = time();
            $data['status'] = 2;
            $res[0] = $id = Db::name('user_salesman')->insert($data);

            // 更新用户角色集合role_ids（新增业务员角色）
            $user = model('User')->field('role_ids')->find($param['user_id']);
            $roleIds = explode(',', $user['role_ids']);
            if (!in_array($param['role_id'], $roleIds)) {
                array_push($roleIds, $param['role_id']); // 新增业务员角色
            }
            $data1['role_ids'] = implode(',', $roleIds);
            $res[1] = Db::name('user')->where(['user_id' => $param['user_id']])->update($data1);

            // 任意一个表写入失败都会抛出异常，TODO：是否可以不做该判断
            if (in_array(0, $res)) {
                return show(config('code.error'), '申请失败', '', 403);
            }

            // 提交事务
            Db::commit();
            return show(config('code.success'), '申请成功，已提交审核', '', 201);
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return show(config('code.error'), '网络忙，请重试', '', 500);
        }
        /* 手动控制事务 e */
    }
}
