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
							<!-- <text class="icon color-white iconbg-ad">&#xe636;</text> -->
							<image src="../../static/img/home_page/ad_icon.png" style="width: 65.5px;height: 65.5px;"></image>
							<br/>
							<text>投放广告</text>
						</view>
					</uni-grid-item>
					<uni-grid-item v-if="role.device">
						<view @click="toRole(2)">
							<!-- <text class="icon color-white iconbg-partner">&#xe637;</text> -->
							<image src="../../static/img/home_page/partner_icon.png" style="width: 65.5px;height: 65.5px;"></image>
							<br/>
							<text>合作经营</text>
						</view>
					</uni-grid-item>
					<uni-grid-item v-if="role.shop">
						<view @click="toRole(3)">
							<!-- <text class="icon color-white iconbg-shop">&#xe61b;</text> -->
							<image src="../../static/img/home_page/shop_icon.png" style="width: 65.5px;height: 65.5px;"></image>
							<br/>
							<text>店铺合作</text>
						</view>
					</uni-grid-item>
					<uni-grid-item v-if="role.saleperson">
						<view @click="toRole(1)">
							<!-- <text class="icon color-white iconbg-sale">&#xe63d;</text> -->
							<image src="../../static/img/home_page/business_icon.png" style="width: 65.5px;height: 65.5px;"></image>
							<br/>
							<text>业务申请</text>
						</view>
					</uni-grid-item>
					<uni-grid-item>
						<view>
							<navigator url="/pages/news/news">
								<!-- <text class="icon color-white iconbg-notice">&#xe652;</text> -->
								<image src="../../static/img/home_page/news_icon.png" style="width: 65.5px;height: 65.5px;"></image>
								<br/>
								<text>店通资讯</text>
							</navigator>
						</view>
					</uni-grid-item>
					<uni-grid-item>
						<view>
							<navigator url="/pages/case/case">
								<!-- <text class="icon color-white iconbg-case">&#xe648;</text> -->
								<image src="../../static/img/home_page/news_case_icon.png" style="width: 65.5px;height: 65.5px;"></image>
								<br/>
								<text>广告案列</text>
							</navigator>
						</view>
					</uni-grid-item>
					<uni-grid-item>
						<view>
							<navigator url="/pages/feedback/feedback">
								<!-- <text class="icon color-white iconbg-advice">&#xe74f;</text> -->
								<image src="../../static/img/home_page/feedback_icon.png" style="width: 65.5px;height: 65.5px;"></image>
								<br/>
								<text>投诉建议</text>
							</navigator>
						</view>
					</uni-grid-item>
					<uni-grid-item>
						<view>
							<navigator url="">
								<!-- <text class="icon color-white iconbg-advice">&#xe74f;</text> -->
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
							<text class="text-grid">{{totaldata.addevice}}+</text>
							<br/>
							<text class="color-white">广告屏</text>
						</navigator>
					</uni-grid-item>
					<uni-grid-item style="width: 104.5px;height: 85px;margin-left: 16px;background-image: url(../../static/img/home_page/total_city.png);background-size: contain;">
						<navigator url="/pages/city/city">
							<text class="text-grid">{{totaldata.city}}</text>
							<br/>
							<text class="color-white">覆盖城市</text>
						</navigator>
					</uni-grid-item>
					<uni-grid-item style="width: 104.5px;height: 85px;margin-left: 16px;background-image: url(../../static/img/home_page/total_merchant.png);background-size: contain;">
						<navigator url="/pages/shop/shop-list">
							<text class="text-grid">{{totaldata.shop}}+</text>
							<br/>
							<text class="color-white">服务商家</text>
						</navigator>
					</uni-grid-item>
				</uni-grid>
			</uni-card>
		</view>
		<!-- 统计内容 s -->
		
		<!-- 尾货紧急处理 s -->
		<view class="uni-padding-wrap uni-common-mt uni-center">
			<view class="text-left">
				<text class="uni-h4 uni-bold" style="color: #333333;">紧急处理</text>
			</view>
			
			<view class="uni-flex uni-row" style="-webkit-flex-wrap: wrap;flex-wrap: wrap;">
				<view class="" style="width: 168px;" v-for="(item, index) in 6" :key="index">
					<!-- <uni-card note="Tips">
						内容主体，可自定义内容及样式
					</uni-card> -->
					
					<uni-card :is-shadow="true" title="下架时间：01/29 12:00" mode="style" thumbnail="../../static/img/home_page/tg_goods_cover.png" note="true" @click="clickCard">
						<text class="content-box-text uni-ellipsis">惠氏启韵启赋妈妈奶粉上...</text>
						<block slot="footer">
							<view class="footer-box">
								<view class="" @click.stop="footerClick('喜欢')"><text class="footer-box__item">喜欢</text></view>
								<view class="" @click.stop="footerClick('评论')"><text class="footer-box__item">评论</text></view>
								<view class="" @click.stop="footerClick('分享')"><text class="footer-box__item">分享</text></view>
							</view>
						</block>
					</uni-card>
				</view>
				<view class="" style="width: 168px;">
					<uni-card title="标题文字" thumbnail="" extra="额外信息" note="Tips">
						内容主体，可自定义内容及样式
					</uni-card>
				</view>
				<view class="" style="width: 168px;">
					<uni-card title="标题文字" thumbnail="" extra="额外信息" note="Tips">
						内容主体，可自定义内容及样式
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
	.text-grid {
		font-size: 24px;
		line-height: 54px;
		font-weight: bolder;
		color: #FFFFFF;
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
		background-color: #4C85FC;
	}
	.iconbg-partner{
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
	.iconbg-case{
		height: 45px;
		width: 45px;
		border-radius: 45px;
		border: 1px solid #F3F3F3;
		line-height: 45px;
		display: inline-block;
		background-color:#FF4403;
	}
	.iconbg-shop{
		height: 45px;
		width: 45px;
		border-radius: 45px;
		border: 1px solid #F3F3F3;
		line-height: 45px;
		display: inline-block;
		background-color: #8CE050;
	}
</style>
