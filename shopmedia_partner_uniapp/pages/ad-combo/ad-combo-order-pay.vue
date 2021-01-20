<template>
	<view class="uni-container">
		<view class=""> 
			<uni-card :is-shadow="true" class="uni-bold">
				<view class="uni-flex uni-row">
					<view class="text-left" style="width: 360rpx;">广告名称</view>
					<view class="uni-common-pl text-right" style="-webkit-flex: 1;flex: 1;">{{adComboOrder.ad_name}}</view>
				</view>
				<view class="uni-flex uni-row">
					<view class="text-left" style="width: 360rpx;">广告主</view>
					<view class="uni-common-pl text-right" style="-webkit-flex: 1;flex: 1;">{{adComboOrder.advertiser_name}}</view>
				</view>
				<view class="uni-flex uni-row">
					<view class="text-left" style="width: 360rpx;">订单编号</view>
					<view class="uni-common-pl text-right" style="-webkit-flex: 1;flex: 1;">{{adComboOrder.order_sn}}</view>
				</view>
				<view class="uni-flex uni-row">
					<view class="text-left" style="width: 360rpx;">广告套餐价格</view>
					<view class="uni-common-pl text-right color-red" style="-webkit-flex: 1;flex: 1;">￥{{adComboOrder.order_price}}</view>
				</view>
			</uni-card>
			
			<uni-card :is-shadow="true">
				<view class="uni-title uni-bold">选择支付方式</view>
				<text class="uni-text-small color-red">（点击显示二维码收款）</text>
				<view class="uni-padding-wrap uni-common-mt" style="width: 100%; text-align: center;">
					<uni-grid :column="1" :showBorder="false" :square="false">
						<uni-grid-item>
							<image src="/static/img/tuijian_top.png" :lazy-load="true" style="width: 100rpx;height: 50rpx;"></image>
							<image src="/static/img/WePayLogo.png" :lazy-load="true" style="width: 172rpx;height: 53rpx;" @tap="previewPayQRCode(wxNativePayQRCodeImgUrl)"></image>
							<tki-qrcode
							   ref="qrcode"
							   cid="1"
							   :val="wxNativePayQRCodeUrl"
							   :size="200"
							   unit="upx"
							   background="#ffffff"
							   foreground="#000000"
							   pdground="#000000"
							   icon="/static/img/WePayLogo_green.png"
							   :iconSize="20"
							   :lv="3" 
							   :onval="true"
							   :loadMake="true"
							   :usingComponents="true"
							   :showLoading="false"
							   loadingText="二维码生成中"
							   @result="qrR" v-show="true" />
						</uni-grid-item>
						<uni-grid-item>
							<image src="/static/img/allinpay_logo.png" :lazy-load="true" style="width: 172rpx;height: 53rpx;" @tap="previewPayQRCode('/static/img/allinpay_QRCode.png')"></image>
							<text class="uni-text-small" style="text-align: left;">（支持微信、支付宝）</text>
						</uni-grid-item>
					</uni-grid>
				</view>
			</uni-card>
		</view>
	</view>
</template>

<script>
	import {mapState} from 'vuex';
	import tkiQrcode from "@/components/tki-qrcode/tki-qrcode.vue"
	
	export default {
		components: {tkiQrcode},
		data() {
			return {
				order_id: '', // 广告套餐订单ID
				adComboOrder: {}, // 广告套餐订单数据
				wxNativePayQRCodeUrl: '', // 微信扫码支付二维码链接
				wxNativePayQRCodeImgUrl: '' // 二维码生成后的图片地址或base64
			}
		},
		computed: {
			...mapState(['hasLogin', 'forcedLogin', 'userInfo', 'commonheader'])
		},
		onLoad(option) {
			// 获取新增广告套餐订单ID
			if (option.order_id != undefined && option.order_id) {
				this.order_id = option.order_id;
				
				this.getAdComboOrder();
			}
		},
		methods: {
			/**
			 * 获取广告套餐订单数据
			 */
			getAdComboOrder() {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/ad_combo_order/' + this.order_id,
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'GET',
					success: function(res) {
						// console.log('adComboOrder', res);
						if (res.data.status == 1) {
							self.adComboOrder = res.data.data;
							self.getWxNativePayQRCodeUrl();
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
			 * 获取微信扫码支付付款二维码链接
			 */
			getWxNativePayQRCodeUrl() {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/adComboWxNativePayQRCodeUrl',
					data: {
						order_id: this.order_id,
						order_price: this.adComboOrder.order_price
					},
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'POST',
					success: function(res) {
						// console.log('wxNativePay', res);
						if (res.data.status == 1) {
							self.wxNativePayQRCodeUrl = res.data.data.code_url;
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
			 * 返回二维码路径
			 * @param {Object} res
			 */
			qrR(res) {
				// console.log('qrR', res);
				this.wxNativePayQRCodeImgUrl = res;
			},
			
			/**
			 * 预览付款二维码
			 * @param {Object} image
			 */
			previewPayQRCode(image) {
				var imgArr = [];
				imgArr.push(image);
				// 预览图片
				uni.previewImage({
					urls: imgArr,
					current: imgArr[0]
				});
			}
		}
	}
</script>

<style>

</style>
