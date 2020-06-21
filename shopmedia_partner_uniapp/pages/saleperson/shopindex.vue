<template>
	<view class="content">
			<view class="sale-title main-color">— 我的邀请码 —</view>
			<view class="countcon">
				<view class="countcon-item">客户邀请码：<text class="uni-bold">{{sale_info.invitation_code}}</text></view>
				<view class="countcon-item">业务员邀请码：<text class="uni-bold">{{sale_info.son_invitation_code}}</text></view>
			</view>
			
			<view class="sale-title main-color">— 开拓店铺 —</view>
			<view class="uni-padding-wrap">
				<uni-grid :column="4" :square="false">
					<uni-grid-item>
						<navigator url="/pages/saleperson/salesman-shop-list">
							合计
							<view class="uni-bold">{{shop_number.total_shop_count}}</view>
						</navigator>
					</uni-grid-item>
					<uni-grid-item>
						<navigator url="/pages/saleperson/salesman-shop-list?shop_status=1">
							启用
							<view class="uni-bold">{{shop_number.enable_shop_count}}</view>
						</navigator>
					</uni-grid-item>
					<uni-grid-item>
						<navigator url="/pages/saleperson/salesman-shop-list?shop_status=2">
							待审核
							<view class="uni-bold">{{shop_number.pending_shop_count}}</view>
						</navigator>
					</uni-grid-item>
					<uni-grid-item>
						<navigator url="/pages/saleperson/salesman-shop-list?shop_status=3">
							驳回
							<view class="uni-bold">{{shop_number.reject_shop_count}}</view>
						</navigator>
					</uni-grid-item>
				</uni-grid>
			</view>
			
			<view class="sale-title main-color">— 我的资金 —</view>
			<view class="uni-padding-wrap uni-common-mb">
				<uni-grid :column="3" :square="false">
					<uni-grid-item>
						总收入
						<view class="uni-bold">{{sale_info.income}}</view>
					</uni-grid-item>
					<uni-grid-item>
						已提现
						<view class="uni-bold">{{sale_info.cash}}</view>
					</uni-grid-item>
					<uni-grid-item>
						余额
						<view class="uni-bold">{{sale_info.money}}</view>
					</uni-grid-item>
				</uni-grid>
			</view>
			<!-- <view class="countcon">
				<view class="countcon-item">总收入：<text class="uni-bold">{{sale_info.income}}</text></view>
				<view class="countcon-item">已提现：<text class="uni-bold">{{sale_info.cash}}</text></view>
				<view class="countcon-item">余额：<text class="uni-bold">{{sale_info.money}}</text></view>
			</view> -->
			
			<!-- 我的团队 s -->
			<view class="">
				<view class="countcon">
					<view class="countcon-item bg-qgray-color">
						<text>我的团队</text>
					</view>
				</view>
				<uni-card :is-shadow="true">
					<view>
						<view class="listcon">
							<view class="countcon-item">
								<text>电话</text>
							</view>
							<view class="countcon-item">
								<text>开拓店铺</text>
							</view>
						</view>	 
						<view class="listcon" v-for="item in salesmanList">
							<view class="countcon-item">
								<text>{{item.phone}}</text>
							</view>
							<view class="countcon-item">
								<text>{{item.total_count}}</text>
							</view>
						</view>
					</view>
				</uni-card>
			</view>
			<!-- 我的团队 e -->
			
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
				shop_number: [], //店铺数量
				salesmanList: [] // 下级业务员列表
			}
		},
		computed: mapState(['forcedLogin','hasLogin','userInfo','commonheader']),
		onLoad() {
			  this.getSaleInfo();
			  this.getSaleCount();
			  this.getSalesmanList();
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
						self.shop_number = res.data.data;
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
			},
			
			/**
			 * 获取下级业务员列表
			 */
			getSalesmanList() {
				let self=this;
				uni.request({
					url: this.$serverUrl + 'api/shopkeeper_salesman_list',
					data: {
						parent_id: this.userInfo.user_id
					},
					header: {
						'commonheader': this.commonheader,
						'access-user-token':this.userInfo.token
					},
					success: (res) => {
						if(res.data.status == 1){
							self.salesmanList = res.data.data;
						}
					}
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
