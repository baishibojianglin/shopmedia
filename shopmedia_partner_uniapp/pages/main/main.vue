<template>
	<view class="content">
		
		<uni-card  :is-shadow='true'>
			<view class="tvcon">
				<video class="vedio-con" src="https://sustock-website.oss-cn-chengdu.aliyuncs.com/company1.mp4" :autoplay="true"  :loop="false" :controls="true"></video><!-- https://sustock-app-test.oss-cn-chengdu.aliyuncs.com/company.mp4 -->
				<view class="vedio-logo">
					— 店通传媒 —
				</view>
			</view>
		</uni-card>

		<view>
			<uni-card style="background-image: url(../../static/img/bgg.png);" :is-shadow='false'>
				<uni-grid class="view-grid-con totalcontentbg" :column="3">
					<navigator url="/pages/device/device-all-list">
						<uni-grid-item>
							<text class="text-grid-title">广告屏</text>
							<text class="text-grid">{{totaldata.addevice}}+</text>
						</uni-grid-item>
					</navigator>	
					<navigator url="/pages/city/city">
						<uni-grid-item>
							<text class="text-grid-title">覆盖城市</text>
							<text class="text-grid">{{totaldata.city}}</text>
						</uni-grid-item>
					</navigator>
					<navigator url="/pages/shop/shop-list">
						<uni-grid-item>
							<text class="text-grid-title">服务商家</text>
							<text class="text-grid">{{totaldata.shop}}+</text>
						</uni-grid-item>
					</navigator>
				</uni-grid>
			</uni-card>
		</view>

		<view>
			<text class="user-title"> <text class="color-blue">—</text> <span style="padding: 0 5px;">店通服务</span> <text class="color-blue">—</text></text>
		</view>

        <uni-card  :is-shadow='true'>
			<view class="navcon">
				<view class="navcon-item" @click="usead()">
					<text class="iconposition icon color-white iconbg-ad">&#xe636;</text>
					<br />
					<text>投放广告</text>
				</view>
				<view v-if="role.device" @click="toRole(2)" class="navcon-item">
					<text class="iconposition icon color-white iconbg-parnter">&#xe637;</text>
					<br />
					<text>合作经营</text>
				</view>
				<view v-if="role.shop" @click="toRole(3)" class="navcon-item">
					<text class="iconposition icon color-white iconbg-shop">&#xe61b;</text>
					<br />
					<text>店铺合作</text>
				</view>
				<view v-if="role.saleperson" @click="toRole(1)" class="navcon-item">
					<text class="iconposition icon color-white iconbg-sale">&#xe63d;</text>
					<br />
					<text>业务申请</text>
				</view>
				<view class="navcon-item">
					<navigator url="/pages/news/news">
						<text class="iconposition icon color-white iconbg-notice">&#xe652;</text>
						<br />
						<text>店通资讯</text>
					</navigator>
				</view>
				<view class="navcon-item">
					<navigator url="/pages/case/case">
						<text class="iconposition icon color-white iconbg-case">&#xe648;</text>
						<br/>
						<text>广告案列</text>
					</navigator>
				</view>
				<view class="navcon-item">
					<navigator url="/pages/feedback/feedback">
						<text class="iconposition icon color-white iconbg-advice">&#xe74f;</text>
						<br/>
						<text>投诉建议</text>
					</navigator>
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
