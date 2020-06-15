<template>
	<view class="content">
	

		<view>
			<uni-card style="background-color:#ECECEC;" :is-shadow='true'>
				<uni-grid class="view-grid-con totalcontentbg" :column="3">
					<uni-grid-item>
						<text class="text-grid-title">广告屏</text>
						<text class="text-grid" @click="toDeviceList">{{totaldata.addevice}}</text>
					</uni-grid-item>
					<uni-grid-item>
						<text class="text-grid-title">覆盖城市</text>
						<text class="text-grid">{{totaldata.city}}</text>
					</uni-grid-item>
					<uni-grid-item>
						<text class="text-grid-title">服务商家</text>
						<text class="text-grid">{{totaldata.shop}}</text>
					</uni-grid-item>
				</uni-grid>
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

			}
		},
		computed: mapState(['forcedLogin', 'hasLogin', 'userInfo', 'commonheader']),
		onLoad() {
			//获取城市数据
			this.getcity();
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
	.content {
		margin: 0;
		padding: 0;
		text-align: center;
	}

	.vedio-con {
		width:100%;
		margin:0px;
		padding: 0px;
		border:15px solid #57585C;
		border-bottom: 30px solid #57585C;
		box-sizing: border-box;
		border-radius: 7px;
		height: 205px;
	}

	.view-grid-con {
		margin: 0px 5px;
	}

	.text-grid-title {
		margin-top: 10px;
	}

	.text-grid {
		line-height: 80px;
		font-weight: bolder;
		font-size: 17px;
		color: #409EFF;
	}

	.user-title {
		line-height: 50px;
		font-size: 16px;
	}

	.iconbg {
		height: 50px;
		width: 50px;
		border-radius: 50px;
		border: 1px solid #F3F3F3;
		line-height: 50px;
		display: inline-block;
	}
	.iconbg-ad{
		height: 45px;
		width: 45px;
		border-radius: 45px;
		border: 1px solid #F3F3F3;
		line-height: 45px;
		display: inline-block;
		background-color: #409EFF;
	}
	.iconbg-parnter{
		height: 45px;
		width: 45px;
		border-radius: 45px;
		border: 1px solid #F3F3F3;
		line-height: 45px;
		display: inline-block;
		background-color: #EB795B;
	}
	.iconbg-sale{
		height: 45px;
		width: 45px;
		border-radius: 45px;
		border: 1px solid #F3F3F3;
		line-height: 45px;
		display: inline-block;
		background-color: #C9D269;
	}
	.iconbg-notice{
		height: 45px;
		width: 45px;
		border-radius: 45px;
		border: 1px solid #F3F3F3;
		line-height: 45px;
		display: inline-block;
		background-color: #F7D810;
	}
	.iconbg-advice{
		height: 45px;
		width: 45px;
		border-radius: 45px;
		border: 1px solid #F3F3F3;
		line-height: 45px;
		display: inline-block;
		background-color:#7EECF9;
	}
	.navcon {
		display: flex;
		flex-flow: row wrap;
		justify-content: left;
		text-align: center;
	}

	.navcon-item {
		flex: 0 0 33%;
		padding: 10px 0;
	}
	.tvcon{
		width:100%;
		position: relative;
	}
	.totalcontentbg{
		background-color: #fff;
	}
	.vedio-logo{
		width: 100%;
		position: absolute;
		bottom:10px;
		text-align: center;
		color:#fff;
	}
</style>
