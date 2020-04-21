<template>
	<view class="content">
		   <view>
                 <video class="vedio-con"  autoplay="false" loop="false" controls="true" src="https://sustock-app.oss-cn-chengdu.aliyuncs.com/sustock-logo.mp4"></video>
		   </view>
			   
           <view >
			   <uni-grid  class="view-grid-con" :column="3">
			       <uni-grid-item>
			           <text class="text-grid-title">屏总量</text>
					   <text class="text-grid">50000+</text>
			       </uni-grid-item>
			       <uni-grid-item>
			           <text class="text-grid-title">覆盖城市</text>
					   <text class="text-grid">7</text>
			       </uni-grid-item>
			       <uni-grid-item>
			           <text class="text-grid-title">服务商家</text>
					   <text class="text-grid">80000+</text>
			       </uni-grid-item>
			   </uni-grid>
			</view>


																					  
	</view>
</template>

<script>
	import common from '@/common/common.js';
	import {mapState, mapMutations} from 'vuex';
	export default {
		data() {
			return {}
		},
		computed: mapState(['forcedLogin','hasLogin','userInfo','header']),
		onLoad() {
		},
		methods: {
			test(){
				uni.request({
					url: this.$serverUrl + 'api/login',
					data: {
						phone: this.phone,
						password: this.password
					},
					method: 'PUT',
					success: function(res) {
							if (res.data.status == 1) {
								let userInfo = res.data.data;
								// TODO：使用vuex管理登录状态时开启
								self.login(userInfo);
								//跳转到首页
								uni.reLaunch({
									url: '../main/main',
								});

							} 
					}
				})
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
.vedio-con{
	width: 100%;
}
.view-grid-con{
	margin: 0px 5px;
}
.text-grid-title{
	margin-top: 10px;
}
.text-grid{
	line-height: 80px;
	font-weight: bolder;
	font-size: 17px;
	color:#504AF2;
}
</style>
