<template>
	<view class="content">
		<view>
			 <uni-card  :is-shadow='true'  v-for="(value,index) in adlist">
				<uni-list>
					<uni-list-item :title="value.ad_name" :show-arrow="false" :show-badge="true" :badge-text="adcate[value.ad_cate_id]"></uni-list-item>
				</uni-list>
			</uni-card>
		</view>
	</view>
</template>

<script>
	import common from '@/common/common.js';
	import {mapState, mapMutations} from 'vuex';
	
	export default {
		data() {
			return {
				adlist:{},//广告列表
				adcate:{} //广告类别
			}
		},
		computed: mapState(['forcedLogin', 'hasLogin', 'userInfo', 'commonheader']),
		onLoad() {
			//获取广告信息
            this.getAd();
			this.getAdCate();
		},
		methods: {
			 /**
			  * 获取广告类别
			 */
			 getAdCate(){
				   let self=this;
				   uni.request({
						url: this.$serverUrl + 'api/get_ad_cate',
						header: {
							'commonheader': this.commonheader,
							'access-user-token': this.userInfo.token
						},
						method: 'GET',
						success: function(res) {
							self.adcate=res.data;
						}
					})
						   
			},
			
			
			/**
			 * 获取广告屏城市
			*/
		   getAd(){
			   let self=this;
			   uni.request({
			   		url: this.$serverUrl + 'api/get_ad',
			   		header: {
			   			'commonheader': this.commonheader,
			   			'access-user-token': this.userInfo.token
			   		},
			   		method: 'GET',
			   		success: function(res) {
						self.adlist=res.data;
			   		}
			   	})
			   
		   }   
		   
		   
		   

		}
	}
</script>

<style>

</style>
