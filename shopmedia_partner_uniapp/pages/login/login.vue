<template>
	<view class="content">
		<u-row style="margin-bottom: 20px;">
			<u-col span="24" class="contain-logo">
				<image class="logo" mode="aspectFit" :src="logourl"></image>
			</u-col>
		</u-row>
		
		<view class="input-group">
			<view class="input-row border">
				<text class="title">手机号</text>
				<m-input class="m-input" type="text" clearable focus v-model="phone" placeholder="请输入手机号"></m-input>
			</view>
			<view class="input-row">
				<text class="title">密码</text>
				<m-input type="password" displayable v-model="password" placeholder="请输入密码"></m-input>
			</view>
		</view>
		
		<view class="btn-row">
			<button type="primary" style="background-color: #3F44F3;" @tap="bindLogin">登录</button>
		</view>
		
		<view class="action-row" style="margin-top: 10px;">
			<navigator style="color: #000;" url="../reg/reg">注册账号</navigator>
			<text>|</text>
			<navigator style="color: #000;" url="../pwd/pwd">忘记密码</navigator>
		</view>
		
		<view class="oauth-row" v-if="hasProvider" v-bind:style="{top: positionTop + 'px'}">
			<view class="oauth-image" v-for="provider in providerList" :key="provider.value">
				<image :src="provider.image" @tap="oauth(provider.value)"></image>
				<!-- #ifdef MP-WEIXIN -->
				<button v-if="!isDevtools" open-type="getUserInfo" @getuserinfo="getUserInfo"></button>
				<!-- #endif -->
			</view>
		</view>
	</view>
</template>

<script>
	import Vue from 'vue'
	import Row from '@/components/dl-grid/row.vue'
	import Col from '@/components/dl-grid/col.vue'
	Vue.component('u-row', Row); //<row>和<col>为H5原生标签, 不能直接用, 可起名<u-row>或者其他的
	Vue.component('u-col', Col);
	import {
		mapState,
		mapMutations
	} from 'vuex';
	import common from '@/common/common.js';
	import mInput from '../../components/m-input.vue';

	export default {
		components: {
			mInput
		},
		data() {
			return {
				providerList: [],
				hasProvider: false,
				phone: '18989898899',
				password: 'abc123',
				positionTop: 0,
				isDevtools: false,
				logourl: '/static/img/logo.png',
			}
		},
		computed: mapState(['forcedLogin']),
		methods: {
			...mapMutations(['login']),

			initProvider() {
				const filters = ['weixin', 'qq', 'sinaweibo'];
				uni.getProvider({
					service: 'oauth',
					success: (res) => {
						if (res.provider && res.provider.length) {
							for (let i = 0; i < res.provider.length; i++) {
								if (~filters.indexOf(res.provider[i])) {
									this.providerList.push({
										value: res.provider[i],
										image: '../../static/img/' + res.provider[i] + '.png'
									});
								}
							}
							this.hasProvider = true;
						}
					},
					fail: (err) => {
						console.error('获取服务供应商失败：' + JSON.stringify(err));
					}
				});
			},

			initPosition() {
				/**
				 * 使用 absolute 定位，并且设置 bottom 值进行定位。软键盘弹出时，底部会因为窗口变化而被顶上来。
				 * 反向使用 top 进行定位，可以避免此问题。
				 */
				this.positionTop = uni.getSystemInfoSync().windowHeight - 100;
			},

			/**
			 * 登录
			 */
			bindLogin() {
				let self = this;

				/**
				 * 客户端对账号信息进行一些必要的校验。
				 */
				// 手机号
				if (this.phone == '') {
					uni.showToast({
						icon: 'none',
						title: '请输入手机号码'
					});
					return false;
				}
				if (!this.phone.match(/^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|18[0|1|2|3|5|6|7|8|9])\d{8}$/)) { /* 或 !(/^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|18[0|1|2|3|5|6|7|8|9])\d{8}$/).test(this.phone) */
					uni.showToast({
						icon: 'none',
						title: '手机号不合法'
					});
					return;
				}
				// 密码
				if (!this.password.match(/^[a-zA-Z]\w{5,19}$/)) {
					uni.showToast({
						icon: 'none',
						title: '密码必须以字母开头，长度在6~20之间，只能包含字母、数字和下划线'
					});
					return;
				}
				let aa = {
					'content-type': "application/json; charset=utf-8",
					'sign': common.sign(), // 验签，TODO：对参数如did等进行AES加密，生成sign如：'6IpZZyb4DOmjTaPBGZtufjnSS4HScjAhL49NFjE6AJyVdsVtoHEoIXUsjrwu6m+o'
					'version': getApp().globalData.version, // 应用大版本号
					'model': getApp().globalData.systemInfo.model, // 手机型号
					'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
					'did': getApp().globalData.did, // 设备号
				};
				console.log('commonHeaders：', getApp().globalData.commonHeaders);
				console.log('commonHeadersaa：', aa);
				/**
				 * 使用 uni.request 将账号信息发送至服务端，客户端在回调函数中获取结果信息。
				 */
				uni.request({
					url: this.$serverUrl + 'api/login',
					data: {
						phone: this.phone,
						password: this.password
					},
					header: /* getApp().globalData.commonHeaders, */ {
						'content-type': "application/json; charset=utf-8",
						'sign': common.sign(), // 验签，TODO：对参数如did等进行AES加密，生成sign如：'6IpZZyb4DOmjTaPBGZtufjnSS4HScjAhL49NFjE6AJyVdsVtoHEoIXUsjrwu6m+o'
						'version': getApp().globalData.version, // 应用大版本号
						'model': getApp().globalData.systemInfo.model, // 手机型号
						'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
						'did': getApp().globalData.did, // 设备号
					},
					method: 'PUT',
					success: function(res) {
						console.log('login success', res);
						if (1 == res.data.status) {
							let userInfo = res.data.data;
							self.login(userInfo);
							// self.toMain(userInfo); // 跳转到首页
							uni.reLaunch({
								url: '../main/main',
							});
						} else {
							uni.showToast({
								icon: 'none',
								title: res.data.message, // '用户账号或密码不正确'
							});
						}
					},
					fail(error) {
						console.log('bindLogin失败：', error);
					}
				})
			},
			oauth(value) {
				uni.login({
					provider: value,
					success: (res) => {
						uni.getUserInfo({
							provider: value,
							success: (infoRes) => {
								/**
								 * 实际开发中，获取用户信息后，需要将信息上报至服务端。
								 * 服务端可以用 userInfo.openId 作为用户的唯一标识新增或绑定用户信息。
								 */
								this.toMain(infoRes.userInfo.nickName);
							},
							fail() {
								uni.showToast({
									icon: 'none',
									title: '登陆失败'
								});
							}
						});
					},
					fail: (err) => {
						console.error('授权登录失败：' + JSON.stringify(err));
					}
				});
			},
			getUserInfo({
				detail
			}) {
				if (detail.userInfo) {
					this.toMain(detail.userInfo.nickName);
				} else {
					uni.showToast({
						icon: 'none',
						title: '登陆失败'
					});
				}
			},

			/**
			 * 跳转到首页
			 * @param {Object} userInfo
			 */
			toMain(userInfo) {
				this.login(userInfo);
				/**
				 * 强制登录时使用reLaunch方式跳转过来
				 * 返回首页也使用reLaunch方式
				 */
				if (this.forcedLogin) {
					uni.reLaunch({
						url: '../main/main',
					});
				} else {
					uni.navigateBack();
				}
			}
		},
		onReady() {
			this.initPosition();
			this.initProvider();
			// #ifdef MP-WEIXIN
			this.isDevtools = uni.getSystemInfoSync().platform === 'devtools';
			// #endif
		}
	}
</script>

<style>
	.contain-logo {
		margin-top: 30px;
		text-align: center;
	}

	.logo {
		height: 130px;
	}

	.logo-text {
		font-size: 20px;
		font-weight: bold;
	}

	.action-row {
		display: flex;
		flex-direction: row;
		justify-content: center;
	}

	.action-row navigator {
		color: #007aff;
		padding: 0 10px;
	}

	.oauth-row {
		display: flex;
		flex-direction: row;
		justify-content: center;
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
	}

	.oauth-image {
		position: relative;
		width: 50px;
		height: 50px;
		border: 1px solid #dddddd;
		border-radius: 50px;
		margin: 0 20px;
		background-color: #ffffff;
	}

	.oauth-image image {
		width: 30px;
		height: 30px;
		margin: 10px;
	}

	.oauth-image button {
		position: absolute;
		left: 0;
		top: 0;
		width: 100%;
		height: 100%;
		opacity: 0;
	}
</style>
