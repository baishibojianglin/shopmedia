<template>
	<view class="uni-page-body uni-padding-wrap">
		<view class="contain-logo">
			<image class="logo" :src="logourl"></image>
		</view>
		<view class="uni-center logotext">
			<text>店通传媒</text>
		</view>

		<view>
			<view class="input-line-height">
				<text class="input-line-height-1">手机</text>
				<input class="input-line-height-2" type="text" v-model="phone" placeholder="请输手机号" />
			</view>
			<view class="input-line-height">
				<text class="input-line-height-1">密码</text>
				<input class="input-line-height-2" type="password" v-model="password" placeholder="请输入密码" />
			</view>
		</view>

		<view>
			<button class="login-button bg-main-color" @click="bindLogin()">登 录</button>
		</view>

		<view class="uni-common-mt uni-center">
			<checkbox-group>
				<label>
					<checkbox value="psw" :checked="rememberPsw" @click="rememberPsw = !rememberPsw" color="#409EFF" />记住账号和密码</label>
			</checkbox-group>
		</view>

		<view class="bottom">
			<view class="bottom-con">
				<navigator url="../reg/reg">注册账号</navigator>
				<text class="bottom-con-1">|</text>
				<navigator url="../pwd/pwd">忘记密码</navigator>
			</view>
		</view>

	</view>
</template>

<script>
	import {
		mapState,
		mapMutations
	} from 'vuex';

	export default {
		components: {},
		data() {
			return {
				phone: '',
				password: '',
				logourl: '/static/img/logo.png',
				rememberPsw: true // 记住账号密码
			}
		},
		computed: mapState(['forcedLogin', 'hasLogin', 'userInfo', 'commonheader']),
		onLoad() {
			this.getRememberPassword();
			
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
		},
		methods: {

			/**
			 * 映射vuex的login方法
			 */
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
			},

			/**
			 * 登录
			 */
			bindLogin() {
				let self = this;
				//验证电话
				if (this.phone == '') {
					uni.showToast({
						icon: 'none',
						title: '请输入手机号码'
					});
					return false;
				}
				if (!this.phone.match(/^(13[0-9]|14[0-9]|15[0-9]|16[0-9]|17[0-9]|18[0-9]|19[0-9])\d{8}$/)) { /* 或 !(/^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|18[0|1|2|3|5|6|7|8|9])\d{8}$/).test(this.phone) */
					uni.showToast({
						icon: 'none',
						title: '手机号不正确'
					});
					return false;
				}
				// 密码
				if (!this.password.match(/^[0-9A-Za-z]{6,20}$/)) {
					uni.showToast({
						icon: 'none',
						duration: 2500,
						title: '由6-20位数字或字母组成'
					});
					return false;
				}
				//使用 uni.request 将账号信息发送至服务端，客户端在回调函数中获取结果信息。
				uni.request({
					url: this.$serverUrl + 'api/login',
					data: {
						phone: this.phone,
						password: this.password
					},
					header: {
						commonheader: this.commonheader
					},
					method: 'PUT',
					success: function(res) {
						if (res.data.status == 1) {
							let userInfo = res.data.data;

							// 使用vuex管理登录状态时开启
							self.login(userInfo);

							// 记住账号密码
							self.rememberPassword();

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
				})
			},

			/**
			 * 登录成功将用户名密码存储到用户本地
			 */
			rememberPassword() {
				if (this.rememberPsw) { // 用户勾选“记住账号密码”
					uni.setStorageSync('phone', this.phone);
					uni.setStorageSync('password', this.password);
				} else { //用户没有勾选“记住账号密码”
					uni.removeStorageSync('phone');
					uni.removeStorageSync('password');
					this.phone = '';
					this.password = '';
				}
			},

			/**
			 * 页面加载完成，获取本地存储的用户名及密码
			 */
			getRememberPassword() {
				let phone = uni.getStorageSync('phone');
				let password = uni.getStorageSync('password');
				if (phone && password) {
					this.phone = phone;
					this.password = password;
				} else {
					this.phone = '';
					this.password = '';
				}
			}
		}
	}
</script>

<style>
	.contain-logo {
		margin-top: 50px;
		text-align: center;
	}

	.logotext {
		font-size: 24px;
		margin-bottom: 20px;
	}

	.logo {
		height: 120px;
		width: 120px;
		border-radius: 20px;
	}

	.input-line-height {
		display: flex;
		align-items: center;
		line-height: 50px;
		border-bottom: 1px solid #ECECEC;
		font-size: 16px;
		position: relative;
	}

	.input-line-height-1 {
		position: absolute;
		left: 5px;
		padding: 15px 0 10px 0;
	}

	.input-line-height-2 {
		flex: 1;
		font-size: 16px;
		text-align: center;
		padding: 15px 0 10px 0;
	}

	.login-button {
		color: #fff;
		margin-top: 20px;
	}

	.bottom-con {
		display: flex;
		flex-direction: row;
		justify-content: center;
		font-size: 14px;
	}

	.bottom-con-1 {
		padding: 0 8px;
	}

	.bottom {
		position: fixed;
		bottom: 40px;
		left: 0;
		right: 0;
	}
</style>
