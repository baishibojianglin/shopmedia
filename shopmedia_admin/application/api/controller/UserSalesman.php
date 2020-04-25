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
}
