<?php

use think\Route;

/* -------------------- 后台管理系统路由 -------------------- s */

// 登录
Route::post('login', 'admin/Login/login');
Route::get('code', 'admin/Login/createverifycode');
Route::put('logout', 'admin/Login/logout');

// 上传、删除图片
Route::post('upload', 'admin/Upload/uploadimg');
Route::post('deleteimages', 'admin/Upload/deleteimg');

// Auth用户组
Route::resource('auth_group', 'admin/auth_group');
Route::get('auth_group_tree', 'admin/auth_group/authGroupTree'); // Auth用户组列表树
Route::put('config_auth_group_rule/:id', 'admin/auth_group/configAuthGroupRule'); // 配置Auth用户组权限规则
// Auth权限规则
Route::resource('auth_rule', 'admin/auth_rule');
Route::get('auth_rule_tree', 'admin/auth_rule/authRuleTree'); // Auth权限规则列表树
Route::get('lazy_load_auth_rule_tree', 'admin/auth_rule/lazyLoadAuthRuleTree'); // 懒加载Auth权限规则树形列表
// Auth权限规则菜单
Route::get('auth_rule_menus', 'admin/AuthRuleMenus/authRuleMenus'); // 权限规则菜单

// 管理员
Route::resource('admin', 'admin/Admin');

// 区域
Route::resource('region', 'admin/Region');
Route::get('lazy_load_region_tree', 'admin/Region/lazyLoadRegionTree'); // 懒加载区域树形数据
Route::get('get_region_list', 'admin/Region/getRegionList'); // 获取区域列表数据（用于级联选择器等）

// 分公司
Route::resource('company', 'admin/Company');
Route::get('company_tree', 'admin/Company/companyTree'); // 分公司列表树

// 用户角色
Route::resource('user_role', 'admin/UserRole');
Route::get('user_role_list', 'admin/UserRole/userRoleList'); // 用户角色列表（不分页）

// 用户（业务员）
Route::resource('user_salesman', 'admin/UserSalesman');
Route::get('son_salesman', 'admin/UserSalesman/sonSalesman'); // 获取指定角色的下级业务员销售数据列表
Route::get('get_salesman_list', 'admin/UserSalesman/getSalesmanList'); //获取业务员列表（不分页，用于 Select 选择器等）
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
Route::get('get_device_brand', 'admin/Device/getDeviceBrand');
Route::post('get_device_model', 'admin/Device/getDeviceModel');
Route::get('get_device_size', 'admin/Device/getDeviceSize');
Route::get('get_device_status', 'admin/Device/getDeviceStatus');
Route::get('get_device_level', 'admin/Device/getDeviceLevel');
Route::get('get_device_shop', 'admin/Device/getDeviceShop');

// 广告
Route::resource('ad', 'admin/Ad');
// 广告类别
Route::get('ad_cate_list', 'admin/AdCate/adCateList'); // 广告类别列表（不分页）
// 广告套餐
Route::resource('ad_combo', 'admin/AdCombo');
// 广告套餐订单
Route::resource('ad_combo_order', 'admin/AdComboOrder');
// 广告案例
Route::resource('ad_case', 'admin/AdCase');

// 店铺
Route::resource('shop', 'admin/Shop');
Route::get('shop_count', 'admin/Shop/getShopCount'); // 统计店铺数据
// 店铺类别
Route::get('shop_cate_list', 'admin/ShopCate/shopCateList'); // 店铺类别列表（不分页）
// 店铺周边环境
Route::get('shop_environment', 'admin/ShopCate/shopEnvironment'); // 店铺环境列表（不分页）

// 活动
Route::resource('activity', 'admin/Activity');
Route::get('activity_list', 'admin/Activity/activityList');  // 活动列表（不分页）
// 活动奖品
Route::resource('act_prize', 'admin/ActPrize');
Route::get('act_prize_level', 'admin/ActPrize/actPrizeLevelList'); // 活动奖品等级列表
// 活动中奖纪录
Route::resource('act_raffle', 'admin/ActRaffle');

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
Route::post('api/thirdlogin', 'api/ThirdLogin/thirdlogin'); // 第三方授权登录
Route::post('api/bind_phone', 'api/ThirdLogin/bindPhone'); // 第三方授权登录绑定手机号

// 调用短信接口
Route::post('api/send_sms', 'api/SendSms/sendSms');

// 调用微信支付接口
Route::post('api/wxPay', 'api/WxPay/index'); // 统一下单（测试）
Route::post('api/wxPayNotify', 'api/WxPayNotify/notify'); // 微信支付回调通知
Route::post('api/adWxPay', 'api/WxPay/adWxPay'); // 广告投放订单（微信）支付

// 用户个人中心
Route::resource('api/user', 'api/User');
Route::post('api/apply_partner', 'api/User/applyPartner'); // 申请成为广告屏合作商
Route::post('api/apply_shopkeeper', 'api/User/applyShopkeeper'); // 申请成为店家
Route::post('api/get_user_role', 'api/User/getUserRole');
// 用户获得的奖品
Route::get('api/user_prize_count', 'api/User/userPrizeCount'); // 统计用户获得的奖品数量
Route::get('api/user_prize_list', 'api/User/userPrizeList'); // 获取用户获得的奖品列表

// 广告屏
Route::get('api/device_all_list','api/Device/index');
Route::get('api/device_list','api/Device/getDeviceList');
Route::get('api/device_detail','api/Device/deviceDetail');
Route::get('api/device_size','api/Device/getSize');
Route::get('api/device_price','api/Device/getPrice');


// 广告
Route::resource('api/ad', 'api/Ad');
Route::get('api/get_ad', 'api/Ad/getAd');
Route::get('api/get_ad_cate', 'api/Ad/getAdCate');
// 广告类别
Route::get('api/ad_cate_list', 'api/AdCate/adCateList'); // 广告类别列表（不分页）

// 广告套餐
Route::get('api/ad_combo_list', 'api/AdCombo/AdComboList'); // 广告套餐列表（不分页）

// 广告套餐订单
Route::resource('api/ad_combo_order', 'api/AdComboOrder');

// 业务员
Route::get('api/partner_salesman', 'api/UserSalesman/partnerSalesman'); // 获取指定的广告屏合作商业务员
Route::get('api/partner_salesman_list', 'api/UserSalesman/partnerSalesmanList'); // 获取广告屏合作商业务员列表
Route::get('api/shopkeeper_salesman_list', 'api/UserSalesman/shopkeeperSalesmanList'); // 获取店铺业务员列表
Route::get('api/getMoney', 'api/UserSalesman/getMoney');
Route::get('api/getMoneyDevice', 'api/UserSalesman/getMoneyDevice');
Route::get('api/getMoneyShop', 'api/UserSalesman/getMoneyShop');
Route::get('api/get_role_status', 'api/UserSalesman/getRoleStatus');
Route::post('api/apply_salesman', 'api/UserSalesman/applySalesman'); // 申请成为业务员
Route::post('api/sale_info', 'api/UserSalesman/getSaleInfo');
Route::post('api/sale_count', 'api/UserSalesman/getSaleCount');
Route::post('api/shop_count', 'api/UserSalesman/getShopCount');
Route::get('api/salesman_shop_list', 'api/UserSalesman/getSalesmanShopList');
// 广告主业务员
Route::get('api/get_advertiser_salesman', 'api/AdvertiserSalesman/read');
Route::get('api/get_advertiser_salesman_ad_count', 'api/AdvertiserSalesman/getAdvertiserSalesmanAdCount'); // 统计广告主业务员广告数
Route::get('api/get_advertiser_salesman_list', 'api/AdvertiserSalesman/getAdvertiserSalesmanList'); // 获取（下级）广告主业务员列表

// 用户（广告屏合作商）合作的广告屏
Route::resource('api/partner_device', 'api/PartnerDevice');
Route::put('api/partnerRole', 'api/PartnerDevice/partnerRole');
// 用户（广告屏合作商）订单
Route::resource('api/partner_order', 'api/PartnerOrder');

// 用户（店家）
Route::get('api/get_shopkeeper', 'api/UserShopkeeper/read'); // 店家信息
Route::get('api/shopkeeper_shop_list', 'api/UserShopkeeper/shopList'); // 店家店铺列表
Route::put('api/shopRole', 'api/UserShopkeeper/shopRole');
Route::post('api/shopkeeper_order_commission', 'api/UserShopkeeperOrderCommission/shopkeeperOrderCommission'); // 商城系统 dt.dilinsat.com 购买商品后广告机安装店家提成

// 店铺
Route::resource('api/shop', 'api/Shop');
// 店铺类别
Route::get('api/shop_cate_list', 'api/ShopCate/shopCateList'); // 店铺类别列表（不分页）
// 店铺周边环境
Route::get('api/shop_environment', 'api/ShopCate/shopEnvironment'); // 店铺环境列表（不分页）

// 新闻
Route::resource('api/news', 'api/News');

// 用户反馈
Route::resource('api/feedback', 'api/Feedback');

//上传图片
Route::post('api/upload', 'api/Upload/upload');
Route::put('api/deleimg', 'api/Upload/deleimg');

//获取整体广告屏、城市、商家数据
Route::get('api/get-total-data', 'api/Index/getTotalData');

// 区域
Route::get('api/lazy_load_region_tree', 'api/Region/lazyLoadRegionTree'); // 懒加载区域树形数据
Route::get('api/get_region_list', 'api/Region/getRegionList'); // 获取区域列表数据（用于级联选择器等）

// 获取店通已开通的城市
Route::get('api/get_fix_city', 'api/Index/getCity'); // 懒加载区域树形数据

// 抽奖活动
Route::get('api/get_prize', 'api/Prize/getPrize'); // 获取中奖奖品信息
Route::post('api/winner_info', 'api/Prize/winnerInfo'); // 提交领奖信息
Route::post('api/record_raffle_log', 'api/Prize/recordRaffleLog'); // 记录抽奖信息

// 活动中奖记录
Route::get('api/raffle_prize_count', 'api/ActRaffle/rafflePrizeCount'); // 统计奖品领取状态数量
Route::get('api/raffle_prize_list', 'api/ActRaffle/rafflePrizeList'); // 获取奖品领取状态列表
Route::put('api/update_prize_status', 'api/ActRaffle/updatePrizeStatus'); // 更新领奖状态

//获取广告案例
Route::get('api/get_case', 'api/Adcase/getCase'); // 获取中奖奖品信息

/* -------------------- 客户端路由 -------------------- e */





/* -------------------- 微信公众号开发路由 -------------------- s */

Route::any('api/wechat', 'api/WeChat/index'); // 检验signature 与 接收事件推送（关注/取消关注事件）。Route::any() 所有请求都支持的路由规则。此处切勿使用 Route::get()
Route::any('api/http_curl', 'api/WeChat/http_curl'); // cURL请求
Route::get('api/get_wx_access_token', 'api/WeChat/getWxAccessToken'); // 获取微信公众号access_token
Route::get('api/get_wx_server_ip', 'api/WeChat/getWxServerIp'); // 获取微信服务器IP地址
Route::get('api/get_wx_qrcode_ticket', 'api/WeChat/getQRCodeTicket'); // 生成带参数的二维码：第一步、创建二维码ticket
Route::get('api/show_wx_qrcode', 'api/WeChat/showQRCode'); // 生成带参数的二维码：第二步、通过ticket到指定URL换取二维码
Route::get('api/create_wx_menu', 'api/WeChat/definedItem'); // 创建自定义菜单

/* -------------------- 微信公众号开发路由 -------------------- e */