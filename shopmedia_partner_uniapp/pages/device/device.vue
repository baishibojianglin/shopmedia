<template>
	<view class="content">
		<view>
			<map class="map" :scale='9' :latitude="latitude" :longitude="longitude" :markers='markers' :enable-satellite="false">
			</map>
		</view>

		<view class="countcon">
			<view class="countcon-item">
				<text>屏总数：{{5040+salecount}} 台</text>
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
				<view class="listcon-item-3">
					<text v-show="false">详情</text>
				</view>
			</view>
			<view class="listcon" v-for="(value, key) in devicelist" :key="key" @click="toDevice(value.device_id)">
				<view class="listcon-item-1">
					<text>{{value.device_id}}</text>
				</view>
				<view class="listcon-item-2">
					<text>{{value.shopname}}</text>
				</view>
				<view class="listcon-item-1">
					<text class="">¥{{value.sale_price}}</text>
				</view>
				<view class="listcon-item-3">
					<text class="icon icon-size">&#xe6b7;</text>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {mapState} from 'vuex';
	
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
		computed: {
			...mapState(['hasLogin', 'forcedLogin', 'userInfo', 'commonheader'])
		},
		onLoad() {
			this.getmarkers();
		},
		methods: {
			//获取广告屏位置
			getmarkers() {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/getMarkers',
					header: {
						'commonheader': this.$store.state.commonheader,
						'access-user-token': this.userInfo.token
					},
					success: (res) => {
						self.salecount = res.data.data.length; //可合作数量
						self.devicelist = res.data.data; //可合作设备列表
						res.data.data.forEach((value, index) => {
							self.$set(self.markers, index, {
								title: value.device_id + ' ' + value.shopname,
								longitude: value.longitude,
								latitude: value.latitude
							});
						})
					}
				});
			},

			//跳转到设备详情
			toDevice(device_id) {
				uni.navigateTo({
					url: './device-detail?device_id=' + device_id
				});
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

	.map {
		width: 100%;
		height: 250px;
	}

	.countcon {
		display: flex;
		flex-direction: row;
		justify-content: center;
		border-bottom: 1px solid #E7E6DF;
		font-weight: bold;
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
		line-height: 40px;
		flex-basis: 70px;
	}

	.listcon-item-2 {
		flex: 1;
		line-height: 40px;
	}

	.listcon-item-3 {
		line-height: 40px;
		flex-basis: 30px;
	}

	.icon-size {
		font-size: 16px;
	}
</style>
