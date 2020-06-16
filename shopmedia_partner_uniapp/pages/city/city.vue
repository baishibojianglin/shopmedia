<template>
	<view class="content">
	

		<view>

		</view>

		
	</view>
</template>

<script>
	import common from '@/common/common.js';
	import {mapState, mapMutations} from 'vuex';
	
	export default {
		data() {
			return {

			}
		},
		computed: mapState(['forcedLogin', 'hasLogin', 'userInfo', 'commonheader']),
		onLoad() {
			//获取城市数据
			this.getCity();
		},
		methods: {
			/**
			 * 获取广告屏城市
			*/
		   getCity(){
			   let self=this;
			   //获取角色信息
			   	uni.request({
			   		url: this.$serverUrl + 'api/get_fix_city',
			   		header: {
			   			'commonheader': this.commonheader,
			   			'access-user-token': this.userInfo.token
			   		},
			   		method: 'GET',
			   		success: function(res) {
						
			   		}
			   	})
			   
		   }

		}
	}
</script>

<style>

</style>
