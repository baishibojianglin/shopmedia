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

        // 判断传入的参数是否存在

    }
}
