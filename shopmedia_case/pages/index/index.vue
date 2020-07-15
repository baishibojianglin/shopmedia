<template>
	<view class="content">
        <view class="vedio-con">
			<video class="vedio" poster="https://sustock-app-test.oss-cn-chengdu.aliyuncs.com/03f78905d88fe2624be60d3c7d2304d9115206942_2_20171103011750470.jpg" src="https://sustock-app-test.oss-cn-chengdu.aliyuncs.com/%E6%B3%89%E5%A1%94%E5%A4%A7%E7%B1%B3.mp4" controls></video>
			<view  class="vedio-text">泉塔大米</view>
		</view>
	</view>
</template>

<script>
	export default {
		components: {
			
		},
		data() {
			return {

			}
		},
		onLoad(){

		},
		methods: {

			
        }
	}
</script>

<style>
.vedio-con{
	width: 100%;
	height: 200px;
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
