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
			if (isset($param['size'])) { // 每页条数
				$param['size'] = intval($param['size']);
			}

			// 查询条件
			$map = [];
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
				$data = model('Company')->getCompany($map, $this->size);
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
	 * 创建（更新）分公司
	 * @return \think\response\Json
	 * @throws \think\Exception
	 */
	public function createCompany(){
		$form=input();
		//添加分公司基本信息
        $form['data']['createtime']=date('Y-m-d H:i:s');
        if($form['company_id']!=''){ //更新
        	$mapcompany['company_id']=$form['company_id'];
        	$number=Db::name('company')->where($mapcompany)->update($form['data']);
        }else{ //新增
        	$number=Db::name('company')->insert($form['data']);
        }
        
		
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
		//添加分公司基本信息
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

	/**
	 * 获取销售区域
	 * @return \think\response\Json
	 */
	public function getzone(){
		$form=input();	
		$maparea['parent_id']=$form['parent_id'];
	    $listarea=model('Region')->where($maparea)->cache(true, 10)->select();

		if(!empty($listarea)){
			$message['data']=$listarea;
			$message['status']=1;
		}else{
			$message['data']=[];
			$message['status']=0;
		}
		return json($message);
	}

	/**
	 * 获取供应商销售区域
	 * @return \think\response\Json
	 */
	public function getarea_company(){
		$form=input();
		$mapcompany['id']=$form['id'];	
		$map['parent_id']=$form['parent_id'];
		$map['level']=$form['level'];
        //获取company表销售区域字段值，并分解成数组
		$listcompany = model('Company')->salearea($mapcompany);
        $listcompanyvalue=explode("|",$listcompany);
        //查询生成前台tree组件需要的数据格式
        $data=model('Region')->getzone($map,$listcompanyvalue);
        //区域全选情况的查询
        if(empty($data)){
        	 $data=model('Region')->getRegion($map);
        }

        if(!empty($data)){
        	$message['data']= $data;
        	$message['status']=1;
        }else{
        	$message['status']=0;       	
        }

		return json($message);
	}

	/**
	 * 插入供应商销售区域字段值
	 * @return \think\response\Json
	 */
	public function submitArea(){
		$form=input();
		$listcompany=model('Company')->insertcompany($form['data']);
		
		if(!empty($listcompany)){
			$message['companyid']=$listcompany;
			$message['status']=1;
			$message['words']='销售区域配置成功';
		}else{
			$message['status']=0;
			$message['words']='销售区域配置失败';
		}
		return json($message);
	}

	/**
	 * 获取商品种类
	 * @return \think\response\Json
	 */
	public function getshopcate_company(){
		$form=input();	
		$mapcate['parent_id']=$form['parent_id'];
		$mapcate['audit_status']=1;
        //获取商品种类
        $listcate=model('GoodsCate')->where($mapcate)->field('cate_id,cate_name')->cache(true, 10)->select();
        //原来勾选的商品种类
    	$mapcompany['id']=$form['id'];
        $listselectcate=model('Company')->where($mapcompany)->field('salecate')->find();
          
        if(!empty($listcate)){
        	$message['data']=$listcate;
        	$message['selectdata']=$listselectcate;
        	$message['status']=1;
        }else{
        	$message['data']=[];
        	$message['status']=0;      	
        }
	    return json($message);
	}

	/**
	 * 插入供应商销售商品种类字段值
	 * @return \think\response\Json
	 */
	public function cate_insert(){
		$form=input();
		$form['data']['status']=1;
		$listcompany=model('Company')->update($form['data']);
		
		if(!empty($listcompany)){
			$message['companyid']=$listcompany;
			$message['status']=1;
			$message['words']='商品种类配置成功';
		}else{
			$message['status']=0;
			$message['words']='商品种类配置失败';
		}
		return json($message);
	}

	/**
	 * 获取供应商列表树
	 * @return \think\response\Json
	 */
	public function companyTree()
	{
		// 获取商品类别列表树，用于页面下拉框列表
		try {
			$data = model('Company')->field('id, name')->select(); // TODO：待处理，暂时这样写
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
}
