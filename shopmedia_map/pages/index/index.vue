<template>
	<view class="content">
		 <div class="contain-map">
                <map 
				  class="map"
				  :scale='10'
				 :latitude="latitude" 
				 :longitude="longitude" 
				 :markers='markers'
				 :enable-satellite="true"
				 >
                </map>
		 </div>     
	</view>
</template>

<script>
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
           getmarkers(){
			   let self=this;
			   uni.request({
			       url: this.$url+'getMarkers',
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
	.content {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
	}
	.contain-map{
		position: fixed;
		top:44px;
		bottom:0;
		left: 0;
		right:0;
	}
	.map{
		width: 100%;
		height: 100%;
	}
</style>
