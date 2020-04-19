<template>
	<view class="content">
		   <view>
                 <video class="vedio-con"  autoplay="true" loop="false" controls="true" src="https://sustock-app.oss-cn-chengdu.aliyuncs.com/sustock-logo.mp4"></video>
		   </view>
			   
           <view >
			   <uni-grid  class="view-grid-con" :column="3">
			       <uni-grid-item>
			           <text>屏总量</text>
					   <text class="text-grid">50000+</text>
			       </uni-grid-item>
			       <uni-grid-item>
			           <text class="text">覆盖城市</text>
					   <text class="text-grid">7</text>
			       </uni-grid-item>
			       <uni-grid-item>
			           <text class="text">服务商家</text>
					   <text class="text-grid">80000+</text>
			       </uni-grid-item>
			   </uni-grid>
			</view>

																					  
	</view>
</template>

<script>
	import common from '@/common/common.js';
	export default {
		data() {
			return {

				}
		},
		onLoad() {

		},
		methods: {
			
		}

	}
</script>

<style>
.content{
	margin: 0;
	padding: 0;
	text-align: center;
}
.vedio-con{
	width: 100%;
}
.view-grid-con{
	margin: 0px 5px;
}
.text-grid{
	line-height: 80px;
	font-weight: bolder;
	font-size: 17px;
	color:#504AF2;
}
</style>
