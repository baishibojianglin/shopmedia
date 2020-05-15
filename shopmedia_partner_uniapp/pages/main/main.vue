<template>
	<view class="content">
		<view>
			<video class="vedio-con" src="https://sustock-app-test.oss-cn-chengdu.aliyuncs.com/company.mp4" :autoplay="true" :loop="false" :controls="true"></video>
		</view>

		<view>
			<uni-grid class="view-grid-con" :column="3">
				<uni-grid-item>
					<text class="text-grid-title">广告屏</text>
					<text class="text-grid">20000+</text>
				</uni-grid-item>
				<uni-grid-item>
					<text class="text-grid-title">覆盖城市</text>
					<text class="text-grid">3</text>
				</uni-grid-item>
				<uni-grid-item>
					<text class="text-grid-title">服务商家</text>
					<text class="text-grid">5000+</text>
				</uni-grid-item>
			</uni-grid>
		</view>

		<view>
			<text class="user-title"> <text class="color-blue">—</text> 店通服务 <text class="color-blue">—</text></text>
		</view>

		<view class="navcon">
			<view class="navcon-item" @click="usead()">
				<text class="iconposition icon color-blue iconbg">&#xe636;</text>
				<br />
				<text>广告投放咨询</text>
			</view>
			<view @click="toRole(2)" class="navcon-item">
				<text class="iconposition icon color-red iconbg">&#xe637;</text>
				<br />
				<text>合作经营</text>
			</view>
			<view @click="toRole(3)" class="navcon-item">
				<text class="iconposition icon iconbg" style="color:#1AA034;">&#xe61b;</text>
				<br />
				<text>店铺合作</text>
			</view>
			<view @click="toRole(1)" class="navcon-item">
				<text class="iconposition icon color-blue iconbg" style="color:#205C6D;">&#xe63d;</text>
				<br />
				<text>业务申请</text>
			</view>
			<view class="navcon-item">
				<navigator url="/pages/news/news">
					<text class="iconposition icon color-blue iconbg" style="color:#F7D810;">&#xe652;</text>
					<br />
					<text>店通资讯</text>
				</navigator>
			</view>
			<view class="navcon-item">
				<navigator url="/pages/feedback/feedback">
					<text class="iconposition icon color-blue iconbg" style="color:#04EAFB;">&#xe74f;</text>
					<br/>
					<text>投诉建议</text>
				</navigator>
			</view>
		</view>
	</view>
</template>

<script>
	import common from '@/common/common.js';
	import {mapState, mapMutations} from 'vuex';
	
	export default {
		data() {
			return {
				role: {
					role_ids: '', //角色字符串
					device: false, //广告屏合作者
					shop: false, //店铺
					saleperson: false //业务员
				}
			}
		},
		computed: mapState(['forcedLogin', 'hasLogin', 'userInfo', 'commonheader']),
		onLoad() {},
		onShow() {
			//调用-判断用户角色
			this.is_role();
		},
		methods: {
			/**
			 * 投放广告
			 */
			usead() {
				uni.makePhoneCall({
					phoneNumber: '02865272616'
				});
			},
			
			/**
			 * 指定角色跳转
			 * @param {Object} role_ids
			 */
			toRole(role_ids) {
				let self = this;
				//广告屏合作商
				if (role_ids == 2) {
					uni.setStorageSync('role_id', role_ids); // 将用户角色 role_id 存储在本地缓存中（同步）
					//进入合作设备列表
					uni.navigateTo({
						url: '../user-partner/user-partner?user_id=' + self.userInfo.user_id + '&role_id=2'
					});
					/**有条件的限制进入版块 暂时未使用 s--*/
					// if (this.role.device == true) { //已经是广告屏合作者			
					// 	//账号该角色是否可用
					// 	uni.request({
					// 		url: this.$serverUrl + 'api/partnerRole',
					// 		data: {
					// 			user_id: this.userInfo.user_id,
					// 		},
					// 		header: {
					// 			'commonheader': this.commonheader,
					// 			'access-user-token': this.userInfo.token
					// 		},
					// 		method: 'PUT',
					// 		success: function(res) {
					// 			if (res.data.status == 1) {
					// 				if (res.data.data.status == 0) { //禁用
					// 					uni.showToast({
					// 						icon: 'none',
					// 						title: '账号该功能被禁用',
					// 						duration: 2000
					// 					});
					// 					return false;
					// 				}
					// 				if (res.data.data.status == 2) { //待审核
					// 					uni.showToast({
					// 						icon: 'none',
					// 						title: '申请审核中...',
					// 						duration: 2000
					// 					});
					// 					return false;
					// 				}
					// 				if (res.data.data.status == 3) { //驳回
					// 					uni.showToast({
					// 						icon: 'none',
					// 						title: '该账号不支持该申请',
					// 						duration: 2000
					// 					});
					// 					return false;
					// 				}
					// 				//进入申请页
					// 				uni.navigateTo({
					// 					url: '../user-partner/user-partner?user_id=' + self.userInfo.user_id + '&role_id=2'
					// 				});

					// 			}
					// 		}
					// 	})


					// } else { //还不是广告屏合作者
					// 	uni.showModal({
					// 		title: '提示',
					// 		content: '您还不是广告屏合作者,申请加入？',
					// 		success: function(res) {
					// 			if (res.confirm) {
					// 				uni.navigateTo({
					// 					url: "../user/apply-partner"
					// 				});
					// 			} else if (res.cancel) {

					// 			}
					// 		}
					// 	});
					// }
					/**有条件的限制进入版块 暂时未使用 e--*/
				}

				//店铺合作者
				if (role_ids == 3) {
					//进入店铺主页
					uni.navigateTo({
						url: '../user-shopkeeper/user-shopkeeper?user_id=' + self.userInfo.user_id + '&role_id=3'
					});
					// if (this.role.shop == true) { //已经是店铺合作者			
					// 	//账号该角色是否可用
					// 	uni.request({
					// 		url: this.$serverUrl + 'api/shopRole',
					// 		data: {
					// 			user_id: this.userInfo.user_id,
					// 		},
					// 		header: {
					// 			'commonheader': this.commonheader,
					// 			'access-user-token': this.userInfo.token
					// 		},
					// 		method: 'PUT',
					// 		success: function(res) {
					// 			if (res.data.status == 1) {
					// 				if (res.data.data.status == 0) { //禁用
					// 					uni.showToast({
					// 						icon: 'none',
					// 						title: '账号该功能被禁用',
					// 						duration: 2000
					// 					});
					// 					return false;
					// 				}
					// 				if (res.data.data.status == 2) { //待审核
					// 					uni.showToast({
					// 						icon: 'none',
					// 						title: '申请审核中...',
					// 						duration: 2000
					// 					});
					// 					return false;
					// 				}
					// 				if (res.data.data.status == 3) { //驳回
					// 					uni.showToast({
					// 						icon: 'none',
					// 						title: '该账号不支持该申请',
					// 						duration: 2000
					// 					});
					// 					return false;
					// 				}
					// 				//进入申请页
					// 				uni.navigateTo({
					// 					url: '../user-shopkeeper/user-shopkeeper?user_id=' + self.userInfo.user_id + '&role_id=3'
					// 				});

					// 			}
					// 		}
					// 	})


					// } else { //还不是店铺合作者
					// 	uni.showModal({
					// 		title: '提示',
					// 		content: '申请店铺安装智能屏？',
					// 		success: function(res) {
					// 			if (res.confirm) {
					// 				uni.navigateTo({
					// 					url: "../user/apply-shopkeeper"
					// 				});
					// 			} else if (res.cancel) {

					// 			}
					// 		}
					// 	});
					// }
				}

				//业务员
				if (role_ids == 1) {
					
					uni.navigateTo({
						url: '../saleperson/center'
					});				
				/**有条件的限制进入版块 暂时未使用 s--*/	
					// if (this.role.saleperson == true) { //已经是业务员			

					// 	//进入业务员首页
					// 	uni.navigateTo({
					// 		url: '../saleperson/center'
					// 	});

					// } else { //还不是业务员
					// 	uni.showModal({
					// 		title: '提示',
					// 		content: '咨询官方如何参与店通业务？',
					// 		success: function(res) {
					// 			if (res.confirm) {
					// 				uni.makePhoneCall({
					// 					phoneNumber: '13693444308'
					// 				});
					// 			} else if (res.cancel) {
					// 				//不操作
					// 			}
					// 		}
					// 	});
					// }
				/**有条件的限制进入版块 暂时未使用 e--*/
				}
				
				
			},
			
			/**
			 * 判断用户角色
			 */
			is_role() {
				let self = this;
				//获取角色信息
				uni.request({
					url: this.$serverUrl + 'api/get_user_role',
					data: {
						user_id: this.userInfo.user_id,
					},
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'POST',
					success: function(res) {
						if (res.data.status == 1) {
							//处理角色信息
							self.role.role_ids = res.data.data.role_ids;
							let role_str = res.data.data.role_ids;
							let role_array = role_str.split(',');
							role_array.forEach((value, index) => {
								switch (parseInt(value)) {
									case 2:
										self.role.device = true;
										break;
									case 3:
										self.role.shop = true;
										break;
									default:
										self.role.saleperson = true;
								}
							})
						} else {
							uni.showToast({
								icon: 'none',
								title: '网络繁忙，稍后重试',
								duration: 2000
							});
						}
					}
				})
			}
		}
	}
</script>

<style>
	.content {
		margin: 0;
		padding: 0;
		text-align: center;
	}

	.vedio-con {
		width: 100%;
		margin: 0;
		padding: 0;
		height: 205px;
	}

	.view-grid-con {
		margin: 0px 5px;
	}

	.text-grid-title {
		margin-top: 10px;
	}

	.text-grid {
		line-height: 80px;
		font-weight: bolder;
		font-size: 17px;
		color: #409EFF;
	}

	.user-title {
		line-height: 50px;
		font-size: 16px;
	}

	.iconbg {
		height: 50px;
		width: 50px;
		border-radius: 50px;
		border: 1px solid #F3F3F3;
		line-height: 50px;
		display: inline-block;
	}

	.navcon {
		display: flex;
		flex-flow: row wrap;
		justify-content: left;
		text-align: center;
	}

	.navcon-item {
		flex: 0 0 33%;
		padding: 10px 0;
	}
</style>
