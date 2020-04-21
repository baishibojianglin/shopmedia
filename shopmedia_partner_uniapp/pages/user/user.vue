<template>
	<view class="uni-padding-wrap">
		<view class="uni-common-mt" v-if="hasLogin">
			<uni-card is-full is-shadow>
				<view class="uni-flex uni-row userData">
					<view class="text uni-flex" style="width: 200rpx;height: 220rpx;-webkit-justify-content: center;justify-content: center;-webkit-align-items: center;align-items: center;">
						<image :src="userData.avatar" style="width: 150rpx;height: 150rpx;"></image>
					</view>
					<view class="uni-flex uni-column" style="-webkit-flex: 1;flex: 1;-webkit-justify-content: space-between;justify-content: space-between;">
						<view class="uni-flex uni-row" style="-webkit-justify-content: space-between;justify-content: space-between;">
							<view class="text uni-bold">{{userData.user_name}}</view>
							<!-- <view class="text" @click="goUserInfo()"><text class="uni-icon uni-icon-compose"></text></view> -->
						</view>
						<view class="uni-flex uni-row">
							<view class="text" style="-webkit-flex: 1;flex: 1;">{{userData.phone}}</view>
							<view class="text" style="-webkit-flex: 1;flex: 1;"><!-- （占位） --></view>
						</view>
					</view>
				</view>
			</uni-card>
		</view>
		
		<view class="uni-common-mt" v-if="hasLogin">
			<uni-card title="我的角色" thumbnail="" extra="" note="" is-full is-shadow>
				<button class="mini-btn" size="mini" v-for="(item, index) in userData.user_roles" :key="index" @click="toUserRoleDetails(index)">{{item}}</button>
			</uni-card>
		</view>
		
		<view class="uni-btn-v uni-common-mt">
			<button v-if="!hasLogin" type="primary" class="primary" @tap="bindLogin">登录</button><!-- hasLogin -->
			<button v-if="hasLogin" type="default" @tap="bindLogout">退出登录</button><!-- hasLogin -->
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
				userData: {}
			}
		},
		computed: {
			...mapState(['hasLogin', 'forcedLogin', 'userInfo', 'commonheader'])
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
				this.userData = {};
				this.logout(); // TODO：使用vuex管理登录状态时开启
				
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
						url: this.$serverUrl + 'api/user/' + this.userInfo.user_id,
						header: {
							'commonheader': this.$store.state.commonheader,
							'access-user-token': this.userInfo.token
						},
						method: 'GET',
						success:function(res){
							// 用户信息
							let userData = JSON.parse(Aes.decode(res.data.data));
							userData.avatar = userData.avatar ? self.$imgServerUrl + userData.avatar.replace(/\\/g, "/") : '../../static/img/userHL.png'; // 用户头像
							userData.role_ids = userData.role_ids.split(","); // 用户角色ID集合（字符串转数组）
							self.userData = userData;
						},
						fail(error) {
							console.log('getUserInfo失败：', error);
						}
					})
				}
			},
			
			/**
			 * 跳转用户角色详情页
			 */
			toUserRoleDetails(role_id) {
				// 业务员角色
				let salesman = ['4', '5', '6']
				if (salesman.indexOf(role_id) != -1) {
					uni.switchTab({
						url: '/pages/main/main'
					})
				} else {
					// 非业务员角色
					let url;
					switch (role_id){
						case '2':
							url = '/pages/user-partner/user-partner?user_id=' + this.userInfo.user_id + '&role_id=' + role_id;
							break;
						case '3':
							url = '/pages/user-shop/user-shop?user_id=' + this.userInfo.user_id + '&role_id=' + role_id;
							break;
						default:
							break;
					}
					uni.navigateTo({
						url: url
					})
				}
			}
		}
	}
</script>

<style>
	/* @import '../../common/uni-nvue.css'; */
	
	.userData .text {
		/* margin: 10upx 5upx; */
		padding: 0 2upx;
		/* background-color: #ebebeb; */
		height: 100upx;
		line-height: 100upx;
		text-align: center;
		color: #777;
		font-size: 30upx;
	}
	
	.mini-btn {
	    margin-right: 10rpx;
	}
</style>
