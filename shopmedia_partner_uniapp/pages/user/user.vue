<template>
	<view class="uni-padding-wrap">
		<view class="uni-common-mt" v-if="hasLogin">
			<uni-card is-full is-shadow>
				<view class="uni-flex uni-row userData">
					<view class="text uni-flex" style="width: 200rpx;height: 220rpx;-webkit-justify-content: center;justify-content: center;-webkit-align-items: center;align-items: center;">
						<!-- <image :src="userData.avatar" style="width: 150rpx;height: 150rpx;"></image> -->
						<image class="headimg"  mode="aspectFit" :src="userData.avatar != '' ? userData.avatar : logourl"></image>
					</view>
					<view class="uni-flex uni-column">
						<view class="uni-flex uni-row">
							<!-- <view class="text uni-bold">{{userData.user_name}}</view> -->
							<!-- <view class="text" @click="goUserInfo()"><text class="uni-icon uni-icon-compose"></text></view> -->
						</view>
						<view class="uni-flex uni-row">
							<view class="text">账号：{{userData.user_name}}</view>
						</view>
						<view class="uni-flex uni-row">
							<view class="text">注册：{{userData.create_time}}</view>
						</view>
					</view>
				</view>
			</uni-card>
		</view>
		
		<view class="uni-common-mt" v-if="hasLogin">
			<uni-card title="我的角色" thumbnail="" extra="" note="" is-full is-shadow>
				<button v-for="(item, index) in userData.user_roles" :key="index" v-if="item.role_id != 7" class="mini-btn" :class="item.user_role.status != 1 ? 'color-disable' : ''" size="mini" @click="toUserRoleDetails(item)">{{item.role_title}}</button>
			</uni-card>
		</view>
		
		<view class="uni-common-mt" v-if="hasLogin">
			<uni-card title="我的奖品" is-full :isShadow="true">
				<uni-grid class="uni-center" :column="2" :showBorder="false" :square="false">
					<uni-grid-item>
						<navigator url="/pages/user/user-prize-list?prize_status=0">
							<view class="uni-text-small">未领取</view>
							<view class="uni-bold color-red">{{userPrizeCount.userPrizeCount0}}</view>
						</navigator>
					</uni-grid-item>
					<uni-grid-item>
						<navigator url="/pages/user/user-prize-list?prize_status=1">
							<view class="uni-text-small">已领取</view>
							<view class="uni-bold">{{userPrizeCount.userPrizeCount1}}</view>
						</navigator>
					</uni-grid-item>
				</uni-grid>
			</uni-card>
		</view>
		
		<view class="uni-common-mt" v-if="hasLogin">
			<uni-card title="我的积分" is-full :isShadow="true">
				<uni-grid class="uni-center" :column="3" :showBorder="false" :square="false">
					<uni-grid-item>
						<text class="uni-text-small">获得</text>
						<text class="uni-bold">{{userData.get_integrals}}</text>
					</uni-grid-item>
					<uni-grid-item>
						<text class="uni-text-small">已兑换</text>
						<text class="uni-bold">{{userData.used_integrals}}</text>
					</uni-grid-item>
					<uni-grid-item>
						<text class="uni-text-small">剩余</text>
						<text class="uni-bold color-red">{{userData.rest_integrals}}</text>
					</uni-grid-item>
				</uni-grid>
			</uni-card>
		</view>
		
		<view class="uni-btn-v uni-common-mt">
			<button v-if="!hasLogin" type="primary" class="primary" @tap="bindLogin">登 录</button><!-- hasLogin -->
			<button v-if="hasLogin" type="default" @tap="bindLogout">退出登录</button><!-- hasLogin -->
		</view>
	</view>
</template>

<script>
	import {mapState, mapMutations} from 'vuex';
	import Aes from '@/common/Aes.js';
	export default {
		data() {
			return {
				userData: {},
				logourl: '/static/img/logoheadimg.png',
				userPrizeCount: [] // 统计用户获得的奖品数量
			}
		},
		computed: {
			...mapState(['hasLogin', 'forcedLogin', 'userInfo', 'commonheader'])
		},
		onShow() {
			this.getUserInfo(); // 获取用户信息
			this.getUserPrizeCount();
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
				let self = this;
				// 请求接口
				uni.request({
					url: this.$serverUrl + 'api/logout',
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'PUT',
					success: function(res){
						if (res.statusCode == 201) { // 退出登录成功
							// 清空登录状态及登录用户数据
							self.userData = {};
							self.logout(); // TODO：使用vuex管理登录状态时开启
							
							// 如果需要强制登录跳转回登录页面
							if (self.forcedLogin) {
								uni.reLaunch({
									url: '../login/login',
								});
							}
						} else {
							uni.showToast({
								icon: 'none',
								title: res.data.message
							})
						}
					},
					fail(error) {
						uni.showToast({
							icon: 'none',
							title: '请求异常'
						});
					}
				})
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
							'commonheader': this.commonheader,
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
							uni.showToast({
								icon: 'none',
								title: '请求异常'
							});
						}
					})
				}
			},
			
			/**
			 * 跳转用户角色详情页
			 */
			toUserRoleDetails(item) {
				// 判断用户角色状态
				if (item.user_role.status != 1) {
					uni.showToast({
						icon: 'none',
						title: '角色状态异常'
					});
					return;
				}
				let role_id = Number(item.role_id);
				// 将用户角色 role_id 存储在本地缓存中（同步）
				try {
					uni.setStorageSync('role_id', role_id);
				} catch (e) {
					// error
				}
				
				// 业务员角色
				let salesman = [4, 5, 6]
				if (salesman.indexOf(role_id) != -1) {
					uni.switchTab({
						url: '/pages/main/main'
					})
				} else {
					// 非业务员角色
					let url = '';
					switch (role_id){
						case 2:
							url = '/pages/user-partner/user-partner?user_id=' + this.userInfo.user_id + '&role_id=' + role_id;
							break;
						case 3:
							url = '/pages/user-shopkeeper/user-shopkeeper?user_id=' + this.userInfo.user_id + '&role_id=' + role_id;
							break;
						default:
							break;
					}
					uni.navigateTo({
						url: url
					})
				}
			},
			
			/**
			 * 统计用户获得的奖品数量
			 */
			getUserPrizeCount() {
				let self=this;
				uni.request({
					url: this.$serverUrl + 'api/user_prize_count',
					data: {
						user_id: this.userInfo.user_id,
						phone: this.userInfo.phone
					},
					method: 'GET',
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					success: (res) => {
						if (res.data.status == 1) {
							self.userPrizeCount = res.data.data;
						}
					}
				});	
			}
		}
	}
</script>

<style lang="scss">
	/* @import '../../common/uni-nvue.css'; */
	.headimg{
		width: 60px;
		height: 60px;
		border-radius: 60px;
		background-color: #F2F2F2;
		padding: 10px;
		margin-top:5px;
	}
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
