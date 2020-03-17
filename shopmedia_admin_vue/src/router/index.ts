import Vue from 'vue'
import VueRouter from 'vue-router'
import Login from '@/views/Login.vue'
import Home from '@/views/Home.vue'
// 设备管理
import Addservice from '@/pages/service/Addservice.vue'


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
			{path: 'addservice',name: 'addservice',component:Addservice}, //创建供应商
		]
	}
]

const router = new VueRouter({
	routes
})

export default router
