<template>
	<view class="content">
		<!-- <view>
			<map 
				class="map"
				:scale='9'
				:latitude="latitude" 
				:longitude="longitude" 
				:markers='markers'
				:enable-satellite="false"
			>
			</map>
		</view> -->
		<view class="sale-title main-color">— 我的邀请码 —</view>
		<view class="countcon">
			<view class="countcon-item">客户邀请码：<text class="uni-bold">{{sale_info.invitation_code}}</text></view>
			<view class="countcon-item">业务员邀请码：<text class="uni-bold">{{sale_info.son_invitation_code}}</text></view>
		</view>
		
		<view class="sale-title main-color">— 我的战绩 —</view>
		<view class="countcon">
			<view class="countcon-item">销售：<text class="uni-bold">{{sale_number}} 台</text></view>
			<view class="countcon-item">总收入：<text class="uni-bold">{{sale_info.income}}</text></view>
		</view>
		<view class="countcon">
			<view class="countcon-item">已提现：<text class="uni-bold">{{sale_info.cash}}</text></view>
			<view class="countcon-item">余额：<text class="uni-bold">{{sale_info.money}}</text></view>
		</view>
		
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
						<view class="listcon-item-1">
							<text>电话</text>
						</view>
						<view class="listcon-item-2">
							<text>今日销售（台）</text>
						</view>
						<view class="listcon-item-1">
							<text>累计销售（台）</text>
						</view>
					</view>
					<view class="listcon" v-for="item in salesmanList">
						<view class="listcon-item-1">
							<text>{{item.phone}}</text>
						</view>
						<view class="listcon-item-2">
							<text>{{item.today_count}}</text>
						</view>
						<view class="listcon-item-1">
							<text>{{item.total_count}}</text>
						</view>
					</view>
				</view>
			</uni-card>
		</view>
		<!-- 我的团队 e -->
		
		<view class="countcon">
			<view class="countcon-item bg-qgray-color">
				<text>可销售设备</text>
			</view>
		</view>
		<uni-card :is-shadow="true">
			<view>
				<view class="listcon">
					<view class="listcon-item-1">
						<text>屏号</text>
					</view>
					<view class="listcon-item-2">
						<text>店名</text>
					</view>
					<view class="listcon-item-1">
						<text>合作价</text>
					</view>
					<view class="listcon-item-3">
						<text v-show="false">详情</text>
					</view>
				</view>	 
				<view class="listcon" v-for="value in devicelist" @click="toDevice(value.device_id)">
						<view class="listcon-item-1">
							<text>{{value.device_id}}</text>
						</view>
						<view class="listcon-item-2">
							<text>{{value.shop_name}}</text>
						</view>
						<view class="listcon-item-1">
							<text class="">¥{{value.sale_price/2}}</text>
						</view>
						<view class="listcon-item-3">
							<text class="icon icon-size">&#xe6a2;</text>
						</view>
				</view>	
			</view>
		</uni-card>
	</view>
</template>

<script>
	import common from '@/common/common.js';
	import {mapState, mapMutations} from 'vuex';
	export default {
		data() {
			return {
				latitude:30.657420,//纬度
				longitude: 104.065840,//经度
				markers:[],	//地图图标
				salecount:0,//合作屏数量
				devicelist:[],//设备列表
				sale_info:{} ,//业务员基本信息
				sale_number:0, //广告机数量
				salesmanList: [] // 下级业务员列表
			}
		},
		computed: mapState(['forcedLogin','hasLogin','userInfo','commonheader']),
		onLoad() {
			this.getmarkers();
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
					url: this.$serverUrl+'api/sale_count',
					data: {
						user_id:this.userInfo.user_id,
						role_id:4
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
						role_id:4
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
			
			/**
			 * 获取广告屏位置列表
			 */
			getmarkers(){
				let self=this;
				uni.request({
					url: this.$serverUrl+'api/device_list',
					header: {
						'commonheader': this.commonheader,
						'access-user-token':this.userInfo.token
					},
					success: (res) => {
						if(res.data.data){
							self.salecount=res.data.data.length;//可合作数量
							self.devicelist=res.data.data; //可合作设备列表
							res.data.data.forEach((value,index)=>{
								self.$set(self.markers,index,{
									title:value.device_id+' '+value.shop_name,
									longitude:value.longitude,
									latitude:value.latitude
									});
							})							
						}

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
					url: this.$serverUrl + 'api/partner_salesman_list',
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
