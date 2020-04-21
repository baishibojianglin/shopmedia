<script>
	import common from '@/common/common.js';
	import {mapMutations} from 'vuex';
	export default {
		components: {},
		// 全局变量
		globalData: {
			version: 1, // 应用大版本号
			did: 'sustock' + Math.random()*10 // 设备号
		},
		onLaunch: function(){
			let self=this;
			//设置header基本信息
			// 获取设备系统信息
			uni.getSystemInfo({
				success: function (res) {
					uni.setStorageSync('model',res.model); //手机型号
					uni.setStorageSync('apptype',res.platform);//客户端平台
				}
			});
			uni.setStorageSync('sign', common.sign());  // 验签，TODO：对参数如did、version进行AES加密，生成sign如：'6IpZZyb4DOmjTaPBGZtufjnSS4HScjAhL49NFjE6AJyVdsVtoHEoIXUsjrwu6m+o'
			uni.setStorageSync('version',self.globalData.version); //版本
			uni.setStorageSync('did',self.globalData.did); //设备号
			
			// 获取用户信息缓存（异步）
			uni.getStorage({
				key: 'userInfo',
				success:(res) => {
					self.login(res.data);
				}
			});
		},
		onShow: function() {
		},
		onHide: function() {
		},
		
		methods: {
			...mapMutations(['login'])
		}
	}
</script>

<style>
	/* 头条小程序需要把 iconfont 样式放到组件外 */
	@import "components/m-icon/m-icon.css";
	
	/* 每个页面公共css */
	@import './common/uni.css'; /* uni.css - 通用组件、模板样式库，可以当作一套ui库应用 */
	@import './common/base.css'; /* 导入基础样式 */
</style>
