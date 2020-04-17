<template>
	<view class="uni-page-body">
		<view>
			<uni-card is-shadow>
				<map class="map" :longitude="longitude" :latitude="latitude" :scale="9" :markers="markers" :enable-satellite="false"></map>
			</uni-card>
			<!-- <map class="map" :longitude="longitude" :latitude="latitude" :scale="9" :markers="markers" :enable-satellite="false"></map> -->
		</view>
		
		<view class="uni-common-mt">
			<uni-card title="拥有广告屏" thumbnail="" :extra="'合计 ' + salecount + ' 台'" is-shadow>
				<uni-list>
					<uni-list-item v-for="(item, index) in deviceList" :key="index" :title="'广告收入：今日￥' + 123 + '，累计￥' + 999" :note="'店铺：' + item.shopname">广告屏编号：{{item.device_id}}</uni-list-item><!-- '合作价：￥' + item.sale_price + '，占股：' + item.share * 100 + '%' -->
				</uni-list>
			</uni-card>
		</view>
	</view>
</template>

<script>
	import common from '@/common/common.js';
	
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
		onLoad(event) {
			// 获取参数
			this.userId = event.user_id;
			this.roleId = event.role_id;
			
			this.getUserPartnerDevice();
		},
		methods: {
			/**
			 * 获取广告设备合作者拥有的设备
			 */
			getUserPartnerDevice() {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/user_partner_device',
					data: {
						user_id: this.userId,
						role_id: this.roleId
					},
					header: {
						'sign': common.sign(), // 验签，TODO：对参数如did等进行AES加密，生成sign如：'6IpZZyb4DOmjTaPBGZtufjnSS4HScjAhL49NFjE6AJyVdsVtoHEoIXUsjrwu6m+o'
						'version': getApp().globalData.version, // 应用大版本号
						'model': getApp().globalData.systemInfo.model, // 手机型号
						'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
						'did': getApp().globalData.did, // 设备号
						'access-user-token': global.isLogin().user_info.token
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
						console.log('error', error)
					}
				});
			}
		}
	}
</script>

<style>
	.map {
		width: 100%;
		height: 320rpx;
	}
</style>
