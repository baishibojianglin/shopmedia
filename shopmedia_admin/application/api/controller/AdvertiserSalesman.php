<?php

namespace app\api\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Db;
use think\Model;

/**
 * api模块 广告主业务员 控制器类
 * Class AdvertiserSalesman
 * @package app\api\controller
 */
class AdvertiserSalesman extends AuthBase
{
    /**
     * 获取指定的广告主业务员
     *
     * @return \think\Response
     */
    public function read()
    {
        // 判断为GET请求
        if (!request()->isGet()) {
            return show(config('code.error'), '请求不合法', '', 400);
        }

        // 传入的参数
        $param = input('param.');

        // 查询条件
        $map = [];
        $map['status'] = config('code.status_enable');
        $map['uid'] = (int)$param['user_id'];
        $map['role_id'] = 5;

        // 获取广告主业务员信息
        $data = Db::name('user_salesman')->where($map)->find();

        if (!$data) {
            return show(config('code.error'), '业务员不存在', '', 404);
        }

        return show(config('code.success'), 'OK', $data);
    }

    /**
     * 统计广告主业务员广告数
     * @return \think\response\Json
     */
    public function getAdvertiserSalesmanAdCount()
    {
        // 获取当前登录广告主业务员ID
        $salesmanMap = ['uid' => $this->user['user_id'], 'role_id' => 5];
        $salesman = Db::name('user_salesman')->field('id')->where($salesmanMap)->find();
        $salesmanId = $salesman['id'];

        // 统计广告数
        try {
            $totalAdCount = $this->_getAdvertiserSalesmanAdCount(['ua.salesman_id' => $salesmanId]); // 广告数量合计
            $enableAdCount = $this->_getAdvertiserSalesmanAdCount(['ua.salesman_id' => $salesmanId, 'ad.audit_status' => 1]); // 启用的广告数量
            $pendingAdCount = $this->_getAdvertiserSalesmanAdCount(['ua.salesman_id' => $salesmanId, 'ad.audit_status' => 0]); // 待审核的广告数量
            $rejectAdCount = $this->_getAdvertiserSalesmanAdCount(['ua.salesman_id' => $salesmanId, 'ad.audit_status' => 2]); // 驳回的广告数量
        } catch (\Exception $e) {
            return show(config('code.error'), '请求异常', $e->getMessage(), 500);
        }

        $data = ['total_ad_count' => $totalAdCount, 'enable_ad_count' => $enableAdCount, 'pending_ad_count' => $pendingAdCount, 'reject_ad_count' => $rejectAdCount];
        return show(config('code.success'), 'OK', $data);
    }

    /**
     * 统计广告主业务员广告数
     * @param array $map
     * @return int|string
     * @throws \think\Exception
     */
    private function _getAdvertiserSalesmanAdCount($map = [])
    {
        $adCount = Db::name('ad')->alias('ad')
            ->join('__USER_ADVERTISER__ ua', 'ua.id = ad.advertiser_id') // 广告主
            ->where($map)
            ->count();

        return $adCount;
    }

    /**
     * 获取（下级）广告主业务员列表
     */
    public function getAdvertiserSalesmanList()
    {
        // 获取当前登录广告主业务员ID
        $salesmanMap = ['uid' => $this->user['user_id'], 'role_id' => 5];
        $salesman = Db::name('user_salesman')->field('id')->where($salesmanMap)->find();
        $salesmanId = $salesman['id'];

        // 获取广告主业务员列表
        $map = ['us.parent_id' => $salesmanId];
        $data = Db::name('user_salesman')->alias('us')
            ->field('us.id, us.uid, u.phone')
            ->join('__USER__ u', 'u.user_id = us.uid')
            ->where($map)
            ->select();
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $map1 = ['ua.salesman_id' => $value['id'], 'ad.audit_status' => 1];
                $data[$key]['ad_count'] = $this->_getAdvertiserSalesmanAdCount($map1);
            }
        }

        return show(config('code.success'), 'OK', $data);
    }
}
