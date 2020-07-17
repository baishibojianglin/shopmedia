<template>
	<view class="content">
        <view class="vedio-con" v-for="value in adcase">
			<video class="vedio" :poster="value.ad_cover" :src="value.ad_video" controls></video>
			<view  class="vedio-text">泉塔大米</view>
		</view>
	</view>
</template>

<script>
	export default {
		components: {
			
		},
		data(){
			return {
               adcase:[] //广告案例列表
			}
		},
		onLoad(){
			
           this.get_case();
		},
		methods: {
			/**
			 * 获取广告案例
			 */
			get_case(){
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/get_case',
					method: 'GET',
					success: function(res) {
                        self.adcase=res.data;
					}
				})
			}
			
        }
	}
</script>

<style>
.vedio-con{
	width: 100%;
	height:175px;
}
.vedio{
	width: 90%;
	margin: 5px 5%;
	height: 100%;
}
.vedio-text{
	width: 100%;
	text-align: center;
}
</style>
