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

				// const base_url = 'http://media.dilinsat.com/h5/'; // 前端域名
				const base_url = 'https://media.sustock.net/h5_test/'; // 前端正式地址
				const wx_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx141d21edf5b7aa08&redirect_uri=' +
					base_url +
					'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect'; // 请求微信code
				// 获取URL 上code
				const code = this.getUrlParam('code');
				// 判断是否存在code
				if (code == null || code == '') {
					// 重新获取code
					// console.log(code)
					window.location.href = wx_url
				} else {
					// 发送code，第三方授权登录
					this.thirdLogin(code)
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
			/**
			 * 解析 URL 参数
			 * @param {Object} name
			 */
			getUrlParam(name) {
				let reg = new RegExp('(^|&)' + name + '=([^&]*)(&|$)');
				let r = window.location.search.substr(1).match(reg);
				if (r != null) {
					return unescape(r[2]);
				}
				return null;
			},
			
			/**
			 * 发送code，第三方授权登录
			 * @param {Object} code
			 */
			thirdLogin(code) {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/thirdlogin', // 发送code给后台，进行第三方授权登录
					data: {
						code: code
					},
					header: {
						commonheader: this.commonheader
					},
					method: 'POST',
					success: (res) => {
						// res里面包含用户信息 openid 等
						if (res.data.status == 1) {
							let userInfo = res.data.data;

							// 使用vuex管理登录状态时开启
							self.login(userInfo);

							// 跳转到首页
							uni.reLaunch({
								url: '../main/main',
							});
						} else {
							uni.showToast({
								icon: 'none',
								title: res.data.message,
								complete() {
									// 判断是否绑定手机号
									if (res.data.message == '请绑定手机号码') {
										uni.navigateTo({
											url: '/pages/login/bind-phone?oauth_info=' + encodeURIComponent(res.data.data.oauth_info)
										})
									}
								}
							});
						}
					}
				});
			},
			
			...mapMutations(['login'])
		}
	}
</script>

<style>
	/* 头条小程序需要把 iconfont 样式放到组件外 */
	@import "components/m-icon/m-icon.css";

	/* 每个页面公共css */
	/* uni.css - 通用组件、模板样式库，可以当作一套ui库应用 */
	@import './common/uni.css';
	/* 导入基础样式 */
	@import './common/base.css';

	/* tree树组件css */
	@import url("components/ly-tree/ly-tree.css");
</style>
