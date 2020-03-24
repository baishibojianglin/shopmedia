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
   *创建（更新）分公司
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
   *获取分公司基本信息
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
   *获取销售区域
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
   *获取供应商销售区域
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
   *插入供应商销售区域字段值
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
   *获取商品种类
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
   *插入供应商销售商品种类字段值
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
