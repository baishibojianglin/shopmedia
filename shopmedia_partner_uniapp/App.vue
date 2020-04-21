<script>
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
		},
		onShow: function() {
		},
		onHide: function() {
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
