<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Request;
use think\Db;

/**
 * admin模块广告屏管理控制器类
 * Class Device
 * @package app\admin\controller
 */
class Device extends Base
{
	/**
	 * 显示广告屏资源列表
	 * @return \think\Response
	 * @throws ApiException
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
			if (!empty($param['company_name'])) { // 分公司名称
				$map['c.company_name'] = ['like', '%' . trim($param['company_name']) . '%'];
			}

			// 获取分页page、size
			$this->getPageAndSize($param);

			// 获取广告屏列表数据
			try {
				$data = model('Device')->getDevice($map, $this->size);
			} catch (\Exception $e) {
				throw new ApiException('网络忙，请重试', 500, config('code.error')); // $e->getMessage()
				//return show(config('code.error'), '网络忙，请重试', [], 500); // $e->getMessage()
			}

			if ($data) {
				// 处理数据
				$status = config('code.status'); // 状态
				foreach ($data as $key => $value) {
					$data[$key]['status_msg'] = $status[$value['status']]; // 定义状态信息
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
	 * 广告屏列表（不分页，用于 Select 选择器等）
	 * @return \think\response\Json
	 */
	public function deviceList()
	{
		// 传入的参数
		$param = input('param.');

		// 查询条件
		$map = [];
		$map['d.status'] = config('code.status_enable');
		$map['d.is_delete'] = config('code.not_delete');
		if (!empty($param['region_ids'])) { // 投放区域ID集合（只含全选）
			$map['d.province_id|d.city_id|d.area_id|d.street_id'] = ['in', $param['region_ids']];
		}
		if (!empty($param['shop_cate_ids'])) { // 投放店铺类别ID集合
			$map['d.shopcate'] = ['in', $param['shop_cate_ids']];
		}

		// 获取广告屏列表数据
		$data = model('Device')->getDeviceList($map);
		if ($data) {
			// 处理数据
			$shopCate = config('code.shop_cate'); // 店铺类别
			foreach ($data as $key => $value) {
				$data[$key]['shop_cate_name'] = $shopCate[$value['shopcate']] ? : '（其他）';
			}
		}

		return show(config('code.success'), 'OK', $data);
	}

	/**
	 * 新增（更新）广告屏
	 * @return \think\Response
	 * @throws ApiException
	 */
	public function addDevice(){
		$form=input();
        $form['data']['createtime']=date('Y-m-d H:i:s');

        if($form['device_id']!=''){ //更新
        	$mapdevice['device_id']=$form['device_id'];
        	$number=Db::name('device')->where($mapdevice)->update($form['data']);
        }else{ //新增
        	$number=Db::name('device')->insert($form['data']);
        }
		
		if($number>0){
			$message['status']=1;
			$message['words']='添加成功';
		}else{
			$message['status']=0;
			$message['words']='添加失败';
		}
		return json($message);
	}

	/**
	 * 获取广告屏基本信息
	 * @return \think\response\Json
	 */
	public function getDevice(){
		$form=input();
		$mapdevice['device_id']=$form['device_id'];
        $devicelist=Db::name('device')->where($mapdevice)->find();
		
		if(!empty($devicelist)){
			$message['data']=$devicelist;
			$message['status']=1;
			$message['words']='获取成功';
		}else{
			$message['status']=0;
			$message['words']='获取失败';
		}
		return json($message);
	}

	/**
	 * 获取广告屏位置信息
	 * @return \think\response\Json
	 */
	public function getMarkers(){
		$form=input();
        $devicelist=Db::name('device')->select();
		
		if(!empty($devicelist)){
			$message['data']=$devicelist;
			$message['status']=1;
			$message['words']='获取成功';
		}else{
			$message['status']=0;
			$message['words']='获取失败';
		}
		return json($message);
	}

	/**
	 * 获取广告屏品牌
	 * @return \think\response\Json
	 */

   public function getDeviceBrand()
    {
        $brand= config('code.device_brand'); 
        $data = []; // 定义二维数组列表
        // 处理数据，将一维数组转成二维数组
        foreach ($brand as $key => $value) {
            $data[] = ['brand_id' => $key, 'brand_name' => $value];
        }
        return show(config('code.success'), 'OK', $data);
    }


	/**
	 * 获取广告屏型号
	 * @return \think\response\Json
	 */

   public function getDeviceModel()
    {
        $form=input();
        $model= config('code.device_model'); 
        $data = []; // 定义二维数组列表
        //处理数据，将一维数组转成二维数组
        foreach ($model[$form['brand_id']] as $key => $value) {
            $data[] = ['model_id' => $key, 'model_name' => $value];
        }
        return show(config('code.success'), 'OK', $data);


    }

	/**
	 * 获取广告屏尺寸
	 * @return \think\response\Json
	 */
   public function getDeviceSize()
    {
        $size= config('code.device_size'); 
        $data = []; // 定义二维数组列表
        // 处理数据，将一维数组转成二维数组
        foreach ($size as $key => $value) {
            $data[] = ['size_id' => $key, 'size_name' => $value];
        }
        return show(config('code.success'), 'OK', $data);
    }

	/**
	 * 获取广告屏状态
	 * @return \think\response\Json
	 */
   public function getDeviceStatus()
    {
        $status= config('code.device_status'); 
        $data = []; // 定义二维数组列表
        // 处理数据，将一维数组转成二维数组
        foreach ($status as $key => $value) {
            $data[] = ['status_id' => $key, 'status_name' => $value];
        }
        return show(config('code.success'), 'OK', $data);
    }


	/**
	 * 获取广告屏等级
	 * @return \think\response\Json
	 */
   public function getDeviceLevel()
    {
        $level= config('code.device_level'); 
        $data = []; // 定义二维数组列表
        // 处理数据，将一维数组转成二维数组
        foreach ($level as $key => $value) {
            $data[] = ['level_id' => $key, 'level_name' => $value];
        }
        return show(config('code.success'), 'OK', $data);
    }


	/**
	 * 获取店铺列表
	 * @return \think\response\Json
	 */
   public function getDeviceShop()
    {
        $match['status']=1;
        $shoplist=Db::name('shop')->where($match)->field('shop_id,shop_name,device_quantity,plan_quantity')->select();
		
	    if(!empty($shoplist)){
            
            foreach ($shoplist as $key => $value){
            	  if($value['device_quantity']>=$value['plan_quantity']){
            	  	      array_splice($shoplist,$key,1);
            	  }
            }
            return show(config('code.success'), 'OK', $shoplist);

		}else{   
			$data['words']="没有店铺有空闲安装";
            return show(config('code.error'), 'OK', $data);
		}

    }



}
