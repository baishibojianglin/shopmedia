<template>
	<view class="uni-page-body">
		<view>
			<swiper class="swiper" :autoplay="true" :interval="3000" :circular="true" :indicator-dots="true" indicator-active-color="#fff">
				<swiper-item v-for="(value, key) in imglist" :key="key">
					<view class="swiper-item">
						<image :src="value"></image>
					</view>
				</swiper-item>
			</swiper>
		</view>

		<view class="uni-padding-wrap uni-common-mt mb">
			<view class="datalist">
				<text class="datalist-title">屏编号：</text>
				<text class="datalist-content">{{datalist.device_id}}</text>
			</view>
			<view class="datalist">
				<text class="datalist-title">总价：</text>
				<text class="datalist-content color-red">{{datalist.sale_price}} 元</text>
			</view>
			<view class="datalist" v-if="datalist.sale_price">
				<text class="datalist-title">合作价：</text>
				<text class="datalist-content color-red">{{(datalist.sale_price/2).toFixed(2)}} 元</text>
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
				<text class="datalist-content">{{datalist.shopname}}</text>
			</view>
			<view class="datalist">
				<text class="datalist-title">店铺面积：</text>
				<text class="datalist-content">{{datalist.shopsize}} ㎡</text>
			</view>
			<view class="datalist">
				<text class="datalist-title">所属行业：</text>
				<text class="datalist-content">{{datalist.shopcate}}</text>
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
				device_id: 0, //设备id
				datalist: {}, //设备信息列表
				imglist: [], //实景图列表
				shopcatelist: {}, //店铺行业列表
				csPhone: '02865272616', // 客服电话
				
				/* GoodsNav 商品导航 s */
				options: [{
					icon: 'headphones',
					text: '联系客服'
				}, {
					icon: 'location-filled',
					text: '位置导航'
				}],
				buttonGroup: [{
					text: '立即合作',
					backgroundColor: '#504AF2',
					color: '#fff'
				}]
				/* GoodsNav 商品导航 e */
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
			// 获取广告屏合作商业务员信息
			this.getPartnerSalesman();
		},
		onNavigationBarButtonTap(e) {
			this.$common.actionSheetTap();
		},
		methods: {
			//获取行业配置信息
			getCate() {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/shopCateList',
					header: {
						'commonheader': this.$store.state.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'GET',
					success: (res) => {
						self.shopcatelist = res.data.data;
					}
				});
			},
			//获取设备详细信息
			deviceDetail() {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/DeviceDetail',
					data: {
						device_id: self.device_id
					},
					header: {
						'commonheader': this.$store.state.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'POST',
					success: (res) => {
						self.datalist = res.data.data; //赋值
						self.datalist.shopcate = self.shopcatelist[self.datalist.shopcate].cate_name; //展示店铺行业
						//取实景图
						let str_image = res.data.data.url_image;
						if (str_image.indexOf(',') == -1) {
							self.$set(self.imglist, 0, str_image);
						} else {
							let str_image_array = str_image.split(",");
							str_image_array.forEach((value, index) => {
								self.$set(self.imglist, index, value);
							})
						}
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
			
			/* GoodsNav 商品导航 s */
			/**
			 * GoodsNav 左侧点击事件
			 * @param {Object} e
			 */
			onClick (e) {
				/* uni.showToast({
					title: `点击${e.content.text}`,
					icon: 'none'
				}) */
				// 联系客服
				if (e.index == 0) {
					this.callPhone(this.csPhone);
				}
				// 位置导航
				if (e.index == 1) {
					this.openLocation();
				}
			},
			/**
			 * GoodsNav 右侧按钮组点击事件
			 * @param {Object} e
			 */
			buttonClick (e) {
				// 立即合作
				if (e.index == 0) {
					uni.navigateTo({
						url: '../partner-order/partner-order?cs_phone=' + this.csPhone + '&device=' + encodeURIComponent(JSON.stringify(this.datalist))
					})
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
			 * 查看位置
			 */
			openLocation() {
				// 使用应用内置地图查看位置
				uni.openLocation({
					latitude: Number(this.datalist.latitude),
					longitude: Number(this.datalist.longitude)
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
