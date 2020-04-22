<?php

namespace app\admin\controller;

use think\Controller;

/**
 * admin模块文件（图像）操作控制器类
 * Class Device
 * @package app\admin\controller
 */
class Upload extends Base
{
    /**
     * 上传图片
     * @return \think\response\Json
     */
    public function uploadimg()
    {
        $data=input();
        $file = request()->file($data['name']);
        $info = $file->getInfo();
        $value = uploadimage($info);
        return json($value);
    }

    /**
     * 删除图片
     */
    public function deleteimg()
    {
        $data = input();
        deleteimage($data);
    }
}
