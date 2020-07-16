import Vue from 'vue'
import VueRouter from 'vue-router'
import Login from '@/views/Login.vue'
import Home from '@/views/Home.vue'

// 管理员管理·角色
import AuthGroup from '@/pages/auth_group/AuthGroup.vue'
import AuthGroupCreate from '@/pages/auth_group/AuthGroupCreate.vue'
import AuthGroupEdit from '@/pages/auth_group/AuthGroupEdit.vue'
import AuthGroupRule from '@/pages/auth_group/AuthGroupRule.vue'
// 管理员管理·权限规则
import AuthRule from '@/pages/auth_rule/AuthRule.vue'
import AuthRuleCreate from '@/pages/auth_rule/AuthRuleCreate.vue'
import AuthRuleEdit from '@/pages/auth_rule/AuthRuleEdit.vue'
// 管理员管理·管理员
import Admin from '@/pages/admin/Admin.vue'
import AdminCreate from '@/pages/admin/AdminCreate.vue'
import AdminEdit from '@/pages/admin/AdminEdit.vue'
// 区域管理
import Region from '@/pages/region/Region.vue'
import RegionCity from '@/pages/region/RegionCity.vue'
import RegionCounty from '@/pages/region/RegionCounty.vue'
import RegionTown from '@/pages/region/RegionTown.vue'
// 设备管理
import Device from '@/pages/device/Device.vue'
import AddDevice from '@/pages/device/AddDevice.vue'
import EditDevice from '@/pages/device/EditDevice.vue'
// 分公司管理
import Company from '@/pages/company/Company.vue'
import CompanyCreate from '@/pages/company/CompanyCreate.vue'
import CompanyEdit from '@/pages/company/CompanyEdit.vue'
// 用户角色
import UserRole from '@/pages/user_role/UserRole.vue'
import UserRoleEdit from '@/pages/user_role/UserRoleEdit.vue'
// 用户（业务员）管理
import UserSalesman from '@/pages/user_salesman/UserSalesman.vue'
import UserSalesmanCreate from '@/pages/user_salesman/UserSalesmanCreate.vue'
import UserSalesmanEdit from '@/pages/user_salesman/UserSalesmanEdit.vue'
import SonSalesman from '@/pages/user_salesman/SonSalesman.vue'
// 用户（广告屏合作商）管理
import UserPartner from '@/pages/user_partner/UserPartner.vue'
import UserPartnerCreate from '@/pages/user_partner/UserPartnerCreate.vue'
import UserPartnerEdit from '@/pages/user_partner/UserPartnerEdit.vue'
// 用户（广告屏合作商）合作的广告屏
import PartnerDevice from '@/pages/partner_device/PartnerDevice.vue'
import PartnerDeviceEdit from '@/pages/partner_device/PartnerDeviceEdit.vue'
// 用户（店家）管理
import UserShopkeeper from '@/pages/user_shopkeeper/UserShopkeeper.vue'
import UserShopkeeperEdit from '@/pages/user_shopkeeper/UserShopkeeperEdit.vue'
// 用户（广告主）管理
import UserAdvertiser from '@/pages/user_advertiser/UserAdvertiser.vue'
import UserAdvertiserEdit from '@/pages/user_advertiser/UserAdvertiserEdit.vue'
// 店铺
import Shop from '@/pages/shop/Shop.vue'
import ShopEdit from '@/pages/shop/ShopEdit.vue'
// 广告管理
import Ad from '@/pages/ad/Ad.vue'
import AdCreate from '@/pages/ad/AdCreate.vue'
import AdEdit from '@/pages/ad/AdEdit.vue'
// 广告套餐
import AdCombo from '@/pages/ad_combo/AdCombo.vue'
// 广告套餐订单
import AdComboOrder from '@/pages/ad_combo_order/AdComboOrder.vue'
// 广告案例
import AdCase from '@/pages/ad_case/AdCase.vue'
import AdCaseCreate from '@/pages/ad_case/AdCaseCreate.vue'
import AdCaseEdit from '@/pages/ad_case/AdCaseEdit.vue'
// 订单管理·用户（广告屏合作商）订单
import PartnerOrder from '@/pages/partner_order/PartnerOrder.vue'
import PartnerOrderCreate from '@/pages/partner_order/PartnerOrderCreate.vue'
import PartnerOrderEdit from '@/pages/partner_order/PartnerOrderEdit.vue'
// 活动管理
import Activity from '@/pages/activity/Activity.vue'
import ActivityCreate from '@/pages/activity/ActivityCreate.vue'
import ActivityEdit from '@/pages/activity/ActivityEdit.vue'
// 活动奖品管理
import ActPrize from '@/pages/act_prize/ActPrize.vue'
import ActPrizeCreate from '@/pages/act_prize/ActPrizeCreate.vue'
import ActPrizeEdit from '@/pages/act_prize/ActPrizeEdit.vue'
// 活动中奖纪录
import ActRaffle from '@/pages/act_raffle/ActRaffle.vue'
// 新闻管理
import News from '@/pages/news/News.vue'
import NewsCreate from '@/pages/news/NewsCreate.vue'
import NewsEdit from '@/pages/news/NewsEdit.vue'
// 用户反馈
import Feedback from '@/pages/feedback/Feedback.vue'
import FeedbackHandle from '@/pages/feedback/FeedbackHandle.vue'

Vue.use(VueRouter)

const routes = [
	{
		// 1--登录
		path: '/',
		name: 'login',
		component:Login
		//component:() => import(/* webpackChunkName: '1' */ '../views/Login.vue')
	},
	{
		// 1--首页
		path: '/home',
		name: 'home',
		component:Home,
		children: [
			// 2--管理员管理
			// 角色
			{path: 'auth_group', name: 'auth_group', component: AuthGroup}, // 角色管理
			{path: 'auth_group_create', name: 'auth_group_create', component: AuthGroupCreate}, // 新增角色
			{path: 'auth_group_edit', name: 'auth_group_edit', component: AuthGroupEdit}, // 编辑角色
			{path: 'auth_group_rule', name: 'auth_group_rule', component: AuthGroupRule}, // 角色权限规则配置
			// 权限规则
			{path: 'auth_rule', name: 'auth_rule', component: AuthRule}, // 权限规则
			{path: 'auth_rule_create', name: 'auth_rule_create', component: AuthRuleCreate}, // 新增权限规则
			{path: 'auth_rule_edit', name: 'auth_rule_edit', component: AuthRuleEdit}, // 编辑权限规则
			// 管理员
			{path: 'admin', name: 'admin', component: Admin}, // 管理员列表
			{path: 'admin_create', name: 'admin_create', component: AdminCreate}, // 新增管理员
			{path: 'admin_edit', name: 'admin_edit', component: AdminEdit}, // 编辑管理员
			// 2--区域管理
			{path: 'region', name: 'region', component: Region}, // 省级区域
			{path: 'regioncity', name: 'regioncity', component: RegionCity}, // 市级区域
			{path: 'regioncounty', name: 'regioncounty', component: RegionCounty}, // 区县级区域
			{path: 'regiontown', name: 'regiontown', component: RegionTown}, // 乡镇街道级区域
			// 2--设备管理
			{path: 'device', name: 'device', component: Device}, //设备列表
			{path: 'adddevice',name: 'adddevice',component:AddDevice}, //添加设备
			{path: 'editdevice',name: 'editdevice',component:EditDevice}, //添加设备
			// 2--分公司管理
			{path: 'company',name: 'company',component:Company}, //分公司列表
			{path: 'companycreate',name: 'companycreate',component:CompanyCreate}, //创建供应商
			{path: 'companyedit',name: 'companyedit',component:CompanyEdit}, //创建供应商
			// 2--用户角色
			{path: 'user_role', name: 'user_role', component: UserRole}, // 用户角色列表
			{path: 'user_role_edit', name: 'user_role_edit', component: UserRoleEdit}, // 用户角色列表
			// 2--用户（业务员）管理
			{path: 'user_salesman', name: 'user_salesman', component: UserSalesman}, // 用户列表
			{path: 'user_salesman_create', name: 'user_salesman_create', component: UserSalesmanCreate}, // 创建用户
			{path: 'user_salesman_edit', name: 'user_salesman_edit', component: UserSalesmanEdit}, // 编辑用户
			{path: 'son_salesman', name: 'son_salesman', component: SonSalesman}, // 下级业务员列表
			// 2--用户（广告屏合作商）管理
			{path: 'user_partner', name: 'user_partner', component: UserPartner}, // 用户列表
			{path: 'user_partner_create', name: 'user_partner_create', component: UserPartnerCreate}, // 新增用户
			{path: 'user_partner_edit', name: 'user_partner_edit', component: UserPartnerEdit}, // 编辑用户
			{path: 'partner_device', name: 'partner_device', component: PartnerDevice}, // 用户合作的设备
			{path: 'partner_device_edit', name: 'partner_device_edit', component: PartnerDeviceEdit}, // 编辑用户合作的设备
			// 2--用户（店家）管理
			{path: 'user_shopkeeper', name: 'user_shopkeeper', component: UserShopkeeper}, // 用户列表
			{path: 'user_shopkeeper_edit', name: 'user_shopkeeper_edit', component: UserShopkeeperEdit}, // 编辑用户
			// 2--店铺
			{path: 'shop', name: 'shop', component: Shop}, // 店铺列表
			{path: 'shop_edit', name: 'shop_edit', component: ShopEdit}, // 编辑店铺
			// 2--用户（广告主）管理
			{path: 'user_advertiser', name: 'user_advertiser', component: UserAdvertiser}, // 用户列表
			{path: 'user_advertiser_edit', name: 'user_advertiser_edit', component: UserAdvertiserEdit}, // 编辑用户
			// 2--广告管理
			{path: 'ad', name: 'ad', component: Ad}, // 广告列表
			{path: 'ad_create', name: 'ad_create', component: AdCreate}, // 新增广告
			{path: 'ad_edit', name: 'ad_edit', component: AdEdit}, // 编辑广告
			// 2--广告套餐
			{path: 'ad_combo', name: 'ad_combo', component: AdCombo}, // 广告套餐列表
			// 2--广告套餐订单
			{path: 'ad_combo_order', name: 'ad_combo_order', component: AdComboOrder}, // 广告套餐订单列表
			// 2--广告案例
			{path: 'ad_case', name: 'ad_case', component: AdCase}, // 广告案例列表
			{path: 'ad_case_create', name: 'ad_case_create', component: AdCaseCreate}, // 新增广告案例
			{path: 'ad_case_edit', name: 'ad_case_edit', component: AdCaseEdit}, // 编辑广告案例
			// 2--订单管理·用户（广告屏合作商）订单
			{path: 'partner_order', name: 'partner_order', component: PartnerOrder}, // 广告屏合作商订单
			{path: 'partner_order_create', name: 'partner_order_create', component: PartnerOrderCreate}, // 创建广告屏合作商订单
			{path: 'partner_order_edit', name: 'partner_order_edit', component: PartnerOrderEdit}, // 编辑广告屏合作商订单
			// 2--活动管理
			{path: 'activity', name: 'activity', component: Activity}, // 活动列表
			{path: 'activity_create', name: 'activity_create', component: ActivityCreate}, // 新增活动
			{path: 'activity_edit', name: 'activity_edit', component: ActivityEdit}, // 编辑活动
			// 2--活动奖品管理
			{path: 'act_prize', name: 'act_prize', component: ActPrize}, // 活动奖品列表
			{path: 'act_prize_create', name: 'act_prize_create', component: ActPrizeCreate}, // 新增活动奖品
			{path: 'act_prize_edit', name: 'act_prize_edit', component: ActPrizeEdit}, // 编辑活动奖品
			// 2--活动中奖纪录
			{path: 'act_raffle', name: 'act_raffle', component: ActRaffle}, // 活动中奖纪录列表
			// 2--新闻管理
			{path: 'news', name: 'news', component: News}, // 新闻列表
			{path: 'news_create', name: 'news_create', component: NewsCreate}, // 新增新闻
			{path: 'news_edit', name: 'news_edit', component: NewsEdit}, // 编辑新闻
			// 2--用户反馈
			{path: 'feedback', name: 'feedback', component: Feedback}, // 用户反馈列表
			{path: 'feedback_handle', name: 'feedback_handle', component: FeedbackHandle}, // 处理用户反馈
		]
	}
]

const router = new VueRouter({
	routes
})

export default router
