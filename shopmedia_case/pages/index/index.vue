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
			
		   //获取屏幕高度和宽度
		   
		   var winWidth = 0;
		   var winHeight = 0;
			//获取窗口宽度
			if (window.innerWidth)
		   winWidth = window.innerWidth;
			else if ((document.body) && (document.body.clientWidth))
		   winWidth = document.body.clientWidth;
		   //获取窗口高度
			if (window.innerHeight)
			winHeight = window.innerHeight;
		   else if ((document.body) && (document.body.clientHeight))
			winHeight = document.body.clientHeight;
			//通过深入Document内部对body进行检测，获取窗口大小
		   if (document.documentElement  && document.documentElement.clientHeight && document.documentElement.clientWidth)
			{
			winHeight = document.documentElement.clientHeight;
			winWidth = document.documentElement.clientWidth;
		    }
			document.getElementsByClassName("vedio").style.height=9/16*winWidth;

		   
		   console.log(winWidth)
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
	height:180px;
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
