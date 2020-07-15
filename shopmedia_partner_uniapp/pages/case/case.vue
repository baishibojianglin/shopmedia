<template>
	<view>
        <uni-card  :is-shadow='true'>
        	<view class="tvcon">
        		<video class="vedio-con" src="https://sustock-app-test.oss-cn-chengdu.aliyuncs.com/%E6%B3%89%E5%A1%94%E5%A4%A7%E7%B1%B3.mp4" :autoplay="false" :initial-time='2'  :loop="false" :controls="true"></video>
        		<view class="vedio-logo text-center">
        			 黑龙江泉塔大米 
        		</view>
        	</view>
        </uni-card>
		
        <uni-card  :is-shadow='true'>
        	<view class="tvcon">
        		<video class="vedio-con" src="https://sustock-app-test.oss-cn-chengdu.aliyuncs.com/%E9%87%91%E6%B2%99%E6%B1%9F%E6%9C%89%E6%9C%BA%E9%B1%BC.mp4" :autoplay="false"  :loop="false" :controls="true"></video>
        		<view class="vedio-logo text-center">
        			 金沙江有机鱼 
        		</view>
        	</view>
        </uni-card>	
			
        <uni-card  :is-shadow='true'>
        	<view class="tvcon">
        		<video class="vedio-con" src="https://sustock-app-test.oss-cn-chengdu.aliyuncs.com/%E6%83%A0%E6%B3%B0%E7%A7%91%E6%8A%80.mp4" :autoplay="false"  :loop="false" :controls="true"></video>
        		<view class="vedio-logo text-center">
        			 惠泰农业科技 
        		</view>
        	</view>
        </uni-card>			
	</view>
</template>

<script>
    import {mapState} from 'vuex';
	export default {
		data() {
			return {

			};
		},
		computed: {
			...mapState(['hasLogin', 'forcedLogin', 'userInfo', 'commonheader'])
		},
		onLoad() {

		},
		onNavigationBarButtonTap(e) {
			this.$common.actionSheetTap();
		},
		methods: {


		}
	};
</script>

<style>
.tvcon{
	width: 100%;
}
.vedio-con{
	width: 100%;
}
</style>
