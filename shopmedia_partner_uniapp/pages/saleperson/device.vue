<template>
	<view class="content">
		
        <view>
			<swiper class="swiper" :autoplay="true" :interval="3000" :circular="true" :indicator-dots="true" indicator-active-color="#fff">
				<swiper-item v-for="(value, key) in imglist" :key="key">
					<view class="swiper-item">
			           <image :src="value.url"></image>
					</view>
				</swiper-item>
			</swiper>
		</view>
		<uni-card :is-shadow="true">
			<view>
				<view class="datalist">
					<text class="datalist-title">屏编号：</text>
					<text class="datalist-content">{{datalist.device_id}}</text>
				</view>
				<view class="datalist">
					<text class="datalist-title">总价：</text>
					<text class="datalist-content">{{datalist.sale_price*2}} 元</text>
				</view>
				<view class="datalist">
					<text class="datalist-title">合作价：</text>
					<text class="datalist-content color-red">{{datalist.sale_price}} 元</text>
				</view>
				<view class="datalist">
					<text class="datalist-title">占股比例：</text>
					<text class="datalist-content color-red">50%</text>
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
				<view class="datalist" style="border-bottom: none;">
					<text class="datalist-title">周围环境：</text>
					<text class="datalist-content">{{datalist.environment}}</text>
				</view>
			</view>
		</uni-card>
		
		<view class="ordercon">
			<button  class="ordercon-item-1 bg-qgray-color fon16" @click="openLocation()">位置导航</button>
			<navigator class="ordercon-item-2" :url="'./signbook?device_id='+device_id+'&address='+datalist.address+'&sale_price='+datalist.sale_price">
			   <button  class="bg-main-color color-white fon16">展示合作协议</button>
		    </navigator>
		</view>
		
	</view>
</template>

<script>
	import {mapState, mapMutations} from 'vuex';
	export default {
		data() {
				return {
                     device_id:0 ,//设备id
					 datalist:{}, //设备信息列表
					 imglist:[] ,//实景图列表
				}
		},
		computed: mapState(['forcedLogin','hasLogin','userInfo','commonheader']),
		onLoad(options) {
           //获取设备id
           this.device_id=options.device_id;
		   //获取设备信息
		   this.deviceDetail();
		},
		onNavigationBarButtonTap(e) {
			this.$common.actionSheetTap();
		},
		methods: {
			//获取设备详细信息
			deviceDetail(){
				let self=this;
				uni.request({
					url: this.$serverUrl+'api/device_detail',
					data: {device_id:self.device_id},
					method: 'GET',
					header: {
						'commonheader': this.commonheader,
						'access-user-token':this.userInfo.token
					},
					success: (res) => {
					   self.datalist=res.data.data; //赋值
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
					longitude: Number(this.datalist.longitude)
				});
			}
		}
	}
</script>

<style>
.content{
	margin: 0;
	padding: 0;
	text-align: center;
}
.swiper{
	width: 100%;
	height: 250px;
}
.swiper-item{
	width: 100%;
	height: 100%;
}
.swiper-item image{
	width: 100%;
	height: 100%;
}
.datalist{
	display: flex;
	flex-direction:row;
	padding: 0 20rpx;
	line-height: 40px;
	border-bottom: 1px solid #E5E2DF;
}
.datalist-title{
	flex-basis: 80px;
	text-align: right;
}
.datalist-content{
	flex: 1;
	text-align: right;
}
.color-red{
	color:#FF6633;
}
.ordercon{
	display: flex;
	flex-direction:row;
}
.ordercon-item-1{
	flex: 1;
	text-align: center;
	margin: 10rpx 15rpx;
}
.ordercon-item-2{
	flex: 3;
	text-align: center;
	margin: 10rpx 15rpx;
}
</style>
