<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Request;
use think\Db;

/**
 * admin模块分公司管理控制器类
 * Class Company
 * @package app\admin\controller
 */
class Company extends Base
{
	/**
	 * 显示分公司资源列表
	 * @return \think\Response
	 * @throws ApiException
	 */
	public function index()
	{
		// 判断为GET请求
		if (request()->isGet()) {
			// 传入的参数
			$param = input('param.');

			// 查询条件
			$map = [];
			if ($this->adminUser['company_id'] != config('admin.platform_company_id')) { // 平台可以查看所有数据，分公司只能查看自有数据
				$map['c.company_id'] = $this->adminUser['company_id'];
			}
			if (!empty($param['company_name'])) { // 分公司名称
				$map['c.company_name'] = ['like', '%' . trim($param['company_name']) . '%'];
			}
			if (isset($param['is_delete'])) { // 是否删除
				$map['c.is_delete'] = $param['is_delete'];
			}

			// 获取分页page、size
			$this->getPageAndSize($param);

			// 获取分公司列表数据
			try {
				$data = model('Company')->getCompany($map, (int)$this->size);
			} catch (\Exception $e) {
				return show(config('code.error'), '网络忙，请重试'.$e->getMessage(), '', 500); // $e->getMessage()
				//throw new ApiException('网络忙，请重试', 500, config('code.error')); // $e->getMessage()
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
	 * 获取分公司列表树
	 * @return \think\response\Json
	 */
	public function companyTree()
	{
		// 获取商品类别列表树，用于页面下拉框列表
		try {
			$data = model('Company')->field('company_id, company_name')->select(); // TODO：待处理，暂时这样写
		} catch (\Exception $e) {
			return show(config('code.error'), '网络忙，请重试', [], 500); // $e->getMessage()
		}

		/*if ($data) {
			// 处理数据
			foreach ($data as $key => $value) {
				if ($value['level'] != 0) {
					// level 用于定义 title 前面的空位符的长度
					$data[$key]['name'] = '└' . str_repeat('─', $value['level'] * 1). ' ' . $value['name']; // str_repeat(string,repeat) 函数把字符串重复指定的次数
				}
			}
		}*/

		return show(config('code.success'), 'OK', $data);
	}

	/**
	 * 删除指定分公司资源
	 * @param int $id
	 * @return \think\response\Json
	 * @throws ApiException
	 */
	public function delete($id)
	{
		// 判断为DELETE请求
		if (request()->isDelete()) {
			// 显示指定的分公司资源
			try {
				$data = model('Company')->find($id);
				//return show(config('code.success'), 'ok', $data);
			} catch (\Exception $e) {
				return show(config('code.error'), '网络忙，请重试', '', 500);
				//throw new ApiException($e->getMessage(), 500, config('code.error'));
			}

			// 判断数据是否存在
			if ($data['company_id'] != $id) {
				return show(config('code.error'), '数据不存在', '', 404);
			}

			// 判断删除条件
			// 判断分公司状态
			if ($data['status'] == config('code.status_enable')) { // 启用
				return show(config('code.error'), '删除失败：分公司已启用', '', 403);
			}

			// 软删除
			if ($data['is_delete'] != config('code.is_delete')) {
				// 捕获异常
				try {
					$result = model('Company')->softDelete('company_id', $id);
				} catch (\Exception $e) {
					throw new ApiException($e->getMessage(), 500, config('code.error'));
				}

				if (!$result) {
					return show(config('code.error'), '移除失败', '', 403);
				} else {
					return show(config('code.success'), '移除成功', '');
				}
			}

			// 真删除
            try {
                $result = model('Company')->destroy($id);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', '', 500);
            }
            if (!$result) {
                return show(config('code.error'), '删除失败', '', 403);
            } else {
                return show(config('code.success'), '删除成功');
            }
		} else {
			return show(config('code.error'), '请求不合法', '', 400);
		}
	}

	/**
	 * 保存更新的分公司资源
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
		// 当为还原软删除的数据时
		if (isset($param['is_delete']) && $param['is_delete'] == config('code.is_delete')) {
			$data['is_delete'] = config('code.not_delete');
		}

		if (empty($data)) {
			return show(config('code.error'), '数据不合法', '', 404);
		}

		// 更新
		try {
			$result = model('Company')->save($data, ['company_id' => $id]); // 更新
		} catch (\Exception $e) {
			return show(config('code.error'), '网络忙，请重试', '', 500); // $e->getMessage()
		}
		if (false === $result) {
			return show(config('code.error'), '更新失败', '', 403);
		} else {
			return show(config('code.success'), '更新成功', '', 201);
		}
	}

	/**
	 * 创建（更新）分公司
	 * @return \think\response\Json
	 * @throws \think\Exception
	 */
	public function createCompany(){
		$form=input();
		//添加分公司基本信息
		//try{
        $form['data']['create_time']=time();
        if(!empty($form['company_id'])){ //更新
        	$mapcompany['company_id']=$form['company_id'];
        	$number=Db::name('company')->where($mapcompany)->update($form['data']);
        }else{ //新增
        	$number=Db::name('company')->insert($form['data']);
        }
        //}
       // catch(\Exception $e){
        	//return json($e->getMessage());
        //}
        
		
		if($number>0){
			$message['status']=1;
			$message['words']='创建成功';
		}else{
			$message['status']=0;
			$message['words']='创建失败';
		}
		return json($message);
	}

	/**
	 * 获取分公司基本信息
	 * @return \think\response\Json
	 */
	public function getCompany(){
		$form=input();
		$mapcompany['company_id']=$form['company_id'];
        $companylist=Db::name('company')->where($mapcompany)->find();
		
		if(!empty($companylist)){
			$message['data']=$companylist;
			$message['status']=1;
			$message['words']='获取成功';
		}else{
			$message['status']=0;
			$message['words']='获取失败';
		}
		return json($message);
	}
}
