<template>
	<view class="uni-padding-wrap">
		<view class="uni-page-head">
			广告屏合作协议
		</view>
		<view class="uni-page-body mb">
			广告屏合作协议内容……
		</view>
		
		<view class="goods-carts">
			<uni-goods-nav :options="options" :button-group="buttonGroup" :fill="true" @click="onClick" @buttonClick="buttonClick" />
		</view>
	</view>
</template>

<script>
	import {mapState} from 'vuex';
	
	export default {
		data() {
			return {
				csPhone: '', // 客服电话
				device: '', // 广告屏信息
				
				/* GoodsNav 商品导航 s */
				options: [{
					icon: 'headphones',
					text: '联系客服'
				}],
				buttonGroup: [{
					text: '确认合作',
					backgroundColor: '#504AF2',
					color: '#fff'
				}]
				/* GoodsNav 商品导航 e */
			}
		},
		computed: {
			...mapState(['hasLogin', 'forcedLogin', 'userInfo', 'commonheader'])
		},
		onLoad(event) {
			this.csPhone = event.cs_phone;
			this.device = JSON.parse(decodeURIComponent(event.device));
		},
		onNavigationBarButtonTap(e) {
			this.$common.actionSheetTap();
		},
		methods: {
			/* GoodsNav 商品导航 s */
			/**
			 * GoodsNav 左侧点击事件
			 * @param {Object} e
			 */
			onClick (e) {
				// 联系客服
				if (e.index == 0) {
					this.callPhone(this.csPhone);
				}
			},
			/**
			 * GoodsNav 右侧按钮组点击事件
			 * @param {Object} e
			 */
			buttonClick (e) {
				let self = this;
				// 确认合作
				if (e.index == 0) {
					uni.showModal({
						title: `${e.content.text}`,
						content: '我已阅读并同意《广告屏合作协议》，确认合作。（如有疑问，请联系客服。）',
						cancelText: '联系客服',
						success: function (res) {
							if (res.confirm) {
								self.createOrder();
							}
							if (res.cancel) {
								self.callPhone(self.csPhone);
							}
						}
					});
				}
			},
			/* GoodsNav 商品导航 e */
			
			/**
			 * 拨打电话
			 * @param {Object} phone
			 */
			callPhone(phone){
				uni.makePhoneCall({
					phoneNumber: phone 
				});
			},
			
			/**
			 * 创建广告屏合作商订单
			 */
			createOrder() {
				uni.request({
					url: this.$serverUrl + 'api/partner_order',
					data: {
						user_id: this.userInfo.user_id,
						role_id: uni.getStorageSync('role_id'),
						phone: this.userInfo.phone,
						device_id: this.device.device_id,
						device_price: this.device.sale_price
					},
					header:{
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'POST',
					success: function(res) {
						console.log('order', res)
						if (res.data.status == 1) {
							uni.showToast({
								icon: 'none',
								title: res.data.message
							});
						} else {
							uni.showToast({
								icon: 'none',
								title: res.data.message
							});
						}
					}
				})
			}
		}
	}
</script>

<style>
	
</style>
