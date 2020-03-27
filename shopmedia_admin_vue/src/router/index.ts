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
// 用户类型
import UserType from '@/pages/user_type/UserType.vue'
import UserTypeEdit from '@/pages/user_type/UserTypeEdit.vue'
// 用户管理（传媒设备合作者）
import UserPartner from '@/pages/user_partner/UserPartner.vue'
import UserPartnerAdd from '@/pages/user_partner/UserPartnerAdd.vue'
import UserPartnerEdit from '@/pages/user_partner/UserPartnerEdit.vue'
import UserPartnerDevice from '@/pages/user_partner/UserPartnerDevice.vue'
import UserPartnerDeviceEdit from '@/pages/user_partner/UserPartnerDeviceEdit.vue'
// 用户管理（传媒设备合作者业务员）
import UserToPartner from '@/pages/user_to_partner/UserToPartner.vue'
import UserToPartnerEdit from '@/pages/user_to_partner/UserToPartnerEdit.vue'
// 用户管理（广告主业务员）
import UserToAd from '@/pages/user_to_ad/UserToAd.vue'
import UserToAdEdit from '@/pages/user_to_ad/UserToAdEdit.vue'
// 用户管理（店铺端业务员）
import UserToShop from '@/pages/user_to_shop/UserToShop.vue'
// 用户管理（店铺端用户）
import UserShop from '@/pages/user_shop/UserShop.vue'
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
			// 2--用户类型
			{path: 'user_type', name: 'user_type', component: UserType}, // 用户类型列表
			{path: 'user_type_edit', name: 'user_type_edit', component: UserTypeEdit}, // 用户类型列表
			// 2--用户管理（传媒设备合作者）
			{path: 'user_partner', name: 'user_partner', component: UserPartner}, // 用户列表
			{path: 'user_partner_add', name: 'user_partner_add', component: UserPartnerAdd}, // 新增用户
			{path: 'user_partner_edit', name: 'user_partner_edit', component: UserPartnerEdit}, // 编辑用户
			{path: 'user_partner_device', name: 'user_partner_device', component: UserPartnerDevice}, // 用户拥有的设备
			{path: 'user_partner_device_edit', name: 'user_partner_device_edit', component: UserPartnerDeviceEdit}, // 编辑用户拥有的设备
			// 2--用户管理（传媒设备合作者业务员）
			{path: 'user_to_partner', name: 'user_to_partner', component: UserToPartner}, // 用户列表
			{path: 'user_to_partner_edit', name: 'user_to_partner_edit', component: UserToPartnerEdit}, // 编辑用户
			// 2--用户管理（广告主业务员）
			{path: 'user_to_ad', name: 'user_to_ad', component: UserToAd}, // 用户列表
			{path: 'user_to_ad_edit', name: 'user_to_ad_edit', component: UserToAdEdit}, // 编辑用户
			// 2--用户管理（店铺端业务员）
			{path: 'user_to_shop', name: 'user_to_shop', component: UserToShop}, // 用户列表
			// 2--用户管理（店铺端用户）
			{path: 'user_shop', name: 'user_shop', component: UserShop}, // 用户列表
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
