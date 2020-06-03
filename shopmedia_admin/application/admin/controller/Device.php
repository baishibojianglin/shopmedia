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
			if ($this->adminUser['company_id'] != config('admin.platform_company_id')) { // 平台可以查看所有数据，分公司只能查看自有数据
				$map['c.company_id'] = $this->adminUser['company_id'];
			}
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
				$status = config('code.device_status'); // 状态
				$brand = config('code.device_brand'); // 品牌
				$model = config('code.device_model'); // 型号
				$size = config('code.device_size'); // 型号
				foreach ($data as $key => $value) {
					$data[$key]['status_msg'] = $status[$value['status']]; // 定义状态信息
					$data[$key]['brand_msg']=$brand[$value['brand']]; //定义品牌信息
					$data[$key]['model_msg']=$model[$value['brand']][$value['model']]; //定义型号信息
					$data[$key]['size_msg']=$size[$value['size']]; //定义尺寸信息
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
	 * 保存新建的广告屏资源
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
			//$form['data']['create_time'] = time();

			// 入库操作
			try {
				//$id = Db::name('Device')->insert($data['data']);
				$id = model('Device')->add($data['data'], 'device_id');
			} catch (\Exception $e) {
				return show(config('code.error'), '请求异常'.$e->getMessage(), '', 500); // $e->getMessage()
			}
			if ($id) {
				return show(config('code.success'), '新建成功', '', 201);
			} else {
				return show(config('code.error'), '新建失败', '', 403);
			}
		} else {
			return show(config('code.error'), '请求不合法', '', 400);
		}
	}

	/**
	 * 获取广告屏基本信息
	 * @param $id
	 * @return \think\response\Json
	 */
	public function read($id){
		$device = Db::name('device')->find($id);

		if (!empty($device)) {
			$message['data'] = $device;
			$message['status'] = 1;
			$message['words'] = '获取成功';
		}else{
			$message['status'] = 0;
			$message['words'] = '获取失败';
		}
		return json($message);
	}

	/**
	 * 保存更新的广告屏资源
	 *
	 * @param  \think\Request  $request
	 * @param  int  $id
	 * @return \think\Response
	 */
	public function update(Request $request, $id)
	{
		// 判断为PUT请求
		if (!request()->isPut()) {
			return show(config('code.error'), '请求不合法', '', 400);
		}

		// 传入的数据
		$param = input('param.');

		// 判断数据是否存在
		$data = [];
		if (isset($param['data'])) {
			$data = $param['data'];
		}

		if (empty($data)) {
			return show(config('code.error'), '数据不合法', '', 404);
		}

		// 更新
		try {
			$result = Db::name('device')->where(['device_id' => $id])->update($data);
			//$result = model('Device')->save($data, ['device_id' => $id]); // 更新
		} catch (\Exception $e) {
			return show(config('code.error'), '请求异常' . $e->getMessage(), '', 500); // $e->getMessage()
		}
		if (false === $result) {
			return show(config('code.error'), '更新失败', '', 403);
		} else {
			return show(config('code.success'), '更新成功', '', 201);
		}
	}

	/**
	 * 新增或更新广告屏（弃用，已由新建save()、更新update()代替）
	 * @return \think\Response
	 * @throws ApiException
	 */
	public function addDevice(){
		$form=input();
        $form['data']['create_time']=time();

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
        $map['status'] = 1;
        $shopList = Db::name('shop')->where($map)->field('shop_id,shop_name,device_quantity,plan_quantity')->select();

	    if (!empty($shopList)) {
            foreach ($shopList as $key => $value){
				if ($value['device_quantity'] >= $value['plan_quantity']) {
					array_splice($shopList, $key, 1);
				}
            }
            return show(config('code.success'), 'OK', $shopList);
		} else {
            return show(config('code.error'), '没有店铺有空闲安装', [], 404);
		}
    }
}
