<template>
	<view class="content">
		<!-- <view class="contain-logo">
			<image class="logo" :src="logourl"></image>
		</view>
		<view class="uni-center logotext">
			<text>商市通</text>
		</view> -->

		<view class="content-view">
			<view class="input-list">
				<text class="iconposition icon color-blue">&#xe7d5;</text>
				<input name="phone" type="number" v-model="phone" placeholder="输入手机号" />
			</view>

			<view class="input-list">
				<text class="iconposition icon color-blue uni-icon">&#xe7d6;</text>
				<input name="verify_code" type="number" v-model="verify_code" placeholder="手机验证码" />
				<button v-if="!showseconds" @click="getVerifyCode()" class="bg-main-color color-white verify-button">获取验证码</button>
				<button v-if="showseconds" class="bg-main-color color-white verify-button" >{{seconds}} S</button>
			</view>

			<view>
				<button @click="bindPhone()"  class="bg-main-color color-white submit">确认绑定</button>
			</view>
		</view>
	</view>
</template>

<script>
	import {mapState, mapMutations} from 'vuex';
	export default {
		components: {},
		data() {
			return {
				phone: '', // 手机号
				verify_code: '', // 验证码
				return_code: '',
				logourl: '/static/img/logo.png',
				seconds: 120 , // 倒计时秒数
				showseconds: false , // 显示倒计时
				timesign: '', // 定时器标志
				oauth_info: '' // 三方授权用户信息
			}
		},
		computed: mapState(['forcedLogin','hasLogin','userInfo','commonheader']),
		mounted() {

		},
		onLoad(option) {
			this.oauth_info = option.oauth_info;
		},
		beforeDestroy() {
			// 彻底清除定时器
		    if(this.timesign) {
		        clearInterval(this.timesign);
		    }
		},
		methods: {
			/**
			 * 倒计时函数
			 */
			countseconds() {
			       if(this.seconds>0){
					   this.seconds--;			   
				   }else{
					   this.showseconds=false;
					   clearInterval(this.timesign);
					   this.seconds=120;
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
						title: '请输入手机号'
					});
					return false;
				}
				if (!(/^1(3|4|5|6|7|8|9)\d{9}$/.test(this.phone))) {
					uni.showToast({
						icon: 'none',
						title: '手机号错误'
					});
					return false;
				}
				
                // 控制倒计时显示
				this.showseconds=true;
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
			 * 绑定手机号提交表单
			 */
			bindPhone() {
				// 检查手机号码
				if (this.phone == '') {
					uni.showToast({
						icon: 'none',
						title: '请输入手机号'
					});
					return false;
				}
				if (!(/^1(3|4|5|6|7|8|9)\d{9}$/.test(this.phone))) {
					uni.showToast({
						icon: 'none',
						title: '手机号错误'
					});
					return false;
				}
				
				// 验证码
				if (this.verify_code == '') {
					uni.showToast({
						icon: 'none',
						title: '请输入验证码'
					});
					return false;
				}

				// 发起网络请求，提交服务端
				uni.request({
					url: this.$serverUrl + 'api/bind_phone',
					data: {
						phone: this.phone,
						verify_code: this.verify_code,
						return_code: this.return_code,
						oauth_info: this.oauth_info
					},
					header:{
						commonheader: this.commonheader
					},
					method: 'POST',
					success: function(res) {
						if (0 == res.data.status) { // 验证失败
							uni.showToast({
								icon: 'none',
								title: res.data.message
							});
							return false;
						} else { // 验证成功跳转
						   uni.showModal({
						       title: '提示',
						       content: '密码重置成功，去登陆',
							   showCancel:false,
						       success: function (res) {
						           if (res.confirm) {
						               uni.navigateTo({
						               	url: '../login/login'
						               });
						           }
						       }
						   })
						}
					},
					fail: function(error) {
						
					}
				})
			}
		}
	}
</script>

<style>
	.contain-logo {
		margin-top: 20px;
		text-align: center;
	}
	.logotext{
		font-size: 30px;
		margin-bottom: 0px;
	}
	.logo {
		height: 120px;
		width: 120px;
		border-radius: 20px;
	}

	.content-view {
		width: 90%;
		margin: 50px auto;
		text-align: center;
	}

	.iconposition {
		position: absolute;
		left: 20px;
		bottom: -4px;
	}

	.input-list {
		position: relative;
		border-bottom: 1px solid #F1F1F1;
		padding-bottom: 5px;
		margin-bottom: 30px;
	}

	.verify-button {
		position: absolute;
		right: 0;
		bottom: 3px;
		font-size: 13px;
		width: 100px;
	}

	.contain-checkbox {
		margin-top: 20px;
	}

	.checkbox {
		font-size: 10rpx;
	}

	.text {
		position: relative;
		top: 3px;
	}

	.submit {
		margin-top: 35px;
	}
</style>
