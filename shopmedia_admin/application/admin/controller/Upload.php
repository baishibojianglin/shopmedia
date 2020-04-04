<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Request;
use think\Db;

/**
 * admin模块媒体设备管理控制器类
 * Class Device
 * @package app\admin\controller
 */
class Upload extends Base
{



  /**
  *上传图片
  */
    public function uploadimg()
    {
        $data=input();
        $file = request()->file($data['name']);
        $info=$file->getInfo();
        $value=uploadimage($info);
        return json($value);
    }



  /**
  *删除图片
  */
    public function deleteimg()
    {
        $data=input();
        deleteimage($data);

    }






}
