<template>
	<view class="uni-page-body">
		<view>
			<!-- <uni-card is-shadow> -->
				<map class="map" :longitude="longitude" :latitude="latitude" :scale="9" :markers="segmentedControl.current == 0 ? markers : orderMarkers" :enable-satellite="false"></map>
			<!-- </uni-card> -->
		</view>
		
		<!-- SegmentedControl 分段器 s -->
		<view class="uni-padding-wrap uni-common-mt mb">
			<uni-segmented-control :current="segmentedControl.current" :values="segmentedControl.items" @clickItem="onClickItem" style-type="text" active-color="#4C85FC"></uni-segmented-control>
			<view class="">
				<view v-if="segmentedControl.current === 0">
					<uni-card title="已合作广告屏" thumbnail="" :extra="'合计 ' + salecount + ' 台'" is-full is-shadow>
						<uni-list>
							<view v-if="this.deviceList.length == 0" class="uni-center notdevice">您还没有合作广告屏</view>
							
							<uni-list-item v-for="(item, index) in deviceList" :key="index" :title="'广告收入：累计￥' + item.total_income" :note="'店铺：' + item.shop_name + '（地址：'+ item.address +'）'" @click="toPartnerDeviceDetail(item.partner_device_id, item.partner_id, item.device_id)"><!-- 广告屏编号：{{item.device_id}} --></uni-list-item><!-- 今日￥' + item.today_income + '， --><!-- '合作价：￥' + item.sale_price + '，占股：' + item.share * 100 + '%' -->
						</uni-list>
					</uni-card>
				</view>
				<view v-if="segmentedControl.current === 1">
					<uni-card title="已签约待付款" thumbnail="" :extra="'合计 ' + orderCount + ' 台'" is-full is-shadow>
						<uni-list>
							<view v-if="this.orderList.length == 0" class="uni-center notdevice">数据不存在</view>
							
							<uni-list-item v-for="(item, index) in orderList" :key="index" :title="'签约时间：' + item.order_time" :note="'店铺：' + item.shop_name + '（地址：'+ item.address +'）'" @click="toPartnerOrderDetail(item.order_id, item.partner_id, item.device_id)"><!-- 广告屏编号：{{item.device_id}} --></uni-list-item><!-- '合作价：￥' + item.sale_price + '，占股：' + item.share * 100 + '%' -->
						</uni-list>
					</uni-card>
				</view>
			</view>
		</view>
		<!-- SegmentedControl 分段器 e -->
		
		<view class="uni-padding-wrap uni-common-mt uni-common-mb btn-bottom">
			<button class="primary fon16" type="primary" @click="toDeviceList">
				查看可合作的屏
				<text class="icon icon-position">&#xe6a2;</text>
			</button>
		</view>
	</view>
</template>

<script>
	import {mapState} from 'vuex';
	
	export default {
		data() {
			return {
				userId: '', // 用户ID
				roleId: '', // 用户角色ID			
				latitude: 30.657420, //纬度
				longitude: 104.065840, //经度
				markers: [], //地图图标
				salecount: 0, //合作屏数量
				deviceList: [], //设备列表
				orderCount: 0, // 订单数量
				orderList: [], // 订单列表
				orderMarkers: [], //地图图标
				
				// SegmentedControl 分段器
				segmentedControl: {
					items: ['已合作', '待付款'],
					current: 0
				}
			}
		},
		computed: {
			...mapState(['hasLogin', 'forcedLogin', 'userInfo', 'commonheader'])
		},
		onLoad(event) {
			// 获取参数
			this.userId = event.user_id;
			this.roleId = event.role_id;
			
			this.getPartnerDeviceList();
			this.getPartnerOrderList();
		},
		onNavigationBarButtonTap(e) {
			this.$common.actionSheetTap();
		},
		methods: {
			/**
			 * 获取广告屏合作商合作的广告屏列表
			 */
			getPartnerDeviceList() {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/partner_device',
					data: {
						user_id: this.userId,
						role_id: this.roleId
					},
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					success: (res) => {
						self.salecount = res.data.data.total;
						self.deviceList = res.data.data.data;
						self.deviceList.forEach((item, index) => {
							self.$set(self.markers, index, {
								title: item.device_id + ' ' + item.shop_name,
								longitude: item.longitude,
								latitude: item.latitude
							});
						})
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
			 * 跳转广告屏合作商合作的广告屏详情页
			 * @param {Object} partner_device_id
			 * @param {Object} partner_id
			 * @param {Object} device_id
			 */
			toPartnerDeviceDetail(partner_device_id, partner_id, device_id) {
				uni.navigateTo({
					url: '../partner-device/partner-device-detail?partner_device_id=' + partner_device_id + '&partner_id=' + partner_id + '&device_id=' + device_id
				})
			},
			
			/**
			 * 获取广告屏合作商订单列表
			 */
			getPartnerOrderList() {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/partner_order',
					data: {
						user_id: this.userId,
						role_id: this.roleId
					},
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					success: (res) => {
						self.orderCount = res.data.data.total;
						self.orderList = res.data.data.data;
						self.orderList.forEach((item, index) => {
							/* self.$set(self.orderList, index,{
								order_sn: item.order_sn,
								device_id: item.device_id,
								order_time: item.order_time,
								shop_name: item.shop_name
							}); */
							self.$set(self.orderMarkers, index, {
								title: item.device_id + ' ' + item.shop_name,
								longitude: item.longitude,
								latitude: item.latitude
							});
						})
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
			 * 跳转广告屏合作商订单详情页
			 * @param {Object} order_id
			 * @param {Object} partner_id
			 * @param {Object} device_id
			 */
			toPartnerOrderDetail(order_id, partner_id, device_id) {
				uni.navigateTo({
					url: '../partner-order/partner-order?order_id=' + order_id + '&partner_id=' + partner_id + '&device_id=' + device_id
				})
			},
			
			/**
			 * 跳转广告屏列表页
			 */
			toDeviceList() {
				uni.navigateTo({
					url: '../device/device'
				})
			},
			
			/**
			 * SegmentedControl 分段器组件触发点击事件时触发
			 * @param {Object} e
			 */
			onClickItem(e) {
				if (this.segmentedControl.current !== e.currentIndex) {
					this.segmentedControl.current = e.currentIndex;
				}
			}
		}
	}
</script>

<style>
	.map {
		width: 100%;
		height: 400rpx;
	}
	.notdevice{
		font-size:16px;
		line-height: 60px;
	}
	.icon-position{
		position: relative;
		top:2px;
		margin-left: 5px;
		font-size: 20px;
	}
</style>
