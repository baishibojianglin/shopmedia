<?php
use think\Route;

/*-----后台管理中心路由-----*/
//登录
Route::POST('login','admin/Login/login');
//登录验证码
Route::GET('code','admin/Login/createverifycode');
//上传图片
Route::POST('upload','admin/Common/uploadimg');
//删除图片
Route::POST('deleteimages','admin/Common/deleteimg');
//获取平台销售区域数据
Route::POST('getarea','admin/Company/getArea');
//获取供应商销售区域数据
Route::POST('companyarea','admin/Company/getarea_company');
//插入供应商销售区域数据
Route::POST('submitArea','admin/Company/submitArea');
//获取供应商销售商品种类
Route::POST('getshopcate','admin/Company/getshopcate_company');
//插入供应商销售商品种类
Route::POST('shopcateinsert','admin/Company/cate_insert');
// 区域
Route::resource('region', 'admin/region');
// 商品类别
Route::resource('goods_cate', 'admin/goods_cate');
Route::get('goods_cate_tree', 'admin/goods_cate/goodsCateTree'); // 商品类别列表树
// 商品品牌
Route::resource('goods_brand', 'admin/goods_brand');
// Auth用户组
Route::resource('auth_group', 'admin/auth_group');
Route::get('auth_group_tree', 'admin/auth_group/authGroupTree'); // Auth用户组列表树
Route::put('config_auth_group_rule/:id', 'admin/auth_group/configAuthGroupRule'); // 配置Auth用户组权限规则
// Auth权限规则
Route::resource('auth_rule', 'admin/auth_rule');
Route::get('auth_rule_tree', 'admin/auth_rule/authRuleTree'); // Auth权限规则列表树
Route::get('lazy_load_auth_rule_tree', 'admin/auth_rule/lazyLoadAuthGroupTree'); // 懒加载Auth权限规则树形列表
// 供应商账户
Route::resource('company_user', 'admin/company_user');
// 供应商
Route::get('company_tree', 'admin/company/companyTree'); // 供应商列表树
Route::POST('createCompany','admin/Company/submitCompany'); // 创建供应商
