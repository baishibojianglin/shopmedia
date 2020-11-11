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
			if (isset($param['status']) && $param['status'] != null) { // 状态
				$map['d.status'] = intval($param['status']);
			}
			if (!empty($param['device_sn'])) { // 设备编号
				$map['d.device_sn'] = ['like', '%' . trim($param['device_sn']) . '%'];
			}
			if (!empty($param['shop_name'])) { // 店铺名称
				$shopName = ['like', '%' . trim($param['shop_name']) . '%'];
				$shopIds = model('Shop')->where(['shop_name' => $shopName])->column('shop_id');
				$map['d.shop_id'] = ['in', $shopIds];
			}

			// 获取分页page、size
			$this->getPageAndSize($param);

			// 获取广告屏列表数据
			try {
				$data = model('Device')->getDevice($map, $this->size);
			} catch (\Exception $e) {
				return show(config('code.error'), '网络忙，请重试'.$e->getMessage(), [], 500); // $e->getMessage()
				//throw new ApiException('网络忙，请重试', 500, config('code.error')); // $e->getMessage()
			}

			if ($data) {
				// 处理数据
				$status = config('code.device_status'); // 状态
				$brand = config('code.device_brand'); // 品牌
				$model = config('code.device_model'); // 型号
				$size = config('code.device_size'); // 尺寸
				foreach ($data as $key => $value) {
					$data[$key]['status_msg'] = $status[$value['status']]; // 定义状态信息
					$data[$key]['brand_msg'] = isset($brand[$value['brand']]) ? $brand[$value['brand']] : ''; //定义品牌信息
					$data[$key]['model_msg'] = $model[$value['brand']][$value['model']]; //定义型号信息
					$data[$key]['size_msg'] = $size[$value['size']]; //定义尺寸信息
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
			$map['s.province_id|s.city_id|s.county_id|s.town_id'] = ['in', $param['region_ids']];
		}
		/*if (!empty($param['shop_cate_ids'])) { // 投放店铺类别ID集合
			$map['s.cate'] = ['in', $param['shop_cate_ids']];
		}*/
		if (!empty($param['ad_cate_id'])) { // 广告所属行业类别
			$map['s.cate'] = ['not in', $param['ad_cate_id']];
		}

		// 获取广告屏列表数据
		try {
			$data = model('Device')->getDeviceList($map);
		} catch (\Exception $e) {
			return show(config('code.error'), $e->getMessage());
		}
		if ($data) {
			// 处理数据
			$adCate = config('ad.ad_cate'); // 广告所属行业类别
			foreach ($data as $key => $value) {
				$data[$key]['ad_cate_name'] = isset($value['shopcate']) ? $adCate[$value['shopcate']] : '（其他）';
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
			/* 手动控制事务 s */
			// 启动事务
			Db::startTrans();
			try {
				// 添加广告屏
				//$id = Db::name('Device')->insert($data['data']);
				//$res[1] = $id = model('Device')->add($data['data'], 'device_id');
				$res[0] = $id = Db::name('device')->strict(false)->insertGetId($data['data']);
				if ($res[0]) {
					$device = Db::name('device')->field('shop_id')->find($id);
				}
				// 更新店铺安装广告屏数量
				$res[1] = Db::name('shop')->where(['shop_id' => $device['shop_id']])->update(['device_quantity' => 1]);

				// 任意一个表写入失败都会抛出异常，TODO：是否可以不做该判断
				if (in_array(0, $res)) {
					return show(config('code.error'), '新建失败', $res, 403);
				}

				// 提交事务
				Db::commit();
				return show(config('code.success'), '新建成功', '', 201);
			} catch (\Exception $e) {
				// 回滚事务
				Db::rollback();
				return show(config('code.error'), '请求异常'.$e->getMessage(), '', 500); // $e->getMessage()
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
        $shopList = Db::name('shop')->where($map)->field('shop_id,shop_name,cate,device_quantity,plan_quantity')->select();
	    if (!empty($shopList)) {
			$adCate = config('ad.ad_cate'); // 广告类别
			try{
	            foreach ($shopList as $key => $value){
					$shopList[$key]['cate_name'] = $adCate[$value['cate']];
					/*// 当广告屏安装数量大于等于该店铺计划安装数量时，移除该店铺
					if ($value['device_quantity'] >= $value['plan_quantity']) {
						array_splice($shopList, $key, 1);
					}*/
	            }
	        }catch(\Exception $e){
                return json($e->getMessage());
	        }
            return show(config('code.success'), 'OK', $shopList);
		} else {
            return show(config('code.error'), '没有店铺有空闲安装', [], 404);
		}
    }
}
