<template>
	<view>
		<view class="uni-page-body">
			<swiper class="swiper" :autoplay="true" :interval="3000" :circular="true" :indicator-dots="true" indicator-active-color="#fff">
				<swiper-item v-for="(value, key) in imglist" :key="key">
					<view class="swiper-item">
						<image :src="value.url"></image>
					</view>
				</swiper-item>
			</swiper>
			<view class="uni-padding-wrap uni-common-mt">
				<view class="datalist">
					<text class="datalist-title">屏编号：</text>
					<text class="datalist-content">{{datalist.device_id}}</text>
				</view>
				<view class="datalist">
					<text class="datalist-title">合作价：</text>
					<text class="datalist-content color-red">{{datalist.sale_price}} 元</text>
				</view>
				<view class="datalist">
					<text class="datalist-title">屏尺寸：</text>
					<text class="datalist-content">{{datalist.size}} 寸</text>
				</view>
				<view class="datalist">
					<text class="datalist-title">数据系统：</text>
					<text class="datalist-content">店通智能大数据系统</text>
				</view>
				<view class="datalist">
					<text class="datalist-title">店铺：</text>
					<text class="datalist-content">{{datalist.shop_name}}</text>
				</view>
				<view class="datalist">
					<text class="datalist-title">店铺面积：</text>
					<text class="datalist-content">{{datalist.shop_area}} ㎡</text>
				</view>
				<view class="datalist">
					<text class="datalist-title">所属行业：</text>
					<text class="datalist-content">{{datalist.shop_cate_name}}</text>
				</view>
				<view class="datalist">
					<text class="datalist-title">位置：</text>
					<text class="datalist-content">{{datalist.address}}</text>
				</view>
				<view class="datalist">
					<text class="datalist-title">周围环境：</text>
					<text class="datalist-content">{{datalist.environment}}</text>
				</view>
			</view>
		</view>
		
		<view class="uni-common-mt mb">
			<uni-card isShadow>
				<view class="uni-page-head">
					<view class="uni-page-head-title">
						广告收入
					</view>
				</view>
				<view class="uni-page-body">
					<uni-grid class="uni-center uni-bold" :column="2" :square="false">
						<uni-grid-item>
							<view>今日<text class="color-red">￥{{partnerDevice.today_income}}</text></view>
						</uni-grid-item>
						<uni-grid-item>
							<view>累计<text class="color-red">￥{{partnerDevice.total_income}}</text></view>
						</uni-grid-item>
					</uni-grid>
				</view>
			</uni-card>
			<uni-card isShadow>
				<view class="uni-page-head">
					<view class="uni-page-head-title">
						合作签约信息
					</view>
				</view>
				<view class="uni-page-body">
					<uni-list>
						<uni-list-item :showArrow="false">
							<view class="uni-flex uni-row" style="-webkit-justify-content: space-between;justify-content: space-between;">
								<view class="">签约订单编号</view>
								<view class="">{{partnerOrder.order_sn}}</view>
							</view>
						</uni-list-item>
						<uni-list-item :showArrow="false">
							<view class="uni-flex uni-row" style="-webkit-justify-content: space-between;justify-content: space-between;">
								<view class="">签约下单时间</view>
								<view class="">{{partnerOrder.order_time}}</view>
							</view>
						</uni-list-item>
						<uni-list-item :showArrow="false">
							<view class="uni-flex uni-row" style="-webkit-justify-content: space-between;justify-content: space-between;">
								<view class="">广告屏总价格</view>
								<view class="color-red">￥{{partnerOrder.device_price}}</view>
							</view>
						</uni-list-item>
						<uni-list-item :showArrow="false">
							<view class="uni-flex uni-row" style="-webkit-justify-content: space-between;justify-content: space-between;">
								<view class="">广告屏合作金额</view>
								<view class="color-red uni-bold">￥{{partnerOrder.order_price}}</view>
							</view>
						</uni-list-item>
						<uni-list-item :showArrow="false">
							<view class="uni-flex uni-row" style="-webkit-justify-content: space-between;justify-content: space-between;">
								<view class="">广告屏占股比例</view>
								<view class="uni-bold">{{partnerOrder.party_b_share * 100}}%</view>
							</view>
						</uni-list-item>
					</uni-list>
				</view>
			</uni-card>
		</view>
	</view>
</template>

<script>
	import {mapState} from 'vuex';
	
	export default {
		data() {
			return {
				deviceId: 0, // 广告屏ID
				datalist: {}, // 广告屏信息
				imglist: [], // 实景图列表
				csPhone: '', // 客服电话
				partnerDeviceId: 0,
				partnerDevice: {}, // 合作商合作的广告屏
				partnerId: 0, // 广告屏合作商ID
				partnerOrder: {} // 广告屏合作商订单信息
			}
		},
		computed: {
			...mapState(['hasLogin', 'forcedLogin', 'userInfo', 'commonheader'])
		},
		onLoad(options) {
			this.partnerDeviceId = options.partner_device_id;
			this.partnerId = options.partner_id;
			this.deviceId = options.device_id; // 获取广告屏ID
			this.getDeviceDetail(); // 获取广告屏信息
			this.getPartnerDevice(); // 获取广告屏合作商合作的广告屏信息
			this.getPartnerOrder(); // 获取广告屏合作商订单信息
			this.getPartnerSalesman(); // 获取广告屏合作商业务员信息
		},
		onNavigationBarButtonTap(e) {
			this.$common.actionSheetTap();
		},
		methods: {
			
			/**
			 * 获取广告屏信息
			 */
			getDeviceDetail() {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/device_detail',
					data: {
						device_id: self.deviceId
					},
					header: {
						'commonheader': this.$store.state.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'GET',
					success: (res) => {
						self.datalist = res.data.data; //赋值
						//取实景图
                        self.imglist = JSON.parse(res.data.data.url_image);
					},
				});
			},
			
			/**
			 * 获取广告屏合作商业务员信息
			 */
			getPartnerSalesman() {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/partner_salesman',
					data: {
						user_id: this.userInfo.user_id,
						role_id: uni.getStorageSync('role_id')
					},
					header: {
						'commonheader': this.$store.state.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'GET',
					success: (res) => {
						self.csPhone = res.data.data.phone;
					}
				})
			},
			
			/**
			 * 获取广告屏合作商合作的广告屏信息
			 */
			getPartnerDevice() {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/partner_device/' + this.partnerDeviceId,
					header: {
						'commonheader': this.$store.state.commonheader,
						'access-user-token': this.userInfo.token
					},
					success: (res) => {
						if (res.data.status == 1) {
							self.partnerDevice = res.data.data;
						}
					},
					fail(error) {
						uni.showToast({
							icon: 'none',
							title: '请求异常'
						});
					}
				});
			},
			
			/**
			 * 获取广告屏合作商订单信息
			 */
			getPartnerOrder() {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/partner_order/0',
					data: {
						partner_id: this.partnerId,
						device_id: this.deviceId
					},
					header: {
						'commonheader': this.$store.state.commonheader,
						'access-user-token': this.userInfo.token
					},
					success: (res) => {
						if (res.data.status == 1) {
							self.partnerOrder = res.data.data;
						}
					},
					fail(error) {
						uni.showToast({
							icon: 'none',
							title: '请求异常'
						});
					}
				});
			}
		}
	}
</script>

<style>
	.swiper {
		width: 100%;
		height: 250px;
	}

	.swiper-item {
		width: 100%;
		height: 100%;
	}

	.swiper-item image {
		width: 100%;
		height: 100%;
	}

	.datalist {
		display: flex;
		flex-direction: row;
		padding: 0 20rpx;
		line-height: 40px;
		border-bottom: 1px solid #E5E2DF;
	}

	/* .datalist-title {
		flex-basis: 70px;
		text-align: right;
	} */

	.datalist-content {
		flex: 1;
		text-align: right;
	}
</style>
