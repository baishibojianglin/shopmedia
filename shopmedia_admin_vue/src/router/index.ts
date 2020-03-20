import Vue from 'vue'
import VueRouter from 'vue-router'
import Login from '@/views/Login.vue'
import Home from '@/views/Home.vue'
// 设备管理
import AddDevice from '@/pages/device/AddDevice.vue'
//分公司管理
import Company from '@/pages/company/Company.vue'
import CompanyCreate from '@/pages/company/CompanyCreate.vue'


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
			{path: 'adddevice',name: 'adddevice',component:AddDevice}, //添加设备
			// 2--分公司管理
			{path: 'company',name: 'company',component:Company}, //分公司列表
			{path: 'companycreate',name: 'companycreate',component:CompanyCreate}, //创建供应商
		]
	}
]

const router = new VueRouter({
	routes
})

export default router
