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
					:enable-satellite="true"
				>	
				</map>
			</u-col>
        </u-row>         
	</view>
</template>

<script>
	import Vue from 'vue'
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
			       url: this.$serverUrl+'getMarkers',
				   header: getApp().globalData.commonHeaders,
			       success: (res) => {
					    res.data.data.forEach((value,index)=>{
							self.$set(self.markers,index,{
								title:'广告屏：'+value.device_id,
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
