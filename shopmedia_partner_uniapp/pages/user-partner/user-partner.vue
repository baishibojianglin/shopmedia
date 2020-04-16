<template>
	<view class="uni-page-body">
		<view>
			<map class="map" :longitude="longitude" :latitude="latitude" :scale="9" :markers='markers' :enable-satellite="false" show-location>
			</map>
		</view>

		<view class="countcon">
			<view class="countcon-item">
				<text>自营：{{5040+salecount}} 台</text>
			</view>
			<view class="countcon-item">
				<text>可合作：{{salecount}} 台</text>
			</view>
		</view>
		
		<view>
			<view class="listcon">
				<view class="listcon-item-1">
					<text>屏号</text>
				</view>
				<view class="listcon-item-2">
					<text>店名</text>
				</view>
				<view class="listcon-item-1">
					<text>合作价</text>
				</view>
			</view>
			
			<view class="listcon" v-for="(value, key) in devicelist" :key="key">
				<view class="listcon-item-1">
					<text>{{value.device_id}}</text>
				</view>
				<view class="listcon-item-2">
					<text>{{value.shopname}}</text>
				</view>
				<view class="listcon-item-1">
					<text>¥{{value.sale_price}}</text>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import common from '@/common/common.js';
	
	export default {
		data() {
			return {
				latitude: 30.657420, //纬度
				longitude: 104.065840, //经度
				markers: [], //地图图标
				salecount: 0, //合作屏数量
				devicelist: [] //设备列表
			}
		},
		onLoad(event) {
			console.log(22, event)
			this.getmarkers();
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
					header: {
						'sign': common.sign(), // 验签，TODO：对参数如did等进行AES加密，生成sign如：'6IpZZyb4DOmjTaPBGZtufjnSS4HScjAhL49NFjE6AJyVdsVtoHEoIXUsjrwu6m+o'
						'version': getApp().globalData.version, // 应用大版本号
						'model': getApp().globalData.systemInfo.model, // 手机型号
						'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
						'did': getApp().globalData.did // 设备号
					},
					success: (res) => {
						console.log(123, res);
					}
				});
			},
			
			//获取广告屏位置
			getmarkers() {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/getMarkers',
					header: /* getApp().globalData.commonHeaders */ {
						'content-type': "application/json; charset=utf-8",
						'sign': common.sign(), // 验签，TODO：对参数如did等进行AES加密，生成sign如：'6IpZZyb4DOmjTaPBGZtufjnSS4HScjAhL49NFjE6AJyVdsVtoHEoIXUsjrwu6m+o'
						'version': getApp().globalData.version, // 应用大版本号
						'model': getApp().globalData.systemInfo.model, // 手机型号
						'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
						'did': getApp().globalData.did // 设备号
					},
					success: (res) => {
						console.log(res)
						res.data.data.forEach((value, index) => {
							self.$set(self.markers, index, {
								title: value.device_id + ' ' + value.shopname,
								longitude: value.longitude,
								latitude: value.latitude
							});
						})
					}
				});
			}
		}
	}
</script>

<style>
	.content {
		display: flex;
		margin: 0;
		padding: 0;
	}

	.map {
		width: 100%;
		height: 280rpx;
	}

	.countcon {
		display: flex;
		flex-direction: row;
		justify-content: center;
		border-bottom: 1px solid #E7E6DF;

	}

	.countcon-item {
		flex: 1;
		line-height: 40px;
	}

	.listcon {
		display: flex;
		flex-direction: row;
		justify-content: center;
		border-bottom: 1px solid #E7E6DF;
	}

	.listcon-item-1 {
		flex: 1;
		line-height: 40px;
	}

	.listcon-item-2 {
		flex: 2;
		line-height: 40px;
	}
</style>
