<?php

use think\Route;

/* -------------------- 后台管理系统路由 -------------------- s */

// 登录
Route::post('login', 'admin/Login/login');
Route::get('code', 'admin/Login/createverifycode');

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
Route::post('createCompany', 'admin/Company/createCompany'); // 创建分公司
Route::post('getCompany', 'admin/Company/getCompany'); // 获取分公司基本信息
Route::post('getzone', 'admin/Company/getzone');

// 用户角色
Route::resource('user_role', 'admin/UserRole');
Route::get('user_role_list', 'admin/UserRole/UserRoleList'); // 用户角色列表（不分页）

// 用户（业务员）
Route::resource('user_salesman', 'admin/UserSalesman');
Route::put('user_salesman', 'admin/UserSalesman');
Route::get('son_salesman', 'admin/UserSalesman/sonSalesman'); // 获取指定角色的下级业务员销售数据列表
// 用户（广告屏合作商）
Route::resource('user_partner', 'admin/UserPartner');
Route::get('user_partner_list', 'admin/UserPartner/userPartnerList');  // 广告屏合作商列表（不分页）
// 用户（广告屏合作商）合作的广告屏
Route::resource('partner_device', 'admin/PartnerDevice');
// 用户（广告屏合作商）订单
Route::resource('partner_order', 'admin/PartnerOrder');
// 用户（店家）
Route::resource('user_shopkeeper', 'admin/UserShopkeeper');
// 用户（广告主）
Route::resource('user_advertiser', 'admin/UserAdvertiser');

// 广告屏管理
Route::resource('device', 'admin/Device');
Route::get('device_list', 'admin/Device/deviceList');  // 广告屏列表（不分页）
Route::post('addDevice', 'admin/Device/addDevice');
Route::post('getDevice', 'admin/Device/getDevice');
Route::get('get_device_brand', 'admin/Device/getDeviceBrand');
Route::post('get_device_model', 'admin/Device/getDeviceModel');
Route::get('get_device_size', 'admin/Device/getDeviceSize');
Route::get('get_device_status', 'admin/Device/getDeviceStatus');
Route::get('get_device_level', 'admin/Device/getDeviceLevel');
Route::get('get_device_shop', 'admin/Device/getDeviceShop');

// 广告
Route::resource('ad', 'admin/Ad');
Route::get('get_ad_cate', 'admin/Ad/getAdCate');
// 广告类别
Route::get('ad_cate_list', 'admin/AdCate/adCateList'); // 广告类别列表（不分页）

// 店家店铺
Route::resource('shop', 'admin/Shop');
Route::get('shop_count', 'admin/Shop/getShopCount'); // 统计店铺数据
// 店铺类别
Route::get('shop_cate_list', 'admin/ShopCate/shopCateList'); // 店铺类别列表（不分页）// 管理员

// 新闻
Route::resource('news', 'admin/News');
// 新闻类别
Route::resource('news_cate', 'admin/NewsCate');

// 用户反馈
Route::resource('feedback', 'admin/Feedback');


/* -------------------- 后台管理系统路由 -------------------- e */










/* -------------------- 客户端路由 -------------------- s */

// 登录与注册
Route::put('api/login', 'api/Login/login'); // 登录
Route::post('api/register', 'api/Login/register'); // 注册
Route::put('api/pwd', 'api/Login/pwd'); // 找回密码
Route::put('api/logout', 'api/Login/logout'); // 退出登录
Route::post('api/hasphone', 'api/Login/hasphone'); // 检查电话是否存在

// 调用短信接口
Route::post('api/send_sms', 'api/SendSms/sendSms');

// 用户个人中心
Route::resource('api/user', 'api/User');
Route::post('api/apply_partner', 'api/User/applyPartner'); // 申请成为广告屏合作商
Route::post('api/apply_shopkeeper', 'api/User/applyShopkeeper'); // 申请成为店家
Route::post('api/get_user_role', 'api/User/getUserRole');

// 广告屏
Route::get('api/device_list','api/Device/getDeviceList');
Route::get('api/device_detail','api/Device/deviceDetail');

// 业务员
Route::get('api/partner_salesman', 'api/UserSalesman/partnerSalesman'); // 获取指定的广告屏合作商业务员
Route::get('api/getMoney', 'api/UserSalesman/getMoney');
Route::get('api/getMoneyDevice', 'api/UserSalesman/getMoneyDevice');
Route::get('api/getMoneyShop', 'api/UserSalesman/getMoneyShop');
Route::get('api/get_role_status', 'api/UserSalesman/getRoleStatus');
Route::post('api/apply_salesman', 'api/UserSalesman/applySalesman'); // 申请成为业务员
Route::post('api/sale_info', 'api/UserSalesman/getSaleInfo');
Route::post('api/sale_count', 'api/UserSalesman/getSaleCount');
// 用户（广告屏合作商）合作的广告屏
Route::resource('api/partner_device', 'api/PartnerDevice');
Route::put('api/partnerRole', 'api/PartnerDevice/partnerRole');
// 用户（广告屏合作商）订单
Route::resource('api/partner_order', 'api/PartnerOrder');

// 用户（店家）
Route::get('api/shopkeeper_shop_list', 'api/UserShopkeeper/shopList'); // 店家店铺列表
Route::put('api/shopRole', 'api/UserShopkeeper/shopRole');

// 店家店铺
Route::resource('api/shop', 'api/Shop');
// 店铺类别
Route::get('api/shop_cate_list', 'api/ShopCate/shopCateList'); // 店铺类别列表（不分页）// 管理员
Route::get('api/shop_environment', 'api/ShopCate/shopEnvironment'); // 店铺环境列表（不分页）

// 新闻
Route::resource('api/news', 'api/News');

// 用户反馈
Route::resource('api/feedback', 'api/Feedback');

//上传图片
Route::post('api/upload', 'api/Upload/upload');
Route::put('api/deleimg', 'api/Upload/deleimg');

/* -------------------- 客户端路由 -------------------- e */