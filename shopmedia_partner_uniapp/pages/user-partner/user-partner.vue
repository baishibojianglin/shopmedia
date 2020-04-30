<template>
	<view class="uni-page-body">
		<view>
			<uni-card is-shadow>
				<map class="map" :longitude="longitude" :latitude="latitude" :scale="9" :markers="markers" :enable-satellite="false"></map>
			</uni-card>
		</view>
		
		<view class="uni-common-mt mb">
			<uni-card title="合作广告屏" thumbnail="" :extra="'合计 ' + salecount + ' 台'" is-shadow>
				<uni-list>
					<uni-list-item v-for="(item, index) in deviceList" :key="index" :title="'广告收入：今日￥' + item.today_income + '，累计￥' + item.total_income" :note="'店铺：' + item.shopname" @click="toPartnerDeviceDetail(item.partner_device_id, item.partner_id, item.device_id)">广告屏编号：{{item.device_id}}</uni-list-item><!-- '合作价：￥' + item.sale_price + '，占股：' + item.share * 100 + '%' -->
				</uni-list>
			</uni-card>
			<view v-if="this.deviceList.length == 0" class="uni-center notdevice">您还没有合作的广告屏，去寻找发现吧！</view>
		</view>
		
		<view class="uni-padding-wrap uni-common-mt uni-common-mb btn-bottom">
			<button class="primary" type="primary" @click="toDeviceList">发现广告屏</button>
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
				deviceList: [] //设备列表
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
						'commonheader': this.$store.state.commonheader,
						'access-user-token': this.userInfo.token
					},
					success: (res) => {
						self.salecount = res.data.data.total;
						self.deviceList = res.data.data.data;
						self.deviceList.forEach((value, index) => {
							self.$set(self.markers, index, {
								title: value.device_id + ' ' + value.shopname,
								longitude: value.longitude,
								latitude: value.latitude
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
			 * 跳转广告屏列表页
			 */
			toDeviceList() {
				uni.navigateTo({
					url: '../device/device'
				})
			}
		}
	}
</script>

<style>
	.map {
		width: 100%;
		height: 320rpx;
	}
	.notdevice{
		font-size:16px;
	}
</style>
