import Vue from 'vue'
import Vuex from 'vuex'
import common from '@/common/common.js';

Vue.use(Vuex)

const store = new Vuex.Store({
	state: {
		forcedLogin: false, // 是否需要强制登录
		hasLogin: false, // 是否登录
		userInfo: {},// 存放用户信息
		commonheader: JSON.stringify({
			'sign':common.sign(),
			'version':1,
			'model':uni.getSystemInfoSync().model,
			'apptype':uni.getSystemInfoSync().platform,
			'did':'sustock2020'
		})
	},
	mutations: {
		/**
		 * 登录
		 * @param {Object} state
		 * @param {Object} userInfo
		 */
		login(state, userInfo) {
			state.hasLogin = true;
			state.userInfo = userInfo; // 将请求中的如res.data.data对象存入userInfo		
			// 将用户信息保存到本地缓存
			uni.setStorageSync('userInfo', userInfo);
		},
		
		/**
		 * 退出登录
		 * @param {Object} state
		 */
		logout(state) {
			state.hasLogin = false;
			state.userInfo = {};
			
			// 根据键名移除对应位置的缓存数据
			uni.removeStorageSync('userInfo');
		}
	}
})

export default store
