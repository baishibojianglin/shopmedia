<?php

namespace app\api\controller;

use think\Controller;
use think\Request;
use think\Db;

/**
 * api模块客户端广告控制器类
 * Class Device
 * @package app\api\controller
 */
class Ad extends AuthBase
{
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
            $data = input('post.');
            // 处理数据
            if (isset($data['startdate']) && !empty($data['startdate'])) {
                $data['start_datetime'] = strtotime($data['startdate']); // 投放开始时间
                //$data['end_datetime'] = strtotime($data['']); // 投放结束时间
            }
            /*if (!empty($data['shop_cate_ids'])) { // 投放店铺类别ID集合
                $data['shop_cate_ids'] = implode(',', $data['shop_cate_ids']);
            }*/
            if (!empty($data['region_ids'])) { // 投放区域ID集合（含全选与半选）
                $data['region_ids'] = json_encode([
                    'checked' => $data['region_ids'][0], // 全选
                    'half' => $data['region_ids'][1] // 半选
                ]);
            }
            if (!empty($data['device_ids'])) { // 投放广告屏ID集合
                $data['device_ids'] = implode(',', $data['device_ids']);
            }

            // 广告主信息
            $advertiser = Db::name('user_advertiser')->field('id')->where(['user_id' => $this->user['user_id']])->find();
            $data['advertiser_id'] = $advertiser['id'];
            $data['advertiser'] = $this->user['user_name'];
            $data['phone'] = $this->user['phone'];

            // validate验证数据合法性
            /*$validate = validate('');
            if (!$validate->check($data)) {
                return show(config('code.error'), $validate->getError(), '', 403);
            }*/

            // 入库操作
            try {
                $id = model('Ad')->add($data, 'ad_id');
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', '', 500); // $e->getMessage()
            }
            if ($id) {
                return show(config('code.success'), '新增成功', '', 201);
            } else {
                return show(config('code.error'), '新增失败', '', 403);
            }
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }
}
