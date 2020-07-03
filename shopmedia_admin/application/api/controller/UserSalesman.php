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
     * 获取店铺业务员开拓店铺数量
     * @return \think\response\Json
     * @throws \think\Exception
     */
    public function getShopCount()
    {
        $form = input();
        $match['uid'] = $form['user_id'];
        $match['role_id'] = $form['role_id'];
        $list = Db::name('user_salesman')->where($match)->find();//获取业务员主键id
        if(!empty($list)){
            $matchid['salesman_id'] = $list['id'];
            $partner = Db::name('user_shopkeeper')->where($matchid)->field('user_id')->select();//该业务员发展的客户
            $totalShopCount = 0; // 店铺数量合计
            $enableShopCount = 0; // 启用的店铺数量
            $pendingShopCount = 0; // 待审核的店铺数量
            $rejectShopCount = 0; // 驳回的店铺数量
            if(!empty($partner)){
                foreach ($partner as $key => $value) {
                    $totalShopCount = $totalShopCount + Db::name('shop')->where(['user_id' => $value['user_id']])->count();
                    $enableShopCount = $enableShopCount + Db::name('shop')->where(['user_id' => $value['user_id'], 'status' => 1])->count();
                    $pendingShopCount = $pendingShopCount + Db::name('shop')->where(['user_id' => $value['user_id'], 'status' => 2])->count();
                    $rejectShopCount = $rejectShopCount + Db::name('shop')->where(['user_id' => $value['user_id'], 'status' => 3])->count();
                }
            }
            $data = ['total_shop_count' => $totalShopCount, 'enable_shop_count' => $enableShopCount, 'pending_shop_count' => $pendingShopCount, 'reject_shop_count' => $rejectShopCount];
            return show(config('code.success'), 'OK', $data);
        }else{
            return show(config('code.error'), '业务员不存在', '', 404);
        }
    }

    /**
     * 获取店铺业务员开拓店铺列表
     */
    public function getSalesmanShopList()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的参数
            $param = input('param.');

            // 查询条件
            $map = [];
            $map['usa.uid'] = $param['user_id'];
            $map['usa.role_id'] = $param['role_id'];
            if (isset($param['shop_status'])) {
                $map['s.status'] = (int)$param['shop_status'];
            }

            // 获取店铺列表数据
            $data = model('Shop')->alias('s')
                ->field('s.*')
                ->join('__USER_SHOPKEEPER__ usk', 's.shopkeeper_id = usk.id')
                ->join('__USER_SALESMAN__ usa', 'usk.salesman_id = usa.id')
                ->where($map)
                ->select();
            if (!$data) {
                return show(config('code.error'), 'Not Found', '', 404);
            }

            $status = config('code.status');
            foreach ($data as $key => $value) {
                // 处理数据
                $data[$key]['status_msg'] = $status[$value['status']]; // 定义status_msg
            }


            return show(config('code.success'), 'OK', $data);
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 获取广告屏业务员销售数量
     * @return \think\response\Json
     * @throws \think\Exception
     */
    public function getSaleCount()
    {
        $form=input();
        $match['uid']=$form['user_id'];
        $match['role_id']=$form['role_id'];
        $list=Db::name('user_salesman')->where($match)->find();//获取业务员主键id
        if(!empty($list)){
            $matchid['salesman_id']=$list['id'];
            $partner=Db::name('user_partner')->where($matchid)->field('user_id')->select();//该业务员发展的客户
            $ordercount=0;
            if(!empty($partner)){
                foreach ($partner as $key => $value) {
                    $matchorder['user_id']=$value['user_id'];
                    $matchorder['order_status']=1;
                    $ordercount=$ordercount + Db::name('partner_order')->where($matchorder)->count();
                }
            }
            return show(config('code.success'), 'OK',$ordercount);

        }else{
            return show(config('code.error'), '业务员不存在', '', 404);
        }
    }

    /**
     * 获取广告屏业务员基本信息
     * @return \think\response\Json
     */
    public function getSaleInfo(){
        $form=input();
        $match['uid']=$form['user_id'];
        $match['role_id']=$form['role_id'];
        $list=Db::name('user_salesman')->where($match)->find();
        if(!empty($list)){
            return show(config('code.success'), 'OK', $list);
        }else{
            return show(config('code.error'), '业务员不存在', '', 404);
        }
    }

    /**
     * 获取业务员角色状态
     * @return \think\response\Json
     */
    public function getRoleStatus(){
        $form=input();
        $match['uid']=$form['user_id'];
        $match['role_id']=$form['role_id'];
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

    /**
     * 获取广告屏合作商业务员列表
     */
    public function partnerSalesmanList()
    {
        // 判断为GET请求
        if (!request()->isGet()) {
            return show(config('code.error'), '请求不合法', '', 400);
        }

        // 传入的参数
        $param = input('param.');

        // 查询条件
        $map = [];
        $map['us.role_id'] = 4; // 广告屏合作商业务员
        if (isset($param['parent_id'])) {
            $map['us.parent_id'] = intval($param['parent_id']);
        }

        // 广告屏合作商业务员列表
        $data = Db::name('user_salesman')->alias('us')
            ->field('us.*, u.phone')
            ->join('__USER__ u', 'us.uid = u.user_id', 'LEFT') // 所属用户
            ->where($map)->select();
        foreach ($data as $key => $value) {
            // 获取广告屏合作商ID集合
            $partnerIds = Db::name('user_partner')->where(['salesman_id' => $value['id']])->column('id');

            // 统计广告屏今日销售数量
            /*$beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y')); // 今日开始时间
            $endToday = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1; // 今日结束时间*/
            $data[$key]['today_count'] = Db::name('partner_order')->distinct('device_id')
                ->where([
                    'partner_id' => ['in', $partnerIds],
                    'order_status' => 1,
                    'pay_time' => 'today' //'pay_time' => ['between time', [$beginToday, $endToday]]
                ])
                ->count('device_id');

            // 统计广告屏累计销售数量
            $data[$key]['total_count'] = Db::name('partner_order')->distinct('device_id')
                ->where([
                    'partner_id' => ['in', $partnerIds],
                    'order_status' => 1
                ])
                ->count('device_id');
        }

        return show(config('code.success'), 'OK', $data);
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
     * 获取店铺业务员列表
     * @return \think\response\Json
     * @throws \think\Exception
     */
    public function shopkeeperSalesmanList()
    {
        // 判断为GET请求
        if (!request()->isGet()) {
            return show(config('code.error'), '请求不合法', '', 400);
        }

        // 传入的参数
        $param = input('param.');

        // 查询条件
        $map = [];
        $map['us.role_id'] = 6; // 店铺业务员
        if (isset($param['user_id'])) {
            $userId = intval($param['user_id']);
            $userSalesman = Db::name('user_salesman')->field('id')->where(['uid' => $userId, 'role_id' => 6])->find();
            $map['us.parent_id'] = $userSalesman['id'];
        }

        // 广告屏合作商业务员列表
        $data = Db::name('user_salesman')->alias('us')
            ->field('us.*, u.phone')
            ->join('__USER__ u', 'us.uid = u.user_id', 'LEFT') // 所属用户
            ->where($map)->select();

        foreach ($data as $key => $value) {
            // 获取店家ID集合
            $shopkeeperIds = Db::name('user_shopkeeper')->where(['salesman_id' => $value['id']])->column('id');

            // 统计店铺业务员开拓的店铺数量
            $data[$key]['total_count'] = Db::name('shop')->distinct('shop_id')
                ->where([
                    'shopkeeper_id' => ['in', $shopkeeperIds],
                    'status' => ['not in', [2, 3]]
                ])
                ->count('shop_id');
        }

        return show(config('code.success'), 'OK', $data);
    }
}
