<?php
use think\Route;

/*-----后台管理中心路由-----*/
//登录
Route::POST('login','admin/Login/login');
//登录验证码
Route::GET('code','admin/Login/createverifycode');
// 分公司
Route::resource('company', 'admin/company');
Route::POST('createCompany','admin/Company/createCompany'); // 创建分公司
//传媒屏管理
Route::POST('getCompany','admin/Company/getCompany');
// 区域
Route::resource('region', 'admin/region');
// 用户（传媒设备合作者）
Route::resource('user_partner', 'admin/UserPartner');
// 用户（传媒设备合作者业务员）
Route::resource('user_to_partner', 'admin/UserToPartner');
// 用户（广告主业务员）
Route::resource('user_to_ad', 'admin/UserToAd');
// 用户（店铺端业务员）
Route::resource('user_to_shop', 'admin/UserToShop');
// 用户（店铺端用户）
Route::resource('user_shop', 'admin/UserShop');



//上传图片
Route::POST('upload','admin/Common/uploadimg');
//删除图片
Route::POST('deleteimages','admin/Common/deleteimg');
//获取地区列表
Route::POST('getzone','admin/Company/getzone');
//获取供应商销售区域数据
Route::POST('companyarea','admin/Company/getarea_company');
//插入供应商销售区域数据
Route::POST('submitArea','admin/Company/submitArea');
//获取供应商销售商品种类
Route::POST('getshopcate','admin/Company/getshopcate_company');
//插入供应商销售商品种类
Route::POST('shopcateinsert','admin/Company/cate_insert');
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

Route::get('company_tree', 'admin/company/companyTree'); // 供应商列表树




/* 客户端路由 */
// 登录与注册
Route::put('api/login', 'api/login/login'); // 登录
Route::post('api/register', 'api/login/register'); // 注册
Route::put('api/pwd', 'api/login/pwd'); // 找回密码
Route::put('api/logout', 'api/login/logout'); // 退出登录
// 用户
Route::resource('api/user', 'api/user');
