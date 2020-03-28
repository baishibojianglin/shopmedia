<template>
	<view class="content">
		
		<view v-if="hasLogin" class="uni-flex uni-row">
			<view class="text uni-flex" style="width: 200rpx;height: 220rpx;-webkit-justify-content: center;justify-content: center;-webkit-align-items: center;align-items: center;">
				<image :src="userData.avatar" style="width: 150rpx;height: 150rpx;"></image>
			</view>
			<view class="uni-flex uni-column" style="-webkit-flex: 1;flex: 1;-webkit-justify-content: space-between;justify-content: space-between;">
				<view class="text" style="height: 120rpx;text-align: left;padding-left: 20rpx;padding-top: 10rpx;">
					{{userData.user_name}}
				</view>
				<view class="uni-flex uni-row">
					<view class="text" style="-webkit-flex: 1;flex: 1;">{{userData.phone}}</view>
					<view class="text" style="-webkit-flex: 1;flex: 1;">（占位）</view>
				</view>
			</view>
		</view>
		
		<view class="btn-row">
			<button v-if="!hasLogin" type="primary" class="primary" @tap="bindLogin">登录</button>
			<button v-if="hasLogin" type="default" @tap="bindLogout">退出登录</button>
		</view>
	</view>
</template>

<script>
	import {mapState, mapMutations} from 'vuex';
	import common from '@/common/common.js';
	import Aes from '@/common/Aes.js';

	export default {
		data() {
			return {
				userData: {},
			}
		},
		computed: {
			...mapState(['hasLogin', 'forcedLogin', 'userInfo'])
		},
		onShow() {
			this.getUserInfo(); // 获取用户信息
		},
		methods: {
			...mapMutations(['logout']),
			
			/**
			 * 登录
			 */
			bindLogin() {
				uni.navigateTo({
					url: '../login/login',
				});
			},
			
			/**
			 * 退出登录
			 */
			bindLogout() {
				this.logout();
				this.userData = {};
				/**
				 * 如果需要强制登录跳转回登录页面
				 */
				if (this.forcedLogin) {
					uni.reLaunch({
						url: '../login/login',
					});
				}
			},
			
			/**
			 * 获取用户信息
			 */
			getUserInfo() {
				let self = this
				if (this.hasLogin) {
					uni.request({
						url: this.$serverUrl + 'user/' + this.userInfo.user_id,
						header: {
							'sign': common.sign(), // 验签
							'version': getApp().globalData.version, // 应用大版本号
							'model': getApp().globalData.systemInfo.model, // 手机型号
							'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
							'did': getApp().globalData.did, // 设备号
							'access-user-token': this.userInfo.token
						},
						method: 'GET',
						success:function(res){
							// 用户信息
							let userData = JSON.parse(Aes.decode(res.data.data));
							userData.avatar = userData.avatar ? self.$imgServerUrl + userData.avatar.replace(/\\/g, "/") : '../../static/img/userHL.png'; // 用户头像
							self.userData = userData;
						},
						fail(error) {
							console.log('getUserInfo失败：', error);
						}
					})
				}
			}
		}
	}
</script>

<style>

</style>
