<template>
	<view class="uni-page-body uni-padding-wrap">
		<!-- <view class="contain-logo">
			<image class="logo" :src="logourl"></image>
		</view> -->
		<view class="uni-center uni-bold logotext">
			<text>商市通登录</text>
		</view>

		<view>
			<view class="input-line-height">
				<!-- <text class="input-line-height-1">电话号码</text> -->
				<text class="m-icon m-icon-phone"></text>
				<input class="input-line-height-2" type="text" v-model="phone" placeholder="请输入手机号" />
			</view>
			<view class="input-line-height" v-if="!is_verify_code">
				<!-- <text class="input-line-height-1">密码</text> -->
				<text class="m-icon m-icon-locked"></text>
				<input class="input-line-height-2" type="password" v-model="password" placeholder="请输入密码" />
			</view>
			
			<view class="input-line-height" v-if="is_verify_code">
				<!-- <text class="input-line-height-1">验证码</text> -->
				<text class="m-icon m-icon-locked"></text>
				<input class="input-line-height-2" name="verify_code" type="number" v-model="verify_code" placeholder="请输入验证码" />
				<button v-if="!showseconds" @click="getVerifyCode()" plain="true" class="verify-button">获取验证码</button>
				<button v-if="showseconds" plain="true" class="verify-button">剩余{{seconds}}s</button>
			</view>
		</view>

		<view>
			<button class="login-button primary" @click="bindLogin()">登 录</button>
			<view class="uni-common-mt" v-if="false">
				<view class="uni-flex uni-row" :style="!is_verify_code ? '-webkit-justify-content: space-between;justify-content: space-between;' : '-webkit-justify-content: flex-end;justify-content: flex-end;'">
					<view class="" v-if="!is_verify_code">
						<checkbox-group>
							<label><checkbox value="psw" :checked="rememberPsw" @click="rememberPsw = !rememberPsw" color="#4C85FC" />记住密码 <navigator url="../pwd/pwd" class="inline" style="margin-left: 1rem;">忘记密码？</navigator></label>
						</checkbox-group>
					</view>
					<view class="">
						<text class="text-right main-color" @click="is_verify_code = !is_verify_code">{{is_verify_code == true ? '密码登录' : '短信登录' }}</text>
					</view>
				</view>
			</view>
		</view>

		<view class="bottom">
			<!-- <view class="bottom-con">
				<navigator url="../reg/reg">注册账号</navigator>
				<text class="bottom-con-1">|</text>
				<navigator url="../pwd/pwd">忘记密码</navigator>
			</view> -->
			<view class="uni-common-mt uni-center">
				<checkbox-group @change="signAgreement">
					<label>
						<checkbox class="checkbox inline" value="1" color="#4C85FC" :checked="signed_agreement" />
					</label>
					<navigator class="inline text" url="/pages/login/user-agreement">
						阅读并同意<text class="color-blue">《商市通用户及隐私协议》</text>
					</navigator>
				</checkbox-group>
			</view>
		</view>

		<!-- 对话框 s -->
		<uni-popup id="popupDialog" ref="popupDialog" type="dialog">
			<uni-popup-dialog type="info" title="用户及隐私协议" content="请你仔细阅读《商市通用户及隐私协议》，如果你同意协议内容，请点击“确定”开始接受我们的服务。" :before-close="true" @confirm="dialogConfirm" @close="dialogClose"></uni-popup-dialog>
		</uni-popup>
		<!-- 对话框 e -->
	</view>
</template>

<script>
	import Aes from '@/common/Aes.js';
	import uniPopup from '@/components/uni-popup/uni-popup.vue'
	import uniPopupMessage from '@/components/uni-popup/uni-popup-message.vue'
	import uniPopupDialog from '@/components/uni-popup/uni-popup-dialog.vue'
	
	import {
		mapState,
		mapMutations
	} from 'vuex';

	export default {
		components: {
			uniPopup,
			uniPopupMessage,
			uniPopupDialog
		},
		data() {
			return {
				phone: '',
				password: '',
				is_verify_code: true, // 是否短信登录
				showseconds: false, // 显示倒计时
				seconds: 120, // 倒计时秒数
				verify_code: '', // 短信验证码
				return_code: '',
				invitation_code: '288888', // 邀请码
				logourl: '/static/img/logo.png',
				signed_agreement: true, // 勾选协议状态
				rememberPsw: true // 记住账号密码
			}
		},
		computed: mapState(['forcedLogin', 'hasLogin', 'userInfo', 'commonheader']),
		onReady() {
			// 页面打开自动打开对话框
			setTimeout(() => {
				// this.msgType = 'success'
				this.openDialog();
			}, 500)
		},
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
					// 发送code，第三方授权登录
					this.thirdLogin(code)
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
						// res里面包含用户信息 openid等
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
				
				// 验证手机号
				if (this.phone == '') {
					uni.showToast({
						icon: 'none',
						title: '请输入手机号'
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
				
				if (this.is_verify_code == true) { // 短信登录，验证短信验证码
					if (this.verify_code == '') {
						uni.showToast({
							icon: 'none',
							title: '请输入短信验证码'
						});
						return false;
					}
				} else { // 密码登录，验证密码
					if (!this.password.match(/^[0-9A-Za-z]{6,20}$/)) {
						uni.showToast({
							icon: 'none',
							duration: 2500,
							title: '密码由6~20位数字或字母组成'
						});
						return false;
					}
				}
				
				// 勾选协议
				if (this.signed_agreement == 0) {
					uni.showToast({
						icon: 'none',
						title: '请阅读并同意用户及隐私协议'
					});
					return false;
				}
				
				// 请求的参数
				let data = {
					phone: this.phone,
					signed_agreement: this.signed_agreement,
					invitation_code: this.invitation_code
				}
				if (this.is_verify_code == true) { // 短信登录
					data.verify_code = this.verify_code;
					data.return_code = this.return_code = '222';
				} else { // 密码登录
					data.password = this.password;
				}
				
				//使用 uni.request 将账号信息发送至服务端，客户端在回调函数中获取结果信息。
				uni.request({
					url: this.$serverUrl + 'api/login',
					data: {data: Aes.encode(JSON.stringify(data))},
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
			 * 倒计时函数
			 */
			countseconds() {
				if(this.seconds > 0){
					this.seconds--;			   
				}else{
					this.showseconds = false;
					clearInterval(this.timesign);
					this.seconds = 120;
				}
			},
			
			/**
			 * 获取短信验证码
			 */
			async getVerifyCode() {
				// 检查手机号码
				if (this.phone == '') {
					uni.showToast({
						icon: 'none',
						title: '请输入手机号码'
					});
					return false;
				}
				if (!(/^1(3|4|5|6|7|8|9)\d{9}$/.test(this.phone))) {
					uni.showToast({
						icon: 'none',
						title: '请输入正确的手机号码'
					});
					return false;
				}
				
				// 勾选协议
				if (this.signed_agreement == 0) {
					uni.showToast({
						icon: 'none',
						title: '请阅读并同意用户及隐私协议'
					});
					return false;
				}
				
				// 控制倒计时显示
				this.showseconds = true;
				this.countseconds();
				
				let self = this;
				this.timesign=setInterval(function(){ self.countseconds(); },1000);
				uni.request({
					url: this.$serverUrl + 'api/send_sms',
					data: {
						phone: this.phone
					},
					header:{
						commonheader: this.commonheader
					},
					method: 'POST',
					success: function(res) {
						self.return_code = res.data
					}
				})
			},
			
			/**
			 * 监测是否勾选用户协议
			 * @param {Object} e
			 */
			signAgreement(e) {
				if (e.detail.value.length == 1) {
					this.signed_agreement = true;
				} else {
					this.signed_agreement = false;
				}
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
			},
			
			/**
			 * 用户协议提示框
			 */
			openDialog(){
				this.$refs.popupDialog.open()
			},
			
			/**
			 * 对话框点击确认按钮
			 */
			dialogConfirm(done) {
				this.signed_agreement = true;
				// 需要执行 done 才能关闭对话框
				done()
			},
			
			/**
			 * 对话框取消按钮
			 */
			dialogClose(done) {
				this.signed_agreement = false;
				// 需要执行 done 才能关闭对话框
				done()
			}
		}
	}
</script>

<style>
	.uni-padding-wrap {
		width: 78.4%;
		padding: 0 10.8%;
	}
	
	.contain-logo {
		margin-top: 50px;
		text-align: center;
	}

	.logotext {
		font-size: 28px;
		margin-top: 89.5px;
		margin-bottom: 57.5px;
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
		font-size: 15px;
		position: relative;
	}

	.input-line-height-1 {
		position: absolute;
		left: 5px;
		padding: 20px 0 15px 0;
	}

	.input-line-height-2 {
		flex: 1;
		font-size: 15px;
		/* text-align: center; */
		padding: 20px 0 15px 15px;
	}

	.login-button {
		color: #FEFEFE;
		margin-top: 48.5px;
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
	
	
	/* 短信验证码 */
	.verify-button {
		/* position: absolute;
		right: 0;
		bottom: 3px; */
		font-size: 17px;
		width: 39%;
		border: 0;
	}
</style>
