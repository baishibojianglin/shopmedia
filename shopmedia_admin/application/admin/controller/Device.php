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
class Device extends Base
{
	/**
	 * 新增广告设备
	 * @return \think\Response
	 * @throws ApiException
	 */

	public function addDevice(){
		$form=input();
		$form['data']['status']=1; //正常
        $form['data']['createtime']=date('Y-m-d H:i:s');
        $form['data']['saled_part']=0;
        $number=Db::name('device')->insert($form['data']);
		
		if($number>0){
			$message['status']=1;
			$message['words']='添加成功';
		}else{
			$message['status']=0;
			$message['words']='添加失败';
		}
		return json($message);
	}





	
}
