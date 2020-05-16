<template>
	<view class="content">
		<view class="uni-center">
			<image class="headimg"  mode="aspectFit" :src="logourl"></image>
		</view>

		
		<view class="infobasic" style="padding-bottom: 35px;">
			<text class="infobasic-item text-left">总收入：{{income}}</text>
			<text class="infobasic-item text-center">已提现：{{cash}}</text>
			<text class="infobasic-item text-right">余额：{{money}}</text>
		</view>					
		<view class="infobasic positon-relative" style="padding-top:35px; border-bottom:none;">
			<view class="money-detail-title">业务详情</view>
		</view> 
		
			
		<view class="uni-padding-wrap text-center">
				<uni-grid class="view-grid-con" :column="3" :highlight="false">
					
					        <!--销售智能屏业务 s-->
							<uni-grid-item>
								<text class="text-grid-title">销售智能屏</text>
								<text class="icon main-color">&#xe723;</text>
								<navigator url="./index" v-if="role.device" hover-class="none">
									<button class="text-grid work-button bg-main-color color-white">	
										进入 	
									</button>
								</navigator>
								<navigator  url="./apply?role_id=4&code=188888" hover-class="none"  v-if="!role.is_device">
									<button class="text-grid work-button bg-second-color color-white">	
										申请开通 
									</button>
								</navigator>						
								<button v-if="role.device_words" class="text-grid work-button bg-gray-color color-white">	
									{{role.device_words}} 	
								</button>							
							</uni-grid-item>
					       <!--销售智能屏业务 e-->
						   
						   
						   
						<!--开拓店铺业务 s-->
							<uni-grid-item>
								<text class="text-grid-title">开拓店铺</text>
								<text class="icon" style="color: #EA4C89;">&#xe726;</text>
								<navigator url="./shop" v-if="role.shop" hover-class="none">
									<button class="text-grid work-button bg-main-color color-white">	
										进入 	
									</button>
								</navigator>
								<navigator url="./apply?role_id=6&code=199999"  v-if="!role.is_shop"  hover-class="none">
									<button class="text-grid work-button bg-second-color color-white">	
										申请开通  	
									</button>
								</navigator>						
								<button v-if="role.shop_words" class="text-grid work-button bg-gray-color color-white">	
									{{role.shop_words}} 	
								</button>							
							</uni-grid-item>
						<!--开拓店铺业务 e-->   
						   

							
						<!--推广广告业务 s-->	
						<uni-grid-item>
							<text class="text-grid-title">推广广告</text>
							<text class="icon" style="color: #38E084;">&#xe725;</text>
							<navigator url="./index" v-if="role.ad" hover-class="none">
								<button class="text-grid work-button bg-main-color color-white">	
									进入 	
								</button>
							</navigator>
							<navigator  v-if="!role.is_ad" url="./apply?role_id=5&code=166666" hover-class="none">
								<button class="text-grid work-button bg-second-color color-white">	
									申请开通  	
								</button>
							</navigator>						
							<button v-if="role.ad_words" class="text-grid work-button bg-gray-color color-white">	
								{{role.ad_words}} 	
							</button>							
						</uni-grid-item>
						<!--推广广告业务 e-->	
							
							
							
							
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
				  income:'',//总收入
				  cash:'',//提现
				  money:'',//余额
                  logourl:'/static/img/logoheadimg.png',
				  role:{
					  is_device:false,
					  device:false,
					  device_words:'',
					  is_ad:false,
					  ad:false,
					  ad_words:'',
					  is_shop:false,
					  shop:false,
					  shop_words:''
				  }
				}
		},
		computed: mapState(['forcedLogin','hasLogin','userInfo','commonheader']),
		onLoad(){

		},
		onNavigationBarButtonTap(e) {
			this.$common.actionSheetTap();
		},
		onShow(){
			//角色
			this.is_role();
			//收入
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
							self.cash=res.data.data.cash;
							self.money=res.data.data.money; 
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
		   		url: this.$serverUrl + 'api/get_user_role',
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
								     self.role.is_device=true;
								     self.getRoleStatus(4);
		   							 break;
		   						  case 5:
								     self.role.is_ad=true;
								     self.getRoleStatus(5);
		   							 break;
		   						  case 6:
								     self.role.is_shop=true;
								     self.getRoleStatus(6);
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
		   
		   },
		 
		/**
		 * 获取角色状态
		 */ 
		 getRoleStatus(role_id){
		   	let self=this;
		   	//获取角色信息
		   	uni.request({
		   		url: this.$serverUrl + 'api/get_role_status',
		   		data: {
		   			user_id:this.userInfo.user_id,
					role_id:role_id
		   		},
		   		header: {
		   			'commonheader': this.commonheader,
		   			'access-user-token':this.userInfo.token
		   		},
		   		method: 'GET',
		   		success: function(res) {
								//广告屏业务员
								if(role_id==4){
										if( res.data.data.status==1 ){
											  self.role.device=true;
											  self.role.device_words='';
											  return false;
										}else{
											  self.role.device=false;
											  switch(res.data.data.status) {
														  case 0:
															 self.role.device_words='停用';
															 break;
														  case 2:
															 self.role.device_words='审核中';
															 break;
														  case 3:
															 self.role.device_words='停用';
															 break;
											  } 
											  return false;
										}									
								}

								
								//广告业务员
								if(role_id==5){
									if( res.data.data.status==1 ){
										  self.role.ad=true;
										  self.role.ad_words='';
										  return false;
									}else{
										  self.role.ad=false;
										  switch(res.data.data.status) {
													  case 0:
														 self.role.ad_words='停用';
														 break;
													  case 2:
														 self.role.ad_words='审核中';
														 break;
													  case 3:
														 self.role.ad_words='停用';
														 break;
										  } 
										  return false;
									}	
								}
	
								//店铺业务员
								if(role_id==6){
									if( res.data.data.status==1 ){
										  self.role.shop=true;
										  self.role.shop_words='';
										  return false;
									}else{
										  self.role.shop=false;
										  switch(res.data.data.status) {
													  case 0:
														 self.role.shop_words='停用';
														 break;
													  case 2:
														 self.role.shop_words='审核中';
														 break;
													  case 3:
														 self.role.shop_words='停用';
														 break;
										  } 
										  console.log(self.role.shop_words);
										  return false;
									}	
								}
	
	
											
											
											
					
		   		}
		   	})							 
		 } , 
		   

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
							    title: '网络繁忙2，稍后重试',
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
	




		   
		   
		   
		}

	}
</script>

<style>
.headimg{
	width: 50px;
	height: 50px;
	border-radius: 50px;
	background-color: #F2F2F2;
	padding: 10px;
	margin-top: 15px;
}
.infobasic{
	display: flex;
	flex-direction: row;
	justify-content: center;
	border-bottom: 1px solid #409EFF;
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
.mtop{
	margin-top: 20px;
}
.money-detail-title{
	position: absolute;
	z-index: 2;
	padding: 3px 15px;
	background-color: #fff;
	border:1px solid #409EFF;
	border-radius: 5px;
	top:-15px;
}
.work-button{
	font-size: 12px;
	width: 90%;
}
</style>
