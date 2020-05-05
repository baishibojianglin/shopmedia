<?php

namespace app\api\controller;

use think\Controller;
use think\Request;

/**
 * api模块客户端店家店铺控制器类
 * Class Shop
 * @package app\api\controller
 */
class Shop extends AuthBase
{
    /**
     * 保存新建的店铺资源
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
            return show(config('code.success'), '新增成功', $data, 401);
            // 判断数据是否存在
            if (empty($data['user_id'])) {
                return show(config('code.error'), '用户不存在', '', 401);
            }

            // 入库操作
            try {
                // 新增反馈
                $id = model('Shop')->add($data, 'shop_id');
            } catch (\Exception $e) {
                return show(config('code.error'), $e->getCode().'网络忙，请重试', '', 500); // $e->getMessage()
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
