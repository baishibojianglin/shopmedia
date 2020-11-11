<?php

namespace app\api\controller;

use think\Controller;
use think\Db;
use think\Model;
use think\Request;

/**
 * api模块客户端用户反馈控制器类
 * Class Feedback
 * @package app\api\controller
 */
class Feedback extends AuthBase
{
    /**
     * 保存新建的用户反馈资源
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

            // 判断数据是否存在
            if (empty($data['user_id'])) {
                return show(config('code.error'), '用户不存在', '', 401);
            }

            // 入库操作
            try {
                // 新增反馈
                $id = model('Feedback')->add($data, 'id');
            } catch (\Exception $e) {
                return show(config('code.error'), $e->getCode().'网络忙，请重试', '', 500); // $e->getMessage()
            }
            if ($id) {
                return show(config('code.success'), '反馈成功', '', 201);
            } else {
                return show(config('code.error'), '反馈失败', '', 403);
            }
        } else {
            return show(config('code.error'), '请求不合法', '', 400);
        }
    }
}
