<template>
	<view class="uni-page-body">
		<view>
			<swiper class="swiper" :autoplay="true" :interval="3000" :circular="true" :indicator-dots="true"
			 indicator-active-color="#fff">
				<swiper-item v-for="(value, key) in imglist" :key="key">
					<view class="swiper-item">
						<image :src="value.url"></image>
					</view>
				</swiper-item>
			</swiper>
		</view>

		<uni-card :is-shadow="true" style="margin-bottom:60px;">
			<view>
				<view class="datalist">
					<text class="datalist-title">屏编号：</text>
					<text class="datalist-content">{{datalist.device_id}}</text>
				</view>
				<view class="datalist">
					<text class="datalist-title">屏尺寸：</text>
					<text class="datalist-content">{{datalist.size}}<!-- {{device_size[datalist.size]}} --> 寸</text>
				</view>
				<view class="datalist">
					<text class="datalist-title">安装店铺：</text>
					<text class="datalist-content">{{datalist.shop_name}}</text>
				</view>
				<view class="datalist">
					<text class="datalist-title">店铺面积：</text>
					<text class="datalist-content">{{datalist.shop_area}} ㎡</text>
				</view>
				<view class="datalist">
					<text class="datalist-title">所属行业：</text>
					<text class="datalist-content">{{cate_name}}</text>
				</view>
				<view class="datalist">
					<text class="datalist-title">位置：</text>
					<view class="datalist-content" @click="openLocation()"><text style="position: relative;top:6px;"><text class="uni-icon uni-icon-location-filled color-blue"></text> {{datalist.address}}</text></view>
				</view>
				<view class="datalist">
					<text class="datalist-title">周围环境：</text>
					<text class="datalist-content">{{datalist.environment}}</text>
				</view>
				<view class="datalist">
					<text class="datalist-title">投放价格：</text>
					<text class="datalist-content color-red"><!-- {{device_price[datalist.level]}} -->1 元/天起</text>
				</view>
			</view>
		</uni-card>
	</view>
</template>

<script>
	import {
		mapState
	} from 'vuex';

	export default {
		data() {
			return {
				device_id: 0, //设备id
				datalist: {}, //设备详细信息
				cate_name: '', //行业名字
				imglist: [] ,//实景图列表
				device_size:{} ,//设备尺寸
				device_price:{} //投放价格
			}
		},
		computed: {
			...mapState(['hasLogin', 'forcedLogin', 'userInfo', 'commonheader'])
		},
		onLoad(options) {
			//获取设备id
			this.device_id = options.device_id;
			//获取店铺行业配置信息
			this.getCate()
			//获取设备信息
			this.deviceDetail();
			//获取设备尺寸、价格
			this.getSize();
			this.getPrice();
		},
		onNavigationBarButtonTap(e) {
			this.$common.actionSheetTap();
		},
		methods: {
			//获取行业配置信息
			getCate() {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/shop_cate_list',
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'GET',
					success: (res) => {
						self.shopcatelist = res.data.data;
					}
				});
			},

			//获取屏尺寸
			getSize(){
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/device_size',
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'GET',
					success: (res) => {
						self.device_size = res.data;
					}
				});
			},

			//获取屏价格
			getPrice(){
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/device_price',
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'GET',
					success: (res) => {
						self.device_price = res.data;
						// console.log(self.device_price)
					}
				});
			},





	
			//获取设备详细信息
			deviceDetail() {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/device_detail',
					data: {
						device_id: self.device_id
					},
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'GET',
					success: (res) => {
						self.datalist = res.data.data; //赋值
						self.cate_name = self.shopcatelist[self.datalist.shop_cate - 1].cate_name;
						//取实景图
						self.imglist = JSON.parse(res.data.data.url_image);
					},
				});
			},

			/**
			 * 查看位置
			 */
			openLocation() {
				// 使用应用内置地图查看位置
				uni.openLocation({
					latitude: Number(this.datalist.latitude),
					longitude: Number(this.datalist.longitude),
					name: this.datalist.shop_name,
					address: this.datalist.address
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

	.bottom-con {
		margin-bottom: 100px;
	}
</style>
