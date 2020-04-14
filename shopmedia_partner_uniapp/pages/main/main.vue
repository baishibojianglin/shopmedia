<template>
	<view class="content">
        <u-row>
            <u-col span="24" class="contain-map">
				<map 
					class="map"
					:scale='10'
					:latitude="latitude" 
					:longitude="longitude" 
					:markers='markers'
					:enable-satellite="false"
				>	
				</map>
			</u-col>
			<u-col span="8">城市：成都</u-col>
			<u-col span="8">总数：5023</u-col>
			<u-col span="8">可售：37</u-col>
        </u-row>         
	</view>
</template>

<script>
	import Vue from 'vue'
	import common from '@/common/common.js';
	import Row from '@/components/dl-grid/row.vue'
	import Col from '@/components/dl-grid/col.vue'
	Vue.component('u-row', Row); //<row>和<col>为H5原生标签, 不能直接用, 可起名<u-row>或者其他的
	Vue.component('u-col', Col);

	export default {
		data() {
				return {
						latitude:30.547441,
						longitude: 104.061738,
						markers:[],			
					   }
		},
		onLoad() {
              this.getmarkers();
		},
		methods: {
			//获取广告屏位置
			getmarkers(){
				let self=this;
				uni.request({
					url: this.$serverUrl+'api/getMarkers',
					header: /* getApp().globalData.commonHeaders */{
						'content-type': "application/json; charset=utf-8",
						'sign': common.sign(), // 验签，TODO：对参数如did等进行AES加密，生成sign如：'6IpZZyb4DOmjTaPBGZtufjnSS4HScjAhL49NFjE6AJyVdsVtoHEoIXUsjrwu6m+o'
						'version': getApp().globalData.version, // 应用大版本号
						'model': getApp().globalData.systemInfo.model, // 手机型号
						'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
						'did': getApp().globalData.did // 设备号
					},
					success: (res) => {
						res.data.data.forEach((value,index)=>{
							self.$set(self.markers,index,{
								title:value.device_id+' '+value.shopname,
								longitude:value.longitude,
								latitude:value.latitude
								});
						})
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
	.contain-map{
		margin: 0;
		padding: 0;
	}
	.map{
	   width: 100%;
	   height: 250px;
	   margin: 0;
	   padding: 0;
	}
</style>
