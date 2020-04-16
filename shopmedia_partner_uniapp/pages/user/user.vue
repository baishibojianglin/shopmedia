<template>
	<view class="uni-padding-wrap">
		<view class="uni-card" v-if="login_info.has_login"><!-- hasLogin -->
			<view class="">
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
			</view>
		</view>
		
		<view class="uni-card">
			<view class="uni-common-mt">
				<uni-grid :column="3" :showBorder="false" :square="true">
					<uni-grid-item v-for="(item, index) in userData.user_roles" :key="index"><text class="uni-common-pl">{{item}}</text></uni-grid-item>
				</uni-grid>
			</view>
		</view>
		
		<view class="">
			<view class="uni-btn-v uni-common-mt">
				<button v-if="!login_info.has_login" type="primary" class="primary" @tap="bindLogin">登录</button><!-- hasLogin -->
				<button v-if="login_info.has_login" type="default" @tap="bindLogout">退出登录</button><!-- hasLogin -->
			</view>
		</view>
	</view>
</template>

<script>
	import {mapState, mapMutations} from 'vuex';
	import common from '@/common/common.js';
	import Aes from '@/common/Aes.js';
import uniGrid from "@/components/uni-grid/uni-grid.vue"
import uniGridItem from "@/components/uni-grid-item/uni-grid-item.vue"
	export default {
		components: {uniGrid,uniGridItem},
		data() {
			return {
				userData: {},
				login_info: {} // 判断是否登录（非vuex管理登录状态）
			}
		},
		computed: {
			...mapState(['hasLogin', 'forcedLogin', 'userInfo'])
		},
		onShow() {
			this.login_info = global.isLogin(); // 判断是否登录（非vuex管理登录状态）
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
				// this.logout(); // TODO：使用vuex管理登录状态时开启
				// 根据键名移除对应位置的缓存数据（非vuex管理登录状态）
				uni.removeStorage({
					key: 'login_info',
					success(res) {
						if (!global.isLogin()) {
							uni.reLaunch({
								url: '../login/login',
							});
						}
					}
				})
				
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
				if (this.login_info.has_login) { // this.hasLogin
					uni.request({
						url: this.$serverUrl + 'api/user/' + this.login_info.user_id, // this.userInfo.user_id
						header: {
							'sign': common.sign(), // 验签
							'version': getApp().globalData.version, // 应用大版本号
							'model': getApp().globalData.systemInfo.model, // 手机型号
							'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
							'did': getApp().globalData.did, // 设备号
							'access-user-token': this.login_info.user_info.token // this.userInfo.token
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
			}
		}
	}
</script>

<style>
	@import '../../common/uni-nvue.css';
	
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
</style>
