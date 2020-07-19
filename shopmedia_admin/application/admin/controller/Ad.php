<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use app\common\lib\IAuth;
use think\Controller;
use think\Db;
use think\Request;

/**
 * admin模块广告管理控制器类
 * Class Ad
 * @package app\admin\controller
 */
class Ad extends Base
{
    /**
     * 显示广告资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的参数
            $param = input('param.');
            if (isset($param['size'])) { // 每页条数
                $param['size'] = intval($param['size']);
            }

            // 查询条件
            $map = [];
            if (!empty($param['ad_name'])) { // 广告名称
                $map['a.ad_name'] = ['like', '%' . trim($param['ad_name']) . '%'];
            }
            if (isset($param['is_delete'])) { // 是否删除
                $map['a.is_delete'] = $param['is_delete'];
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取广告列表数据
            try {
                $data = model('Ad')->getAd($map, $this->size);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', '', 500); // $e->getMessage()
            }

            if ($data) {
                // 处理数据
                $auditStatus = config('code.audit_status'); // 审核状态：0待审核，1通过，2驳回
                $adCate = config('ad.ad_cate'); // 广告所属行业类别
                foreach ($data as $key => $value) {
                    $data[$key]['audit_status_msg'] = $auditStatus[$value['audit_status']]; // 定义审核状态信息
                    $data[$key]['ad_cate_name'] = $value['ad_cate_id'] ? $adCate[$value['ad_cate_id']] : ''; // 定义广告类别名称

                    // 定义店铺所属行业类别名称集合
                    $adCateNames = [];
                    if ($value['shop_cate_ids']) {
                        $shopCateIds = explode(',', $value['shop_cate_ids']);
                        foreach ($adCate as $k => $v) {
                            foreach ($shopCateIds as $k1 => $v1) {
                                if ($k == $v1) {
                                    $adCateNames[] = $adCate[$v1];
                                }
                            }
                        }
                    }
                    $data[$key]['ad_cate_names'] = implode('、', $adCateNames);

                    $data[$key]['start_datetime'] = $value['start_datetime'] ? date('Y-m-d H:i:s', $value['start_datetime']) : ''; // 投放时间
                    $data[$key]['end_datetime'] = $value['end_datetime'] ? date('Y-m-d H:i:s', $value['end_datetime']) : ''; // 结束时间
                    $data[$key]['audit_time'] = $value['audit_time'] ? date('Y-m-d H:i:s', $value['audit_time']) : ''; // 审核时间
                }

                return show(config('code.success'), 'OK', $data);
            } else {
                return show(config('code.error'), 'Not Found', $data, 404);
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }

    /**
     * 保存新建的广告资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        // 判断为POST请求
        if(request()->isPost()){
            // 传入的参数
            $param = input('post.');

            // 入库操作
            /* 手动控制事务 s */
            // 启动事务
            Db::startTrans();
            try {
                $res = [];

                // 根据广告主电话号码查询广告主角色及其所属用户是否存在
                $advertiserUser = Db::name('user')->where(['phone' => trim($param['phone'])])->find();
                if (empty($advertiserUser)) { // 广告主所属用户不存在时，广告主角色一定不存在，则创建广告主角色及其所属用户
                    // 创建广告主所属用户
                    $advertiserUserData = [
                        'user_name' => trim($param['advertiser']),
                        'role_ids' => 7, // 广告主角色ID
                        'phone' => trim($param['phone']),
                        'phone_verified' => 1,
                        'password' => IAuth::encrypt(trim($param['phone'])),
                        'status' => config('code.status_enable'),
                        'create_time' => time(), // 创建时间
                        'create_ip' => request()->ip() // 创建IP
                    ];
                    $res[0] = $advertiserUserID = Db::name('user')->strict(true)->insertGetId($advertiserUserData);

                    // 创建广告主
                    $advertiserData = [
                        'user_id' => $advertiserUserID,
                        'salesman_id' => (int)$param['salesman_id'],
                        'status' => config('code.status_enable'),
                        'create_time' => time()
                    ];
                    $res[1] = $advertiserID = Db::name('user_advertiser')->insertGetId($advertiserData);
                } else { // 广告主所属用户存在时
                    // 判断广告主角色是否存在
                    $roleIdsArr = explode(',', $advertiserUser['role_ids']);
                    if (!in_array(7, $roleIdsArr)) {
                        array_push($roleIdsArr, 7);
                        $roleIdsStr = implode(',', $roleIdsArr);
                        $res[2] = Db::name('user')->where(['user_id' => $advertiserUser['user_id']])->update(['role_ids' => $roleIdsStr]);
                    }

                    $advertiser = Db::name('user_advertiser')->where(['user_id' => $advertiserUser['user_id']])->find();
                    if (empty($advertiser)) { // 广告主角色不存在时，创建广告主角色
                        // 创建广告主
                        $advertiserData = [
                            'user_id' => $advertiserUser['user_id'],
                            'salesman_id' => (int)$param['salesman_id'],
                            'status' => config('code.status_enable'),
                            'create_time' => time()
                        ];
                        $res[3] = $advertiserID = Db::name('user_advertiser')->insertGetId($advertiserData);
                    } else {
                        $advertiserID = $advertiser['id'];
                    }
                }

                // 创建广告
                $adData['ad_name'] = trim($param['ad_name']);
                $adData['ad_cate_id'] = intval($param['ad_cate_id']);
                $adData['ad_price'] = floatval($param['ad_price']);
                $adData['advertiser'] = trim($param['advertiser']);
                $adData['advertiser_id'] = $advertiserID;
                $adData['phone'] = trim($param['phone']);
                $adData['discount_ratio'] = floatval($param['discount_ratio']);
                if (isset($param['ad_datetime']) && !empty($param['ad_datetime'])) {
                    $adData['start_datetime'] = strtotime($param['ad_datetime'][0]); // 投放开始时间
                    $adData['end_datetime'] = strtotime($param['ad_datetime'][1]); // 投放结束时间
                    $adData['play_days'] = intval(($adData['end_datetime'] - $adData['start_datetime']) / 86400); // 投放天数
                }
                if (isset($param['ad_time']) && !empty($param['ad_time'])) {
                    $adData['start_time'] = date('H:i:s', strtotime($param['ad_time'][0])); // 每日播放时间段（开始时间）
                    $adData['end_time'] = date('H:i:s', strtotime($param['ad_time'][1])); // 每日播放时间段（结束时间）
                }
                if (!empty($param['shop_cate_ids'])) { // 投放店铺类别ID集合
                    $adData['shop_cate_ids'] = implode(',', $param['shop_cate_ids']);
                }
                if (!empty($param['region_ids'])) { // 投放区域ID集合（含全选与半选）
                    $adData['region_ids'] = json_encode([
                        'checked' => $param['region_ids'][0], // 全选
                        'half' => $param['region_ids'][1] // 半选
                    ]);
                }
                if (!empty($param['device_ids'])) { // 投放广告屏ID集合
                    $adData['device_ids'] = implode(',', $param['device_ids']);
                }
                // 投放店铺（所属行业）类别ID集合
                $adCate = config('ad.ad_cate');
                $adData['shop_cate_ids'] = [];
                foreach ($adCate as $key => $value) {
                    if ($key != $adData['ad_cate_id']) {
                        array_push($adData['shop_cate_ids'], $key);
                    }
                }
                $adData['shop_cate_ids'] = implode(',', $adData['shop_cate_ids']);
                $res[4] = $adID = Db::name('ad')->insertGetId($adData);

                // 任意一个表写入失败都会抛出异常，TODO：是否可以不做该判断
                if (in_array(0, $res)) {
                    return show(config('code.error'), '新增失败', $res, 403);
                }

                // 提交事务
                Db::commit();
                return show(config('code.success'), '新增成功', '', 201);
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return show(config('code.error'), '请求异常' . $e->getMessage(), '', 500);
            }
            /* 手动控制事务 e */
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 显示指定的广告资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            try {
                $data = model('Ad')->find($id);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', '', 500); // $e->getMessage()
            }

            if ($data) {
                // 处理数据
                $data['ad_datetime'] = [ // 定义投放时间数组
                    date('c', $data['start_datetime']), // UNIX 转换成 UTC
                    date('c', $data['end_datetime'])
                ];
                $data['ad_time'] = [ // 定义播放时间段数组
                    date('c', strtotime($data['start_time'])),
                    date('c', strtotime($data['end_time']))
                ];
                $data['shop_cate_ids'] = explode(',', $data['shop_cate_ids']); // 投放店铺类别ID集合
                $data['device_ids'] = explode(',', $data['device_ids']); // 投放广告屏ID集合

                return show(config('code.success'), 'ok', $data);
            } else {
                return show(config('code.error'), 'Not Found', $data, 404);
            }
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 保存更新的广告资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        // 判断为PUT请求
        if (request()->isPut()) {
            // 传入的数据
            $param = input('param.');

            // validate验证数据合法性：判断是审核状态还是更新其他数据
            /*$validate = validate('');
            $rules = [];
            $scene = 'update';
            if (isset($param['audit_status'])) { // 审核操作
                $rules = ['audit_status' => 'require'];
                $scene = [];
            }
            if (!$validate->check($param, $rules, $scene)) {
                return show(config('code.error'), $validate->getError(), '', 403);
            }*/

            // 判断数据是否存在
            $data = [];
            // 当为还原软删除的数据时
            if (isset($param['is_delete']) && $param['is_delete'] == config('code.is_delete')) {
                $data['is_delete'] = config('code.not_delete');
            }
            if (!empty($param['ad_name'])) { // 广告名称
                $data['ad_name'] = trim($param['ad_name']);
            }
            if (!empty($param['ad_cate_id'])) { // 广告类别ID
                $data['ad_cate_id'] = intval($param['ad_cate_id']);
            }
            if (isset($param['ad_datetime']) && !empty($param['ad_datetime'])) {
                $data['start_datetime'] = strtotime($param['ad_datetime'][0]); // 投放开始时间
                $data['end_datetime'] = strtotime($param['ad_datetime'][1]); // 投放结束时间
            }
            if (isset($param['ad_time']) && !empty($param['ad_time'])) {
                $data['start_time'] = date('H:i:s', strtotime($param['ad_time'][0])); // 每日播放时间段（开始时间）
                $data['end_time'] = date('H:i:s', strtotime($param['ad_time'][1])); // 每日播放时间段（结束时间）
            }
            if (isset($param['play_times'])) { // 每日播放次数
                $data['play_times'] = input('param.play_times', null, 'intval');
            }
            if (isset($param['ad_price'])) { // 广告价格
                $data['ad_price'] = input('param.ad_price', null, 'float');
            }
            if (isset($param['discount_ratio'])) { // 广告折扣率
                $data['discount_ratio'] = input('param.discount_ratio/f');
            }
            if (!empty($param['advertiser'])) { // 广告主名称
                $data['advertiser'] = trim($param['advertiser']);
            }
            if (!empty($param['phone'])) { // 广告主电话
                $data['phone'] = trim($param['phone']);
            }
            if (!empty($param['shop_cate_ids'])) { // 投放店铺类别ID集合
                $data['shop_cate_ids'] = implode(',', $param['shop_cate_ids']);
            }
            if (!empty($param['region_ids'])) { // 投放区域ID集合（含全选与半选）
                $data['region_ids'] = json_encode([
                    'checked' => $param['region_ids'][0], // 全选
                    'half' => $param['region_ids'][1] // 半选
                ]);
            }
            if (!empty($param['device_ids'])) { // 投放广告屏ID集合
                $data['device_ids'] = implode(',', $param['device_ids']);
            }
            if (isset($param['audit_status'])) { // 审核状态
                $data['audit_status'] = input('param.audit_status', null, 'intval');
                $data['audit_id'] = $this->adminUser['id'];
                $data['audit_time'] = time();
            }
            if (isset($param['is_show'])) { // 是否显示
                $data['is_show'] = input('param.is_show', null, 'intval');
            }
            if (isset($param['sort'])) { // 排序
                $data['sort'] = input('param.sort', null, 'intval');
            }

            if (empty($data)) {
                return show(config('code.error'), '数据不合法', '', 404);
            }

            $ad = model('Ad')->field('audit_status')->find($id);
            if ($ad['audit_status'] == 1 && $data['audit_status'] != 1) {
                return show(config('code.error'), '广告已审核通过，已对相关用户进行提成，禁止修改审核状态', [], 403);
            }
            
            // 入库操作
            if ($ad['audit_status'] != 1 && $data['audit_status'] == 1 && $data['device_ids']) { // 投放广告审核通过时，涉及到的提成与收益

                /**
                 * 投放【广告】审核通过涉及到的提成与收益：
                 * ①广告屏合作商50%（按广告屏出资比例，且细分到每个广告屏对应不同的合作商）；
                 * ②广告屏合作商业务员5%，该业务员上级1%；
                 * ③店家20%，并对店家对应的店铺设备收入做分配；
                 * ④店铺业务员4%
                 * ⑤广告主业务员10%
                 */

                /* 手动控制事务 s */
                // 启动事务
                Db::startTrans();
                try {
                    $res = [];

                    // 更新广告信息
                    $res[0] = model('Ad')->save($data, ['ad_id' => $id]); // 更新
                    if ($res[0]) {
                        // 获取更新后的广告信息
                        $ad1 = model('Ad')->field('is_ad_combo, play_days, advertiser_id, ad_price')->find($id);
                        $playDays = $ad1['play_days']; // 广告投放天数
                        $advertiserId = $ad1['advertiser_id']; // 广告主ID
                        $adPrice = $ad1['ad_price']; // 广告总价格
                    }

                    /*广告主业务员提成 s*/
                    $adAdvertiserSalesmanIncome = $adPrice * config('commission.ad_advertiser_salesman_commission');
                    // 根据广告主ID获取广告主业务员及广告主业务员所属用户
                    $advertiser = Db::name('user_advertiser')->alias('a')->field('a.salesman_id, us.uid')->join('__USER_SALESMAN__ us', 'us.id = a.salesman_id')->find($advertiserId);
                    $advertiserSalesmanId = $advertiser['salesman_id'];
                    $advertiserSalesmanUserId = $advertiser['uid'];

                    // 更新店铺业务员余额、收入
                    $res[1] = Db::name('user_salesman')->where(['id' => $advertiserSalesmanId])->setInc('money', $adAdvertiserSalesmanIncome);
                    $res[2] = Db::name('user_salesman')->where(['id' => $advertiserSalesmanId])->setInc('income', $adAdvertiserSalesmanIncome);
                    // 更新店铺业务员余额、收入
                    $res[3] = Db::name('user')->where(['user_id' => $advertiserSalesmanUserId])->setInc('money', $adAdvertiserSalesmanIncome);
                    $res[4] = Db::name('user')->where(['user_id' => $advertiserSalesmanUserId])->setInc('income', $adAdvertiserSalesmanIncome);
                    /*广告主业务员提成 e*/

                    // 根据广告屏ID获取广告屏等级，并计算该广告屏每日每条广告的价格，根据价格提成
                    $deviceList = model('Device')->where(['device_id' => ['in', $data['device_ids']]])->field('device_id, level, shop_id')->select();
                    // 是否广告套餐：广告套餐
                    if($ad1['is_ad_combo'] == 1) {
                        $deviceCount = model('Device')->where(['device_id' => ['in', $data['device_ids']]])->count('device_id');
                        $adPerPrice = $adPrice / $deviceCount;
                    }
                    $deviceLevel = config('code.device_level');
                    foreach ($deviceList as $key => $value) {
                        // 不同等级广告屏投放广告的单价
                        $ad_unit_price = 1 * $deviceLevel[$value['level']];

                        // 是否广告套餐：普通投放
                        if ($ad1['is_ad_combo'] == 0) {
                            $adPerPrice = $ad_unit_price * $playDays;
                        }

                        // 判断是否存在广告屏合作商，存在才对合作商及其业务员提成
                        $partnerDevice = Db::name('partner_device')->where(['device_id' => $value['device_id']])->find();
                        if ($partnerDevice) {
                            /*广告屏合作商收益记录 s*/
                            $adPartnerIncome = $adPerPrice * config('commission.ad_partner_commission');
                            if ($partnerDevice['today_income'] != $adPartnerIncome) {
                                $res['1' . $key] = Db::name('partner_device')->where(['device_id' => $value['device_id']])->update(['today_income' => $adPartnerIncome]); // 今日收益（严格来说应该是近期收益）
                            }
                            $res['2' . $key] = Db::name('partner_device')->where(['device_id' => $value['device_id']])->setInc('total_income', $adPartnerIncome); // 累计收益
                            /*广告屏合作商收益记录 e*/

                            /*广告屏合作商收益 s*/
                            // 根据广告屏ID获取广告屏合作商及其所属用户
                            $partnerId = $partnerDevice['partner_id'];
                            $partnerUserId = $partnerDevice['user_id'];
                            // 更新广告屏合作商余额、收入
                            $res['3' . $key] = Db::name('user_partner')->where(['id' => $partnerId])->setInc('money', $adPartnerIncome);
                            $res['4' . $key] = Db::name('user_partner')->where(['id' => $partnerId])->setInc('income', $adPartnerIncome);
                            // 更新广告屏合作商所属用户余额、收入
                            $res['5' . $key] = Db::name('user')->where(['user_id' => $partnerUserId])->setInc('money', $adPartnerIncome);
                            $res['6' . $key] = Db::name('user')->where(['user_id' => $partnerUserId])->setInc('income', $adPartnerIncome);
                            /*广告屏合作商收益 e*/

                            /*广告屏合作商业务员提成 s*/
                            $adPartnerSalesmanIncome = $adPerPrice * config('commission.ad_partner_salesman_commission');
                            // 根据广告屏合作商ID获取广告屏合作商业务员及其所属用户
                            $userPartner = Db::name('user_partner')->alias('up')->field('up.salesman_id, us.uid')->join('__USER_SALESMAN__ us', 'us.id = up.salesman_id')->where(['up.id' => $partnerId])->find();
                            $partnerSalesmanId = $userPartner['salesman_id'];
                            $partnerSalesmanUserId = $userPartner['uid'];
                            // 更新广告屏合作商业务员余额、收入
                            $res['7' . $key] = Db::name('user_salesman')->where(['id' => $partnerSalesmanId])->setInc('money', $adPartnerSalesmanIncome);
                            $res['8' . $key] = Db::name('user_salesman')->where(['id' => $partnerSalesmanId])->setInc('income', $adPartnerSalesmanIncome);
                            // 更新广告屏合作商业务员所属用户余额、收入
                            $res['9' . $key] = Db::name('user')->where(['user_id' => $partnerSalesmanUserId])->setInc('money', $adPartnerSalesmanIncome);
                            $res['10' . $key] = Db::name('user')->where(['user_id' => $partnerSalesmanUserId])->setInc('income', $adPartnerSalesmanIncome);
                            /*广告屏合作商业务员提成 e*/

                            /*广告屏合作商业务员上级提成 s*/
                            $adPartnerSalesmanParentIncome = $adPerPrice * config('commission.ad_partner_salesman_parent_commission');
                            // 根据广告屏合作商业务员ID获取其上级业务员及其所属用户
                            $userPartnerSalesmanParent = Db::name('user_salesman')->alias('us')->field('us.parent_id, p.uid')->join('__USER_SALESMAN__ p', 'p.id = us.parent_id')->where(['us.id' => $partnerSalesmanId])->find();
                            if ($userPartnerSalesmanParent['parent_id'] && $userPartnerSalesmanParent['uid']) {
                                $partnerSalesmanParentId = $userPartnerSalesmanParent['parent_id'];
                                $partnerSalesmanParentUserId = $userPartnerSalesmanParent['uid'];
                                // 更新广告屏合作商业务员上级余额、收入
                                $res['11' . $key] = Db::name('user_salesman')->where(['id' => $partnerSalesmanParentId])->setInc('money', $adPartnerSalesmanParentIncome);
                                $res['12' . $key] = Db::name('user_salesman')->where(['id' => $partnerSalesmanParentId])->setInc('income', $adPartnerSalesmanParentIncome);
                                // 更新广告屏合作商业务员上级所属用户余额、收入
                                $res['13' . $key] = Db::name('user')->where(['user_id' => $partnerSalesmanParentUserId])->setInc('money', $adPartnerSalesmanParentIncome);
                                $res['14' . $key] = Db::name('user')->where(['user_id' => $partnerSalesmanParentUserId])->setInc('income', $adPartnerSalesmanParentIncome);
                            }
                            /*广告屏合作商业务员上级提成 e*/
                        }

                        /*店家收益 s*/
                        $adShopkeeperIncome = $adPerPrice * config('commission.ad_shopkeeper_commission');
                        // 根据店铺ID获取获取店家及其所属用户
                        $shop = Db::name('shop')->field('user_id, shopkeeper_id')->find($value['shop_id']);
                        $shopkeeperId = $shop['shopkeeper_id'];
                        $shopkeeperUserId = $shop['user_id'];
                        // 更新店家余额、收入
                        $res['15' . $key] = Db::name('user_shopkeeper')->where(['id' => $shopkeeperId])->setInc('money', $adShopkeeperIncome);
                        $res['16' . $key] = Db::name('user_shopkeeper')->where(['id' => $shopkeeperId])->setInc('income', $adShopkeeperIncome);
                        // 更新店家所属用户余额、收入
                        $res['17' . $key] = Db::name('user')->where(['user_id' => $shopkeeperUserId])->setInc('money', $adShopkeeperIncome);
                        $res['18' . $key] = Db::name('user')->where(['user_id' => $shopkeeperUserId])->setInc('income', $adShopkeeperIncome);
                        // 更新店家对应店铺设备的近期收入（此处用今日收入不合理，因为累计收入不是严格每天增加）、累计收入
                        $res['19' . $key] = Db::name('device')->where(['device_id' => $value['device_id']])->update(['today_income' => $adShopkeeperIncome]);
                        $res['19' . $key] = $res['19' . $key] === false ? 0 : true;
                        $res['20' . $key] = Db::name('device')->where(['device_id' => $value['device_id']])->setInc('total_income', $adShopkeeperIncome);
                        /*店家收益 e*/

                        /*店铺业务员提成 s*/
                        $adShopSalesmanIncome = $adPerPrice * config('commission.ad_shop_salesman_commission');
                        // 根据店家ID获取店铺业务员及店铺业务员所属用户
                        $userShopkeeper = Db::name('user_shopkeeper')->alias('ush')->field('ush.salesman_id, usa.uid')->join('__USER_SALESMAN__ usa', 'usa.id = ush.salesman_id')->find($shopkeeperId);
                        $shopSalesmanId = $userShopkeeper['salesman_id'];
                        $shopSalesmanUserId = $userShopkeeper['uid'];
                        // 更新店铺业务员余额、收入
                        $res['21' . $key] = Db::name('user_salesman')->where(['id' => $shopSalesmanId])->setInc('money', $adShopSalesmanIncome);
                        $res['22' . $key] = Db::name('user_salesman')->where(['id' => $shopSalesmanId])->setInc('income', $adShopSalesmanIncome);
                        // 更新店铺业务员余额、收入
                        $res['23' . $key] = Db::name('user')->where(['user_id' => $shopSalesmanUserId])->setInc('money', $adShopSalesmanIncome);
                        $res['24' . $key] = Db::name('user')->where(['user_id' => $shopSalesmanUserId])->setInc('income', $adShopSalesmanIncome);
                        /*店铺业务员提成 e*/
                    }

                    // 任意一个表写入失败都会抛出异常，TODO：是否可以不做该判断
                    if (in_array(0, $res)) {
                        return show(config('code.error'), '审核通过失败', $res, 403);
                    }

                    // 提交事务
                    Db::commit();
                    return show(config('code.success'), '审核通过', '', 201);
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                    return show(config('code.error'), '请求异常'.$e->getMessage(), '', 500);
                }
                /* 手动控制事务 e */
            } else {
                try {
                    $result = model('Ad')->save($data, ['ad_id' => $id]); // 更新
                } catch (\Exception $e) {
                    return show(config('code.error'), '网络忙，请重试', '', 500); // $e->getMessage()
                }
                if (false === $result) {
                    return show(config('code.error'), '更新失败', '', 403);
                } else {
                    return show(config('code.success'), '更新成功', $data, 201);
                }
            }
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }

    /**
     * 删除指定广告资源
     *
     * @param  int $id
     * @return \think\Response
     * @throws ApiException
     */
    public function delete($id)
    {
        // 判断为DELETE请求
        if (request()->isDelete()) {
            // 获取指定的广告
            try {
                $data = model('Ad')->find($id);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', '', 500); // $e->getMessage()
            }

            // 判断数据是否存在
            if ($data['ad_id'] != $id) {
                return show(config('code.error'), '数据不存在', '', 404);
            }

            // 判断删除条件：判断广告审核状态
            if ($data['audit_status'] == config('code.status_enable')) { // 审核通过
                return show(config('code.error'), '删除失败：广告已审核通过', '', 403);
            }

            // 软删除
            if ($data['is_delete'] != config('code.is_delete')) {
                // 捕获异常
                try {
                    $result = model('Ad')->softDelete('ad_id', $id);
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
            try { // 捕获异常
                $result = model('Ad')->destroy($id);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', [], 500); // $e->getMessage()
            }
            if (!$result) {
                return show(config('code.error'), '删除失败', [], 403);
            } else {
                return show(config('code.success'), '删除成功');
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }
}
