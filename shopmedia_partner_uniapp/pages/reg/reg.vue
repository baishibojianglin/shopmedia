<template>
	<view class="content">
<!-- 		<view class="contain-logo">
			<image class="logo" :src="logourl"></image>
		</view>
		<view class="uni-center logotext">
			<text>商市通</text>
		</view> -->

		<view class="content-view">
				<view class="input-list">
					<text class="iconposition icon color-blue">&#xe7c6;</text>
					<input name="invitation_code" v-model="invitation_code" placeholder="输入邀请码" />
				</view>

				<view class="input-list">
					<text class="iconposition icon color-blue">&#xe7d5;</text>
					<input name="phone" type="number" v-model="phone" placeholder="输入手机号" />
				</view>

				<view class="input-list">
					<text class="iconposition icon color-blue">&#xe7b8;</text>
					<input name="password" :password='true' v-model="password" placeholder="请设置密码" />
				</view>

				<view class="input-list">
					<text class="iconposition icon color-blue">&#xe7d6;</text>
					<input name="verify_code" type="number" v-model="verify_code" placeholder="手机验证码" />
					<button class="bg-main-color color-white verify-button" v-if="!showseconds" @click="getVerifyCode()">获取验证码</button>
					<button v-if="showseconds"  class="bg-main-color color-white verify-button" >{{seconds}} S</button>
				</view>

				<view class="input-list" style="border-bottom: none;">
					<checkbox-group @change="seevalue">
						<label>
							<checkbox class="checkbox inline" value="1" color="#409EFF" checked="true" />
						</label>
						<navigator class="inline text" url="book">
							阅读并同意<text class="color-blue">《商市通用户协议》</text>
						</navigator>
					</checkbox-group>
				</view>

				<view>
					<button @click="submitdata()" class="submit bg-main-color color-white">注 册</button>
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
				invitation_code:'', // 邀请码
				phone: '', // 手机号
				password: '', // 密码
				verify_code:'', // 验证码
				return_code:'',
				value: 1, // 勾选协议状态
				logourl: '/static/img/logo.png',
				seconds:120 ,//倒计时秒数
				showseconds:false ,//显示倒计时
				timesign:'' //定时器标志
			}
		},
		computed: mapState(['forcedLogin','hasLogin','userInfo','commonheader']),
		mounted() {

		},
		beforeDestroy() {  //彻底清除定时器
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
			 * 监测是否勾选用户协议
			 * @param {Object} e
			 */
			seevalue(e) {
				this.value = e.detail.value.length;
			},

			/**
			 * 获取短信验证码
			 */
			getVerifyCode() {
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
                //控制倒计时显示
				this.showseconds=true;
				this.countseconds();
				
				let self = this;
				this.timesign=setInterval(function(){ self.countseconds(); },1000);
				uni.request({
					url: this.$serverUrl + 'api/send_sms',
					header:{
						commonheader:this.commonheader
					},
					data: {
						phone: this.phone
					},
					method: 'POST',
					success: function(res) {
						self.return_code = res.data
					}
				})
			},

			/**
			 * 提交数据
			 */
			submitdata() {
				// 检查邀请码
				if (this.invitation_code == '') {
					uni.showToast({
						icon: 'none',
						title: '请输入邀请码'
					});
					return false;
				}
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
				// 检查密码
				if (!this.password.match(/^[0-9A-Za-z]{6,20}$/)) {
					uni.showToast({
						icon:'none',
						title:'由6-20位数字或字母组成'
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
				// 勾选协议
				if (this.value == 0) {
					uni.showToast({
						icon: 'none',
						title: '请阅读并同意用户协议'
					});
					return false;
				}

				// 发起网络请求，提交服务端
				uni.request({
					url: this.$serverUrl + 'api/register',
					data: {
						invitation_code: this.invitation_code,
						phone: this.phone,
						password: this.password,
						verify_code: this.verify_code,//this.verify_code,
						return_code:this.verify_code// this.return_code
					},
					header:{
						commonheader:this.commonheader
					},
					method: 'POST',
					success: function(res) {
						if (0 == res.data.status) { // 验证失败
							uni.showToast({
								icon: 'none',
								title: res.data.message
							});
							return;
						} else { // 验证成功跳转
						   uni.showModal({
						       title: '提示',
						       content: '恭喜您注册成功，去登录',
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
		bottom:-4px;
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
		background-color: #409EFF;
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

	.book {
		text-align: center;
		margin-top: 15px;
	}

	.submit {
		background-color: #409EFF;
		margin-top: 30px;
	}
</style>
