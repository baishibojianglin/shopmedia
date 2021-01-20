<?php

namespace app\api\controller;

use think\Controller;
use think\Request;


/**
 * api模块客户端发送短信控制器类
 * Class Device
 * @package app\admin\controller
 */
class Upload extends Common
{
    /**
     * 调用阿里云oss对象存储
     * @return \think\response\Json
     */
    public function upload(){
          $info['name'] = $_FILES['file']['name'];
          $info['tmp_name'] = $_FILES['file']['tmp_name'];
          $data=uploadimage($info);
          return json($data);
    }


    /**
     * 删除阿里云oss对象存储上的图片
     * @return \think\response\Json
     */
    public function deleimg(){
          $data = input();
          $data=deleteimage($data);
    }


}
