import Vue from 'vue'
import VueRouter from 'vue-router'
import Login from '@/views/Login.vue'
import Home from '@/views/Home.vue'
//设备管理
import Device from '@/pages/device/Device.vue'
import AddDevice from '@/pages/device/AddDevice.vue'
import EditDevice from '@/pages/device/EditDevice.vue'
//分公司管理
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
// 用户（传媒设备合作者）管理
import UserPartner from '@/pages/user_partner/UserPartner.vue'
import UserPartnerAdd from '@/pages/user_partner/UserPartnerAdd.vue'
import UserPartnerEdit from '@/pages/user_partner/UserPartnerEdit.vue'
import UserPartnerDevice from '@/pages/user_partner/UserPartnerDevice.vue'
import UserPartnerDeviceEdit from '@/pages/user_partner/UserPartnerDeviceEdit.vue'
// 用户（店铺端用户）管理
import UserShop from '@/pages/user_shop/UserShop.vue'
import UserShopEdit from '@/pages/user_shop/UserShopEdit.vue'
// 区域管理
import Region from '@/pages/region/Region.vue'
import RegionCity from '@/pages/region/RegionCity.vue'
import RegionCounty from '@/pages/region/RegionCounty.vue'
import RegionTown from '@/pages/region/RegionTown.vue'


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
			// 2--用户（传媒设备合作者）管理
			{path: 'user_partner', name: 'user_partner', component: UserPartner}, // 用户列表
			{path: 'user_partner_add', name: 'user_partner_add', component: UserPartnerAdd}, // 新增用户
			{path: 'user_partner_edit', name: 'user_partner_edit', component: UserPartnerEdit}, // 编辑用户
			{path: 'user_partner_device', name: 'user_partner_device', component: UserPartnerDevice}, // 用户拥有的设备
			{path: 'user_partner_device_edit', name: 'user_partner_device_edit', component: UserPartnerDeviceEdit}, // 编辑用户拥有的设备
			// 2--用户（店铺端用户）管理
			{path: 'user_shop', name: 'user_shop', component: UserShop}, // 用户列表
			{path: 'user_shop_edit', name: 'user_shop_edit', component: UserShopEdit}, // 编辑用户
			// 2--区域管理
			{path: 'region', name: 'region', component: Region}, // 省级区域
			{path: 'regioncity', name: 'regioncity', component: RegionCity}, // 市级区域
			{path: 'regioncounty', name: 'regioncounty', component: RegionCounty}, // 区县级区域
			{path: 'regiontown', name: 'regiontown', component: RegionTown}, // 乡镇街道级区域
		]
	}
]

const router = new VueRouter({
	routes
})

export default router
