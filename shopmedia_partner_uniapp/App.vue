<script>
	import {mapMutations} from 'vuex';
	
	export default {
		// 全局变量
		globalData: {
			
		},
		onLaunch: function(event){
			let self = this;
			
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
		onError:function(event){
			console.log('onError', event)
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
	
	/* tree树组件css */
	@import url("components/ly-tree/ly-tree.css");
</style>
