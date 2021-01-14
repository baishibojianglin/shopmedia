<script>
	import {
		mapMutations
	} from 'vuex';

	export default {
		// 全局变量
		globalData: {

		},
		onLaunch: function(event) {
			let self = this;

			// #ifdef H5
			/* 微信网页授权登录 s */
			let ua = window.navigator.userAgent.toLowerCase()
			if (ua.match(/MicroMessenger/i) == 'micromessenger') { // uniapp判断是否微信浏览器

				const base_url = 'http://media.dilinsat.com/h5/'; // 前端域名
				const wx_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx59483b145b8ede88&redirect_uri=' +
					base_url +
					'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect'; //请求微信code
				
				const code = this.getUrlParam('code');		// 获取URL 上code
				// 判断是否存在code
				if (code == null || code == '') {
					// 重新获取code
					// console.log(code)
					window.location.href = wx_url;
				} else {
					// 发送code           
					this.postCode(code);
				}
			}
			/* 微信网页授权登录 e */
			// #endif

			// 获取用户登录信息缓存（异步）
			uni.getStorage({
				key: 'userInfo',
				success: (res) => {
					self.login(res.data);
				}
			});
		},
		onShow: function() {

		},
		onHide: function() {

		},
		onError: function(event) {
			console.log('onError', event);
		},
		methods: {
			...mapMutations(['login']),

			// 解析URL 参数
			getUrlParam(name) {
				let reg = new RegExp('(^|&)' + name + '=([^&]*)(&|$)');
				let r = window.location.search.substr(1).match(reg);
				if (r != null) {
					return unescape(r[2]);
				}
				return null;
			},
			
			postCode(code) {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/thirdlogin', //发送code给后台。
					data: {
						code: code
					},
					header: {
						commonheader: this.commonheader
					},
					method: 'POST',
					success: (res) => {
						//res里面包含用户信息  openid等
						if (res.data.status == 1) {
							let userInfo = res.data.data;
			
							// 使用vuex管理登录状态时开启
							self.login(userInfo);
			
							//跳转到首页
							uni.reLaunch({
								url: '../main/main',
							});
						} else {
							uni.showToast({
								icon: 'none',
								title: res.data.message
							});
						}
					}
				});
			}
		}
	}
</script>

<style>
	/* 头条小程序需要把 iconfont 样式放到组件外 */
	@import "components/m-icon/m-icon.css";

	/* 每个页面公共css */
	@import './common/uni.css';
	/* uni.css - 通用组件、模板样式库，可以当作一套ui库应用 */
	@import './common/base.css';
	/* 导入基础样式 */

	/* tree树组件css */
	@import url("components/ly-tree/ly-tree.css");
</style>
