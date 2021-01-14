<template>
	<view class="content">
        <view class="vedio-con"  v-for="value in adcase">
			<video class="vedio" :style="vedio_css" :poster="value.ad_cover" :src="value.ad_video" controls object-fit="fill"></video>
			<view  class="vedio-text">{{value.ad_name}}</view>
		</view>
	</view>
</template>


<script>
	export default {
		components: {
			
		},
		data(){
			return {
				adcase:	[], // 广告案例列表
				vedio_css: {
					height: ''
				}
			}
		},
		onLoad(){
			// 获取窗口宽度和高度
			this.getWindowWidthAndHeight();

			// 获取广告案例列表
            this.get_case();
		},
		methods: {
			/**
			 * 获取窗口宽度和高度及广告视频高度
			 */
			getWindowWidthAndHeight() {
				/* 获取窗口宽度和高度 s */
				var winWidth = 0;
				var winHeight = 0;
				
				// #ifdef H5
				// 获取窗口宽度
				if (window.innerWidth)
					winWidth = window.innerWidth;
				else if ((document.body) && (document.body.clientWidth))
					winWidth = document.body.clientWidth;
				// 获取窗口高度
				if (window.innerHeight)
					winHeight = window.innerHeight;
				else if ((document.body) && (document.body.clientHeight))
					winHeight = document.body.clientHeight;
				// 通过深入Document内部对body进行检测，获取窗口大小
				if (document.documentElement  && document.documentElement.clientHeight && document.documentElement.clientWidth)
				{
					winHeight = document.documentElement.clientHeight;
					winWidth = document.documentElement.clientWidth;
				}
				// #endif
				
				// #ifdef APP-PLUS
				// 同样适用于H5端
				uni.getSystemInfo({
					success:function(res){
						winWidth = res.windowWidth;
					}
				})
				// #endif
				/* 获取窗口宽度和高度 e */
				
				// 获取广告视频高度
				this.vedio_css.height = 9/16 * winWidth + 'px';
			},
			
			/**
			 * 获取广告案例列表
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
		margin-top: 10px;
	}
	.vedio{
		width: 90%;
		margin:0 5%;
		height: 100%;
		padding: 0;
	}
	.vedio-text{
		width: 100%;
		text-align: center;
		font-size: 18px;
	}
</style>
