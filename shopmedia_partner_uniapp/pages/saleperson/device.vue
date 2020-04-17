<template>
	<view class="content">
				
        <view>
			<swiper class="swiper" :autoplay="true" :interval="3000" :circular="true" :indicator-dots="true" indicator-active-color="#fff">
				<swiper-item v-for="value in imglist">
					<view class="swiper-item">
			           <image :src="value"></image>						
					</view>
				</swiper-item>
			</swiper>
		</view>
                					
						
							
	</view>
</template>

<script>
	import common from '@/common/common.js';
	export default {
		data() {
				return {
                     device_id:0 ,//设备id
					 datalist:{}, //设备信息列表
					 imglist:[] //实景图列表
				}
		},
		onLoad(options) {
           //获取设备id
           this.device_id=options.device_id;
		   //获取设备信息
		   this.deviceDetail();
		},
		methods: {
			//获取设备详细信息
			deviceDetail(){
				let self=this;
				uni.request({
					url: this.$serverUrl+'api/DeviceDetail',
					data:{device_id:self.device_id},
					method:'POST',
					header: /* getApp().globalData.commonHeaders */{
						'content-type': "application/json; charset=utf-8",
						'sign': common.sign(), // 验签，TODO：对参数如did等进行AES加密，生成sign如：'6IpZZyb4DOmjTaPBGZtufjnSS4HScjAhL49NFjE6AJyVdsVtoHEoIXUsjrwu6m+o'
						'version': getApp().globalData.version, // 应用大版本号
						'model': getApp().globalData.systemInfo.model, // 手机型号
						'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
						'did': getApp().globalData.did // 设备号
					},
					success: (res) => {
                       console.log(res.data)
					   self.datalist=res.data.data; //赋值
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
					}
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
</style>
