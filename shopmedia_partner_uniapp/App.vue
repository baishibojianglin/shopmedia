<script>
    import common from '@/common/common.js';
	export default {
		components: {},
		// 全局变量
		globalData: {
			systemInfo: {}, // 设备系统信息
			version: 1, // 应用大版本号
			did: '12345dg', // 设备号
			commonHeaders: {} // 公用请求头
		},
		onLaunch: function() {		
			let self = this;
			// 获取设备系统信息
			uni.getSystemInfo({
				success: function (res) {
					self.globalData.systemInfo = res; // getApp().globalData.systemInfo = res
				}
			});
			
			// 公用请求头配置
			this.globalData.commonHeaders = {
				'content-type': "application/json; charset=utf-8",
				'sign': common.sign(), // 验签，TODO：对参数如did等进行AES加密，生成sign如：'6IpZZyb4DOmjTaPBGZtufjnSS4HScjAhL49NFjE6AJyVdsVtoHEoIXUsjrwu6m+o'
				'version': getApp().globalData.version, // 应用大版本号
				'model': getApp().globalData.systemInfo.model, // 手机型号
				'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
				'did': getApp().globalData.did ,// 设备号
				'access-user-token': global.isLogin().user_info.token
			}
		},
		onShow: function() {
			//console.log('App Show');
		},
		onHide: function() {
			//console.log('App Hide');
		}
	}
	
	/**
	 * 判断是否登录（非vuex管理登录状态）
	 */
	global.isLogin = function() {
		try{
			var login_info = uni.getStorageSync('login_info');
		}catch(e){
			//TODO handle the exception
			console.log(e)
		}
		if(login_info == ''){
			return false;
		}else{
			return login_info;
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
