<?php

use think\Route;

/* -------------------- 后台管理系统路由 -------------------- s */

// 登录
Route::post('login','admin/Login/login');
Route::get('code','admin/Login/createverifycode');

// 上传、删除图片
Route::post('upload', 'admin/upload/uploadimg');
Route::post('deleteimages', 'admin/upload/deleteimg');

// Auth用户组
Route::resource('auth_group', 'admin/auth_group');
Route::get('auth_group_tree', 'admin/auth_group/authGroupTree'); // Auth用户组列表树
Route::put('config_auth_group_rule/:id', 'admin/auth_group/configAuthGroupRule'); // 配置Auth用户组权限规则
// Auth权限规则
Route::resource('auth_rule', 'admin/auth_rule');
Route::get('auth_rule_tree', 'admin/auth_rule/authRuleTree'); // Auth权限规则列表树
Route::get('lazy_load_auth_rule_tree', 'admin/auth_rule/lazyLoadAuthGroupTree'); // 懒加载Auth权限规则树形列表
// 管理员
Route::resource('admin', 'admin/admin');

// 区域
Route::resource('region', 'admin/region');
Route::get('lazy_load_region_tree', 'admin/region/lazyLoadRegionTree'); // 懒加载区域树形数据

// 分公司
Route::resource('company', 'admin/company');
Route::get('company_tree', 'admin/company/companyTree'); // 分公司列表树
Route::post('createCompany','admin/Company/createCompany'); // 创建分公司
Route::post('getCompany','admin/Company/getCompany'); // 获取分公司基本信息
Route::post('getzone','admin/Company/getzone');

// 用户角色
Route::resource('user_role', 'admin/UserRole');
Route::get('user_role_list', 'admin/UserRole/UserRoleList'); // 用户角色列表（不分页）

// 用户（业务员）
Route::resource('user_salesman', 'admin/UserSalesman');
// 用户（广告屏合作商）
Route::resource('user_partner', 'admin/UserPartner');
Route::get('user_partner_list','admin/UserPartner/userPartnerList');  // 广告屏合作商列表（不分页）
// 用户（广告屏合作商）合作的广告屏
Route::resource('partner_device', 'admin/PartnerDevice');
// 用户（广告屏合作商）订单
Route::resource('partner_order', 'admin/PartnerOrder');
// 用户（店铺端用户）
Route::resource('user_shop', 'admin/UserShop');

// 广告屏管理
Route::resource('device','admin/Device');
Route::get('device_list','admin/Device/deviceList');  // 广告屏列表（不分页）
Route::post('addDevice','admin/Device/addDevice');
Route::post('getDevice','admin/Device/getDevice');


// 广告
Route::resource('ad','admin/Ad');
// 广告类别
Route::get('ad_cate_list','admin/AdCate/adCateList'); // 广告类别列表（不分页）

// 店铺类别
Route::get('shop_cate_list','admin/ShopCate/shopCateList'); // 店铺类别列表（不分页）


/* -------------------- 后台管理系统路由 -------------------- e */










/* -------------------- 客户端路由 -------------------- s */

// 登录与注册
Route::put('api/login', 'api/Login/login'); // 登录
Route::post('api/register', 'api/Login/register'); // 注册
Route::put('api/pwd', 'api/Login/pwd'); // 找回密码
Route::put('api/logout', 'api/Login/logout'); // 退出登录

// 调用短信接口
Route::post('api/send_sms', 'api/SendSms/sendSms');

// 用户个人中心
Route::resource('api/user', 'api/User');
Route::post('api/applyPartner', 'api/User/applyPartner');

// 业务员
Route::get('api/getMarkers','api/Saleperson/getMarkers');
Route::post('api/DeviceDetail','api/Saleperson/DeviceDetail');
Route::get('api/shopCateList','api/Saleperson/shopCateList');
Route::get('api/partner_salesman', 'api/UserSalesman/partnerSalesman'); // 获取指定的广告屏合作商业务员

// 用户（广告屏合作商）合作的广告屏
Route::resource('api/partner_device', 'api/PartnerDevice');
// 用户（广告屏合作商）订单
Route::resource('api/partner_order', 'api/PartnerOrder');


/* -------------------- 客户端路由 -------------------- e */