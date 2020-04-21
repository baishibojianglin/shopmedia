<template>
	<view class="uni-padding-wrap">
		<view class="contain-logo">
			<image class="logo" mode="aspectFit" :src="logourl"></image>
		</view>

		<view>
			<view class="input-line-height">
				<text class="input-line-height-1">手机</text>
				<input class="input-line-height-2" type="text" v-model="phone" placeholder="请输手机号" />
			</view>
			<view class="input-line-height">
				<text class="input-line-height-1">密码</text>
				<input class="input-line-height-2" type="password"  v-model="password" placeholder="请输入密码" />
			</view>
		</view>

		<view>
			<button class="login-button" @click="bindLogin">登 录</button>
		</view>

		<view class="bottom-con">
			<navigator url="../reg/reg">注册账号</navigator>
			<text class="bottom-con-1">|</text>
			<navigator url="../pwd/pwd">忘记密码</navigator>
		</view>
 
	</view>
</template>

<script>
	import common from '@/common/common.js';
	import {mapState, mapMutations} from 'vuex';

	export default {
		components: {},
		data() {
			return {
				phone: '18989898899',
				password: 'abc123',
				logourl: '/static/img/logo.png',
			}
		},
		computed: mapState(['forcedLogin','hasLogin','userInfo','commonheader']),
		onLoad(){
            console.log(this.$store.state)
		},
		methods: {
			//映射vuex的login方法
			...mapMutations(['login']),

			/**
			 * 登录
			 */
			bindLogin(){
				let self = this;
				//验证电话
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
						title: '手机号不正确'
					});
					return false;
				}
				// 密码
				if (!this.password.match(/^[a-zA-Z]\w{5,19}$/)) {   
					uni.showToast({
						icon: 'none',
						duration:2500,
						title: '字母开头，长度在6~20之间，只能包含字母、数字和下划线'
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
					header:{
						commonheader: self.$store.state.commonheader
					},
					method: 'PUT',
					success: function(res) {
						if (res.data.status == 1) {
							let userInfo = res.data.data;
							
							// 使用vuex管理登录状态时开启
							self.login(userInfo);
							
							//跳转到首页
							uni.reLaunch({
								url: '../main/main',
							});

						}
					}
				})
			}
		}
	}
</script>

<style>
	.contain-logo {
		margin-top: 50px;
		margin-bottom: 20px;
		text-align: center;
	}
	.logo {
		height: 140px;
	}
	.input-line-height{
		display: flex;
		align-items:center;
		line-height: 50px;
		border-bottom:1px solid #ECECEC; 
		font-size:16px;
	}
	.input-line-height-1{
		flex:1;
		padding-left: 5px;
	}
	.input-line-height-2{
		flex:3;
		font-size: 16px;
		text-align: left;
	}
	.login-button{
		background-color: #504AF2;
		color:#fff;
		margin-top: 20px;
	}
	.bottom-con{
		display: flex;
		margin-top: 10px;
		flex-direction: row;
		justify-content:center;
		font-size: 14px;
	}
	.bottom-con-1{
		padding: 0 8px;
	}

</style>
