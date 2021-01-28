<template>
	<view class="uni-page-body">
		<!-- 顶部视频 s -->
		<view class="uni-padding-wrap uni-center" style="width: auto;height: 137.1vim;background-image: linear-gradient(#4C85FC, #ffffff);padding: 10px 15px;">
			<view style="position: relative;z-index: 1;">
				<video class="" src="https://sustock-website.oss-cn-chengdu.aliyuncs.com/company1.mp4" :autoplay="true" :loop="false" :controls="true" style="width: 92vmin;height: 51.75vmin;border-radius: 5px;"></video><!-- https://sustock-app-test.oss-cn-chengdu.aliyuncs.com/company.mp4 -->
			</view>
		</view>
		<!-- 顶部视频 e -->

		<!-- 店通服务 s -->
		<view class="uni-padding-wrap uni-center" style="margin-top: 91.9vim">
			<view class="text-left">
				<text class="uni-h4 uni-bold" style="color: #333333;">店通服务</text>
			</view>
			
			<uni-card :isFull="true" style="border: 0;">
				<uni-grid :column="3" :showBorder="false">
					<uni-grid-item>
						<view @click="usead()">
							<image src="../../static/img/home_page/ad_icon.png" style="width: 65.5px;height: 65.5px;"></image>
							<br/>
							<text>投放广告</text>
						</view>
					</uni-grid-item>
					<uni-grid-item v-if="role.device">
						<view @click="toRole(2)">
							<image src="../../static/img/home_page/partner_icon.png" style="width: 65.5px;height: 65.5px;"></image>
							<br/>
							<text>合作经营</text>
						</view>
					</uni-grid-item>
					<uni-grid-item v-if="role.shop">
						<view @click="toRole(3)">
							<image src="../../static/img/home_page/shop_icon.png" style="width: 65.5px;height: 65.5px;"></image>
							<br/>
							<text>店铺合作</text>
						</view>
					</uni-grid-item>
					<uni-grid-item v-if="role.saleperson">
						<view @click="toRole(1)">
							<image src="../../static/img/home_page/business_icon.png" style="width: 65.5px;height: 65.5px;"></image>
							<br/>
							<text>业务申请</text>
						</view>
					</uni-grid-item>
					<uni-grid-item>
						<view>
							<navigator url="/pages/news/news">
								<image src="../../static/img/home_page/news_icon.png" style="width: 65.5px;height: 65.5px;"></image>
								<br/>
								<text>店通资讯</text>
							</navigator>
						</view>
					</uni-grid-item>
					<uni-grid-item>
						<view>
							<navigator url="/pages/case/case">
								<image src="../../static/img/home_page/news_case_icon.png" style="width: 65.5px;height: 65.5px;"></image>
								<br/>
								<text>广告案列</text>
							</navigator>
						</view>
					</uni-grid-item>
					<uni-grid-item>
						<view>
							<navigator url="/pages/feedback/feedback">
								<image src="../../static/img/home_page/feedback_icon.png" style="width: 65.5px;height: 65.5px;"></image>
								<br/>
								<text>投诉建议</text>
							</navigator>
						</view>
					</uni-grid-item>
					<uni-grid-item>
						<view>
							<navigator url="">
								<image src="../../static/img/home_page/tail_cargo_icon.png" style="width: 65.5px;height: 65.5px;"></image>
								<br/>
								<text>尾货系统</text>
							</navigator>
						</view>
					</uni-grid-item>
				</uni-grid>
			</uni-card>
		</view>
		<!-- 店通服务 e -->
        
		<!-- 统计内容 s -->
		<view class="uni-center">
			<uni-card :isFull="true" style="border: 0;background-color: #F6F6F6;"> <!-- style="background-image: url(../../static/img/bgg.png);" -->
				<uni-grid :column="3" :showBorder="false" :square="false">
					<uni-grid-item style="width: 104.5px;height: 85px;background-image: url(../../static/img/home_page/total_device.png);background-size: contain;">
						<navigator url="/pages/device/device-all-list">
							<text class="grid-item-text">{{totaldata.addevice}}+</text>
							<br/>
							<text class="color-white">广告屏</text>
						</navigator>
					</uni-grid-item>
					<uni-grid-item style="width: 104.5px;height: 85px;margin-left: 16px;background-image: url(../../static/img/home_page/total_city.png);background-size: contain;">
						<navigator url="/pages/city/city">
							<text class="grid-item-text">{{totaldata.city}}</text>
							<br/>
							<text class="color-white">覆盖城市</text>
						</navigator>
					</uni-grid-item>
					<uni-grid-item style="width: 104.5px;height: 85px;margin-left: 16px;background-image: url(../../static/img/home_page/total_merchant.png);background-size: contain;">
						<navigator url="/pages/shop/shop-list">
							<text class="grid-item-text">{{totaldata.shop}}+</text>
							<br/>
							<text class="color-white">服务商家</text>
						</navigator>
					</uni-grid-item>
				</uni-grid>
			</uni-card>
		</view>
		<!-- 统计内容 s -->
		
		<!-- 尾货紧急处理 s -->
		<view class="uni-padding-wrap uni-common-mt uni-common-mb uni-center">
			<view class="text-left">
				<text class="uni-h4 uni-bold" style="color: #333333;">紧急处理</text>
			</view>
			
			<view class="uni-flex uni-row uni-common-mt" style="-webkit-flex-wrap: wrap;flex-wrap: wrap;-webkit-justify-content: space-between;justify-content: space-between;">
				<view class="tail-cargo-item" style="width: 168px;" v-for="(item, index) in 6" :key="index">
					<uni-card :isFull="true" :is-shadow="false" title="下架时间 01/29 12:00" mode="style" thumbnail="../../static/img/home_page/tg_goods_cover.png" note="true" @click="clickCard" style="margin-bottom: 10px;border: 0;border-radius: 2px;">
						<view class="">
							<view class="uni-ellipsis">
								<text class="content-box-text">惠氏启韵启赋妈妈奶粉上...</text>
							</view>
							<view class="text-left">
								<text class="fon16 uni-bold" style="color: #D71818;">￥135</text>
								<text class="uni-text-small uni-product-price-favour">￥158</text>
								<text class="uni-text-small color-blue uni-common-pl">库存484</text>
							</view>
						</view>
						
						<block slot="footer">
							<view class="footer-box">
								<view class="" @click.stop="footerClick('立即联系')">
									<button size="mini" class="login-button primary color-white"><text class="footer-box__item">立即联系</text></button>
								</view>
							</view>
						</block>
					</uni-card>
				</view>
			</view>
		</view>
		<!-- 尾货紧急处理 e -->
	</view>
</template>

<script>
	import common from '@/common/common.js';
	import {mapState, mapMutations} from 'vuex';
	
	export default {
		data() {
			return {
				role: {
					role_ids: '', //角色字符串
					device: false, //广告屏合作者
					shop: false, //店铺
					saleperson: false //业务员
				},
				totaldata:{
					addevice:'',//广告屏数量
					city:'',//城市数量
					shop:''//客户数量
				}
			}
		},
		computed: mapState(['forcedLogin', 'hasLogin', 'userInfo', 'commonheader']),
		onLoad() {
			//获取整体数据
			this.getTotalData();
		},
		onShow() {
			//调用-判断用户角色
			this.is_role();
		},
		methods: {
			/**
			 * 获取广告屏数量、城市、服务商家
			*/
		   getTotalData(){
			   let self=this;
			   //获取角色信息
			   	uni.request({
			   		url: this.$serverUrl + 'api/get-total-data',
			   		header: {
			   			'commonheader': this.commonheader,
			   			'access-user-token': this.userInfo.token
			   		},
			   		method: 'GET',
			   		success: function(res) {
                         self.totaldata.addevice=res.data.device;
						 self.totaldata.city=res.data.city;
						 self.totaldata.shop=res.data.shop;
			   		}
			   	})
			   
		   },
			
			
			/**
			 * 投放广告
			 */
			usead() {
				
				// uni.makePhoneCall({
				// 	phoneNumber: '02865272616'
				// });

				uni.navigateTo({
					url: '../ad/findad'
				});	
				
				
			},
			
			/**
			 * 指定角色跳转
			 * @param {Object} role_ids
			 */
			toRole(role_ids) {
				let self = this;
				//广告屏合作商
				if (role_ids == 2) {
					uni.navigateTo({
						url: '../user-partner/user-partner?user_id=' + self.userInfo.user_id + '&role_id=2'
					});				
				}
               
				//店铺合作者
				if (role_ids == 3) {
					uni.navigateTo({
						url: '../user-shopkeeper/user-shopkeeper?user_id=' + self.userInfo.user_id + '&role_id='+self.userInfo.role_ids
					});
				}

				//业务员
				if (role_ids == 1) {				
					uni.navigateTo({
						url: '../saleperson/center'
					});				
				}
				
				
			},
			
			/**
			 * 判断用户角色
			 */
			is_role() {
				let self = this;
				//获取角色信息
				uni.request({
					url: this.$serverUrl + 'api/get_user_role',
					data: {
						user_id: this.userInfo.user_id,
					},
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'POST',
					success: function(res) {
						if (res.data.status == 1) {
							//处理角色信息
							self.role.role_ids = res.data.data.role_ids;
							let role_str = res.data.data.role_ids;
							let role_array = role_str.split(',');
							role_array.forEach((value, index) => {
								switch (parseInt(value)) {
									case 2:
										self.role.device = true;
										break;
									case 3:
										self.role.shop = true;
										break;
									default:
										self.role.saleperson = true;
								}
							})
						} else {
							uni.showToast({
								icon: 'none',
								title: res.data.message, // '网络繁忙，稍后重试'
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
	.grid-item-text {
		font-size: 24px;
		line-height: 54px;
		font-weight: bolder;
		color: #FFFFFF;
	}
	
	.tail-cargo-item .footer-box {
		/* #ifndef APP-NVUE */
		display: flex;
		/* #endif */
		justify-content: flex-end;
		flex-direction: row;
	}
	
	.tail-cargo-item .footer-box__item {
		align-items: center;
		padding: 2px 0;
		/* font-size: 12px; */
		/* color: #666; */
	}
</style>
