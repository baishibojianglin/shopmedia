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
// 用户（广告屏合作商）管理
import UserPartner from '@/pages/user_partner/UserPartner.vue'
import UserPartnerCreate from '@/pages/user_partner/UserPartnerCreate.vue'
import UserPartnerEdit from '@/pages/user_partner/UserPartnerEdit.vue'
// 用户（广告屏合作商）合作的广告屏
import PartnerDevice from '@/pages/partner_device/PartnerDevice.vue'
import PartnerDeviceEdit from '@/pages/partner_device/PartnerDeviceEdit.vue'
// 用户（店铺端用户）管理
import UserShop from '@/pages/user_shop/UserShop.vue'
import UserShopEdit from '@/pages/user_shop/UserShopEdit.vue'
// 广告管理
import Ad from '@/pages/ad/Ad.vue'
import AdCreate from '@/pages/ad/AdCreate.vue'
import AdEdit from '@/pages/ad/AdEdit.vue'


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
			// 2--用户（广告屏合作商）管理
			{path: 'user_partner', name: 'user_partner', component: UserPartner}, // 用户列表
			{path: 'user_partner_create', name: 'user_partner_create', component: UserPartnerCreate}, // 新增用户
			{path: 'user_partner_edit', name: 'user_partner_edit', component: UserPartnerEdit}, // 编辑用户
			{path: 'partner_device', name: 'partner_device', component: PartnerDevice}, // 用户合作的设备
			{path: 'partner_device_edit', name: 'partner_device_edit', component: PartnerDeviceEdit}, // 编辑用户合作的设备
			// 2--用户（店铺端用户）管理
			{path: 'user_shop', name: 'user_shop', component: UserShop}, // 用户列表
			{path: 'user_shop_edit', name: 'user_shop_edit', component: UserShopEdit}, // 编辑用户
			// 2--广告管理
			{path: 'ad', name: 'ad', component: Ad}, // 广告列表
			{path: 'ad_create', name: 'ad_create', component: AdCreate}, // 新增广告
			{path: 'ad_edit', name: 'ad_edit', component: AdEdit}, // 编辑广告
		]
	}
]

const router = new VueRouter({
	routes
})

export default router
