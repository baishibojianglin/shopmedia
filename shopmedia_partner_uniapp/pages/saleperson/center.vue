<template>
	<view class="content">
		<view class="uni-center">
			<image class="headimg"  mode="aspectFit" :src="logourl"></image>
		</view>

		
		<view class="infobasic">
			<text class="infobasic-item text-left">总收入：{{income}}</text>
			<text class="infobasic-item text-right">余额：{{money}}</text>
		</view>					
		<view class="infobasic">
			<view class="infobasic-item uni-center" v-if="role.device">
				<text>推智能屏</text>
				<br/>
				<text class="icon" style="color: #EA4C89;">&#xe723;</text>
				<br/>
				<text>{{device_money}}</text>
			</view>
			<view class="infobasic-item uni-center" v-if="role.shop">
				<text>推小店</text>
				<br/>
				<text class="icon" style="color: #504AF2;">&#xe726;</text>
				<br/>
				<text>{{shop_money}}</text>
			</view>
			<view class="infobasic-item uni-center" v-if="role.ad">
				<text>推广告</text>
				<br/>
				<text class="icon" style="color: #38E084;">&#xe725;</text>
				<br/>
				<text>{{ad_money}}</text>
			</view>
		</view>
		
		<view class="uni-center title">我的业务范围</view>
		
		<view class="uni-padding-wrap">
                <navigator url="./index" hover-class="navigator-hover">
                    <button class="butred workbutton">智能屏业务</button>
                </navigator>
				
				<navigator url="./index" hover-class="navigator-hover">
				    <button class="butblue workbutton">店铺业务</button>
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
				  income:'',//总收入
				  money:'',//余额
				  device_money:'',//推广广告机收入
				  shop_money:'',//推广店铺收入
				  ad_money:'0.00',//推广广告收入
                  logourl: 'https://sustock-app.oss-cn-chengdu.aliyuncs.com/logoimage.png',
				  role:{
					  device:false,
					  ad:false,
					  shop:false
				  }
				}
		},
		computed: mapState(['forcedLogin','hasLogin','userInfo','commonheader']),
		onLoad(){

		},
		onShow(){
			//角色
			this.is_role();
			this.getMoney()
		},
		methods: {
			/**
			 * 业务员收入
			 */
			getMoney(){
				let self=this;
				uni.request({
					url: this.$serverUrl + 'api/getMoney',
					data: {
						user_id:this.userInfo.user_id
					},
					header: {
						'commonheader': this.commonheader,
						'access-user-token':this.userInfo.token
					},
					method: 'GET',
					success: function(res) {
						if(res.data.status==1){
                            self.income=res.data.data.income;  
							self.money=res.data.data.money; 
							let role_str=res.data.data.role_ids;
							let role_array=role_str.split(',');		
							role_array.forEach((value,index)=>{
								 switch(parseInt(value)) {
									  case 4:
										 self.role.device=true;
										 self.getMoneyDevice();
										 break;
									  case 5:
										 self.role.ad=true;
										 break;
									  case 6:
										 self.role.shop=true;
										 self.getMoneyShop();
										 break;
									  default:
										//
								 } 
							})
						}else{
							uni.showToast({
								icon:'none',
							    title: '网络繁忙，稍后重试',
							    duration: 2000
							});
						}
					}
				})
			},
			
			/**
			 * 获取业务员推广广告屏收入
			 */
			getMoneyDevice(){
				let self=this;
				uni.request({
					url: this.$serverUrl + 'api/getMoneyDevice',
					data: {
						user_id:this.userInfo.user_id
					},
					header: {
						'commonheader': this.commonheader,
						'access-user-token':this.userInfo.token
					},
					method: 'GET',
					success: function(res) {
						if(res.data.status==1){
                           self.device_money=res.data.data;
						}else{
							uni.showToast({
								icon:'none',
							    title: '网络繁忙，稍后重试',
							    duration: 2000
							});
						}
					}
				})				
			},
	
	/**
	 * 获取业务员推广店铺收入
	 */
	getMoneyShop(){
		let self=this;
		uni.request({
			url: this.$serverUrl + 'api/getMoneyShop',
			data: {
				user_id:this.userInfo.user_id
			},
			header: {
				'commonheader': this.commonheader,
				'access-user-token':this.userInfo.token
			},
			method: 'GET',
			success: function(res) {
				if(res.data.status==1){
	               self.shop_money=res.data.data;
				}else{
					uni.showToast({
						icon:'none',
					    title: '网络繁忙，稍后重试',
					    duration: 2000
					});
				}
			}
		})				
	},
	
	
	
	
		   
		   /**
		    * 判断用户角色
		    */
		   is_role(){
		   	let self=this;
		   	//获取角色信息
		   	uni.request({
		   		url: this.$serverUrl + 'api/getRole',
		   		data: {
		   			user_id:this.userInfo.user_id,
		   		},
		   		header: {
		   			'commonheader': this.commonheader,
		   			'access-user-token':this.userInfo.token
		   		},
		   		method: 'POST',
		   		success: function(res) {
		   			if(res.data.status==1){
		   				//处理角色信息									
		   				let role_str=res.data.data.role_ids;
		   				let role_array=role_str.split(',');		
		   				role_array.forEach((value,index)=>{
		   					 switch(parseInt(value)) {
		   						  case 4:
		   							 self.role.device=true;
		   							 break;
		   						  case 5:
		   							 self.role.ad=true;
		   							 break;
		   						  case 6:
		   							 self.role.shop=true;
									 break;
		   					 } 
		   				})
		   			}else{
		   				uni.showToast({
		   					icon:'none',
		   				    title: '网络繁忙，稍后重试',
		   				    duration: 2000
		   				});
		   			}
		   		}
		   	})				
		   
		   }
		   	
		   
		   
		   
		   
		   
		}

	}
</script>

<style>
.headimg{
	width: 60px;
	height: 60px;
	border-radius: 60px;
	background-color: #FAFAFA;
	padding: 15px;
	margin-top: 15px;
}
.infobasic{
	display: flex;
	flex-direction: row;
	justify-content: center;
	border-bottom: 1px solid #E3E0D5;
	padding:15px 10px;
}
.infobasic-item{
	flex: 1;
}
.title{
	margin: 20px 0;
}
.butred{
	background-color: #F28EB4;
	color:#fff;
}
.butgreen{
	background-color: #49E28F;
	color:#fff;
}
.butblue{
	background-color: #3F45F2;
	color:#fff;	
}
.workbutton{
	margin-bottom: 20px;
}
</style>
