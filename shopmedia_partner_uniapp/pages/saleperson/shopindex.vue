<template>
	<view class="content">
			<view class="sale-title main-color">— 我的邀请码 —</view>
			<view class="countcon">
				<view class="countcon-item">客户邀请码：<text class="uni-bold">{{sale_info.invitation_code}}</text></view>
				<view class="countcon-item">业务员邀请码：<text class="uni-bold">{{sale_info.son_invitation_code}}</text></view>
			</view>
			
			<view class="sale-title main-color">— 开拓店铺 —</view>
			<view class="countcon">
				<view class="countcon-item">店铺：<text class="uni-bold">{{sale_number}} 个</text></view>
				<view class="countcon-item">总收入：<text class="uni-bold">{{sale_info.income}}</text></view>
			</view>
			<view class="countcon">
				<view class="countcon-item">已提现：<text class="uni-bold">{{sale_info.cash}}</text></view>
				<view class="countcon-item">余额：<text class="uni-bold">{{sale_info.money}}</text></view>
			</view>
						
			<view class="uni-padding-wrap uni-common-mt">
					<navigator url="./shop" hover-class="none">
						<button class="bg-main-color color-white">	
							开展业务 	
						</button>
					</navigator>
			</view>				
							
	</view>
</template>

<script>
	import common from '@/common/common.js';
	import {mapState, mapMutations} from 'vuex';
	export default {
		data() {
				return {
						salecount:0,//合作屏数量
						devicelist:[],//设备列表
						sale_info:{} ,//业务员基本信息
						sale_number:0 //广告机数量
					   }
		},
		computed: mapState(['forcedLogin','hasLogin','userInfo','commonheader']),
		onLoad() {
			  this.getSaleInfo();
			  this.getSaleCount();
		},
		onNavigationBarButtonTap(e) {
			this.$common.actionSheetTap();
		},
		methods: {
			
			/**
			 * 获取业务员销售屏数量
			 */
			getSaleCount(){
				let self=this;
				uni.request({
					url: this.$serverUrl+'api/shop_count',
					data: {
						user_id:this.userInfo.user_id,
						role_id:6
					},
					method:'POST',
					header: {
						'commonheader': this.commonheader,
						'access-user-token':this.userInfo.token
					},
					success: (res) => {
                         self.sale_number=res.data.data;
					}
				});			
			},			
			
			
			
			
			/**
			 * 获取业务员信息
			 */
			getSaleInfo(){
				let self=this;
				uni.request({
					url: this.$serverUrl+'api/sale_info',
					data: {
						user_id:this.userInfo.user_id,
						role_id:6
					},
					method:'POST',
					header: {
						'commonheader': this.commonheader,
						'access-user-token':this.userInfo.token
					},
					success: (res) => {
                       self.sale_info=res.data.data;
					}
				});			
			},

			
			//跳转到设备详情
			toDevice(device_id){
				uni.navigateTo({
				    url:'device?device_id='+device_id
				});
			}
		}
	}
</script>

<style>
.sale-title{
	line-height: 50px;
}
.content{
	margin: 0;
	padding: 0;
	text-align: center;
}
.map{
	width: 100%;
	height: 250px;
}
.countcon{
	display: flex;
	flex-direction:row;
	justify-content:center;
	border-bottom: 1px solid #E7E6DF;
}
.countcon-item{
    flex: 1;
	line-height: 40px;
}
.listcon{
	display: flex;
	flex-direction:row;
	justify-content:center;
	border-bottom: 1px solid #E7E6DF;
}
.listcon-item-1{
	line-height: 40px;
	flex-basis: 70px;
}
.listcon-item-2{
	flex: 1;
	line-height: 40px;
}
.listcon-item-3{
	line-height: 40px;
	flex-basis: 30px;
}
.icon-size{
	font-size: 16px;
}
</style>
