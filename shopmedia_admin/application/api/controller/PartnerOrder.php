<?php

namespace app\api\controller;

use think\Controller;
use think\Model;
use think\Request;

/**
 * api模块客户端广告屏合作商订单控制器类
 * Class PartnerOrder
 * @package app\api\controller
 */
class PartnerOrder extends AuthBase
{
    /**
     * 保存新建的订单资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        // 判断为POST请求
        if(request()->isPost()){
            $data = input('post.');
            $data['company_user_id'] = $this->adminUser['user_id']; // 创建者(平台管理员)ID

            // validate验证数据合法性
            $validate = validate('GoodsCate');
            if (!$validate->check($data)) {
                return show(config('code.error'), $validate->getError(), [], 403);
            }

            // 入库操作
            try {
                $id = model('GoodsCate')->add($data, 'cate_id');
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', [], 500); // $e->getMessage()
            }
            if ($id) {
                return show(config('code.success'), '新增成功', ['cate_id' => $id], 201);
            } else {
                return show(config('code.error'), '新增失败', [], 403);
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }
}
