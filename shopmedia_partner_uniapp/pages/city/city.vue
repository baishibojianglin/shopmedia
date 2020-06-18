<template>
	<view class="content">
		<view>
				<uni-grid :column="3">
					<uni-grid-item v-for="value in city"> 
						<text class="text-grid">{{value.region_name}}</text>
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
			return {
                city:[] //城市列表
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
						self.city=res.data;
			   		}
			   	})
			   
		   }

		}
	}
</script>

<style>
 .text-grid{
	 text-align: center;
	 line-height: 120px;
 }
</style>
