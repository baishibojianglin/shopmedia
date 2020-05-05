<template>
	<view class="content">
		
        <view>
			<swiper class="swiper" :autoplay="true" :interval="3000" :circular="true" :indicator-dots="true" indicator-active-color="#fff">
				<swiper-item v-for="(value, key) in imglist" :key="key">
					<view class="swiper-item">
			           <image :src="value"></image>
					</view>
				</swiper-item>
			</swiper>
		</view>
		
        <view>
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
		
		<view class="ordercon">
			<button  class="ordercon-item" @click="openLocation()">导航实地查看</button>
		</view>
		
	</view>
</template>

<script>
	import common from '@/common/common.js';
	import {mapState, mapMutations} from 'vuex';
	export default {
		data() {
				return {
                     device_id:0 ,//设备id
					 datalist:{}, //设备信息列表
					 imglist:[] ,//实景图列表
					 shopcatelist:{} //店铺行业列表
				}
		},
		computed: mapState(['forcedLogin','hasLogin','userInfo','commonheader']),
		onLoad(options) {
           //获取设备id
           this.device_id=options.device_id;
		   //获取店铺行业配置信息
		   this.getCate()
		   //获取设备信息
		   this.deviceDetail();
		},
		methods: {
			//获取行业配置信息
			getCate(){
			  let self=this;
			  uni.request({
			  	url: this.$serverUrl+'api/shop_cate_list',
			  	method:'GET',
			  	header: {
			  		'commonheader': this.commonheader,
			  		'access-user-token':this.userInfo.token
			  	},
			  	success: (res) => {
					 self.shopcatelist=res.data.data;
			  	}
			  });
			},
			//获取设备详细信息
			deviceDetail(){
				let self=this;
				uni.request({
					url: this.$serverUrl+'api/DeviceDetail',
					data:{device_id:self.device_id},
					method:'POST',
					header: {
						'commonheader': this.commonheader,
						'access-user-token':this.userInfo.token
					},
					success: (res) => {
					   self.datalist=res.data.data; //赋值
					   self.datalist.shopcate=self.shopcatelist[self.datalist.shopcate].cate_name; //展示店铺行业
					   //取实景图
					   let str_image=res.data.data.url_image;
					   if(str_image.indexOf(',')==-1){
						  self.$set(self.imglist,0,str_image);
					   }else{
						  let str_image_array=str_image.split(",");
						  str_image_array.forEach((value,index)=>{
						  	self.$set(self.imglist,index,value);
						  })
					   }
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
.ordercon-item{
	flex: 1;
	text-align: center;
	margin: 10rpx 15rpx;
	background-color: #3339F1;
	color:#ffffff;
}

</style>
