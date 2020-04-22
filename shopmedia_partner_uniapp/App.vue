<script>
	import {mapMutations} from 'vuex';
	
	export default {
		// 全局变量
		globalData: {
		},
		onLaunch: function(){
			let self=this;
			
			// 设置请求头 header 基本信息
			// 获取设备系统信息
			uni.getSystemInfo({
				success: function (res) {
					uni.setStorageSync('model',res.model); //手机型号
					uni.setStorageSync('apptype',res.platform);//客户端平台
				}
			});
			
			// 获取用户登录信息缓存（异步）
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
