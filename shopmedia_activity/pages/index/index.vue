<template>
	<view class="content">
<!-- 		<view class="uni-bold" style="margin-top: 50px;height: 20px;">
			<view class="">店铺ID：{{shop_id}}</view>
			<view class="">用户openid：{{wxUserInfo.openid}}</view>
			<view class="">用户昵称：{{wxUserInfo.nickname}}</view>
			<view class="">
				<image :src="wxUserInfo.headimgurl" mode="aspectFit" width="10"></image>
			</view>
		</view> -->
		
		<view v-show="showseconds">
			<view style="width: 100%; margin-top: 30px;">
				<image style="width:80%;" :mode="mode" :src="src0"></image>
			</view>
			<view>
				<text class="opentime">{{seconds}}</text>
			</view>
		</view>

		<view v-show="!showseconds">
			<view v-if="prize_no">
				<!--未中奖 s-->
				<view style="width: 100%; margin-top: 30px;">
					<image style="width:100%;" :mode="mode" :src="src1"></image>
				</view>
<!-- 				<view style="margin-top:0px;">
					<text class="color-shop" style="color:#007AFF; background-color: #fff; padding: 10px 40px; border-radius: 10px;">{{prize_info.shop.shop_name}}</text>
				</view> -->
				<view style="margin-top:60px; font-size: 16px;">
					<text>很遗憾未中奖，期待您的下次光临！</text>
				</view>
			</view>
			<!--未中奖 e-->

			<view v-if="prize_yes">
				<view style="width: 100%; margin-top:20px;">
					<image style="width:90%;" :mode="mode" :src="src2"></image>
				</view>
				<view style="color:#f00;font-size: 35px; font-weight: bold; position: relative; top:-15px;">{{prize_info.prize.prize_name}}</view>

				<view style="margin-top: 20px;">
					特别鸣谢
					<br />
					<text style="font-weight: bold;color:#007AFF; font-size: 22px; line-height: 50px;">{{prize_info.prize.sponsor}}</text>
					<br />
					对该奖品的独家赞助
				</view>

				
				<view v-if="!isAward">
					<button type="primary" @click="confirmDialog" style="width: 95%; margin-top:30px;">领取奖品</button>
					
					<!-- 提交信息 -->
					<uni-popup ref="dialogInput" type="dialog" @change="change">
						<uni-popup-dialog mode="input" title="请到店领取奖品" value="" placeholder="请输入领奖电话" @confirm="dialogInputConfirm"></uni-popup-dialog>
					</uni-popup>
				</view>
				
				<view v-if="isAward">
					<uni-card :is-shadow="true" class="uni-bold" note="温馨提示：请到店提供电话号码领取你的奖品">
						<view class="uni-flex uni-row">
							<view class="text-left" style="width: 200rpx;">领奖电话</view>
							<view class="uni-common-pl text-right" style="-webkit-flex: 1;flex: 1;">{{phone}}</view>
						</view>
						<view class="uni-flex uni-row">
							<view class="text-left" style="width: 200rpx;">领奖店铺</view>
							<view class="uni-common-pl text-right" style="-webkit-flex: 1;flex: 1;">{{prize_info.prize.is_sponsor_address == 1 ? prize_info.prize.sponsor : prize_info.shop.shop_name}}</view>
						</view>
						<view class="uni-flex uni-row">
							<view class="text-left" style="width: 200rpx;">店铺地址</view>
							<view class="uni-common-pl text-right" @click="openLocation()" style="-webkit-flex: 1;flex: 1;"><text class="uni-icon uni-icon-location-filled"></text>{{prize_info.prize.is_sponsor_address == 1 ? prize_info.prize.address : prize_info.shop.address}}</view>
						</view>
					</uni-card>
				</view>

			</view>
		</view>

		<view v-show="false" class="bottom" style="width: 100%;">店通智能屏&nbsp;&nbsp;&nbsp;&nbsp;智通天下市</view>
	</view>
</template>

<script>
	import uniPopupDialog from '@/components/uni-popup/uni-popup-dialog.vue';

	export default {
		components: {
			uniPopupDialog
		},
		data() {
			return {
				mode: 'aspectFit',
				src0: '/static/openprize.png',
				src1: '/static/xxhg.png',
				src2: '/static/gxzj.png',
				prize_no: false,
				prize_yes: false,
				prize_info: {
					shop:{
						shop_name:''
					}
				},

				seconds: 5, //倒计时秒数
				showseconds: true, //显示倒计时
				timesign: '', //定时器标志
				
				phone: '', // 领奖电话号码
				isAward: false, // 判断是否领奖成功
				
				shop_id: '', // 抽奖店铺ID
				
				// 扫广告屏上二维码后获取的微信用户信息
				wxUserInfo: {
					'openid': '',
					'nickname': '',
					'headimgurl': '' 
				}
			}
		},
		onLoad(option) {

			if (option) {
				if (typeof(option.shop_id) == 'undefined' || option.shop_id == 'null') {
					uni.showModal({
						title: '提示',
						content: '请到店铺扫描广告屏上二维码参与抽奖',
						showCancel: false
					})
					return false;
				}

				
				this.shop_id = option.shop_id;
				this.wxUserInfo.openid = option.openid;
				this.wxUserInfo.nickname = option.nickname;
				this.wxUserInfo.headimgurl = option.headimgurl;
			}
			
			//倒计时
			let self = this;
			this.timesign = setInterval(function() {
				self.countseconds();
				if (self.showseconds == false) {
					if (self.shop_id && self.wxUserInfo.openid) {
						// 记录抽奖信息
						self.recordRaffleLog();
					}
				}
			}, 1000);
			
		},
		methods: {
			/**
			 * 记录抽奖信息
			 */
			recordRaffleLog() {
				let self = this;
				// 发起网络请求，提交服务端
				uni.request({
					url: this.$serverUrl + 'api/record_raffle_log',
					data: {
						shop_id: this.shop_id,
						openid: this.wxUserInfo.openid
						
					},
					method: 'POST',
					success: function(res) {
						if (0 == res.data.status) { // 提交失败
							
							// 判断是否已经抽奖
							if (typeof(res.data.data.today_raffle) != 'undefined' && res.data.data.today_raffle > 0) {
								self.prize_yes = false;
								self.prize_no = true;
							}
							
							uni.showModal({
								title: '提示',
								content: res.data.message,
								showCancel: false
							})

							return;
						} else { // 提交成功
						
							// 获取奖品
							self.prize(self.shop_id);

						}
					},
					fail: function(error) {
						uni.showModal({
							title: '提示',
							content: error.response.message,
							showCancel: false
						})
					}
				})
			},
			
			/**
			 * 倒计时函数
			 */
			countseconds() {
				if (this.seconds > 0) {
					this.seconds--;
				} else {
					this.showseconds = false;
					clearInterval(this.timesign);
					this.seconds = 5;
				}
			},
			
			/**
			 * 获取奖品
			 * @param {Object} shop_id
			 */
			prize(shop_id) {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/get_prize',
					data: {
						shop_id: shop_id,
					},
					method: 'GET',
					success: function(res) {
						if (res.data.status == 0) { //未中奖
							self.prize_no = true;
							self.prize_info = res.data;
							console.log(self.prize_info)
						} else { //中奖
							self.prize_yes = true;
							self.prize_info = res.data;
							console.log(self.prize_info)
						}
					}
				})
			},
			
			/**
			 * 打开提交信息
			 */
			confirmDialog() {
				this.$refs.dialogInput.open()
			},
			
			/**
			 * 输入对话框的确定事件
			 */
			dialogInputConfirm(done, val) {
				uni.showLoading({
					title: '3秒后会关闭'
				})
				// console.log(val);
				this.phone = val;
				if (!(/^1(3|4|5|6|7|8|9)\d{9}$/.test(this.phone))) {
					uni.showToast({
						icon: 'none',
						title: '请输入正确的手机号码'
					});
					return false;
				}
				
				setTimeout(() => {
					uni.hideLoading()
					// 关闭窗口后，恢复默认内容
					done()
				}, 3000)
				
				this.submitForm();
			},
			
			/**
			 * popup 状态发生变化触发
			 * @param {Object} e
			 */
			change(e) {
				// console.log('popup ' + e.type + ' 状态', e.show)
			},
			
			/**
			 * 提交领奖信息
			 */
			submitForm() {
				let self = this;
				
				// 发起网络请求，提交服务端
				uni.request({
					url: this.$serverUrl + 'api/winner_info',
					data: {
						act_id: this.prize_info.prize.act_id,
						prize_id: this.prize_info.prize.prize_id,
						prize_name: this.prize_info.prize.prize_name,
						shop_id: this.prize_info.shop.shop_id,
						phone: this.phone,
						openid: this.wxUserInfo.openid,
						prizewinner: this.wxUserInfo.nickname
					},
					/* header:{
						'commonheader': this.commonheader,
						// 'access-user-token': this.userInfo.token
					}, */
					method: 'POST',
					success: function(res) {
						if (0 == res.data.status) { // 提交失败
							uni.showModal({
								title: '提示',
								content: res.data.message,
								showCancel: false
							})
							return;
						} else { // 提交成功
							uni.showModal({
								title: '提示',
								content: res.data.message,
								showCancel: false
							})
							self.isAward = true;
						}
					},
					fail: function(error) {
						// console.log('error', error.response);
						uni.showModal({
							title: '提示',
							content: error.response.message,
							showCancel: false
						})
					}
				})
			},
			
			/**
			 * 查看位置
			 */
			openLocation() {
				let name = '', address = '', latitude = '', longitude = '';
				if (this.prize_info.prize.is_sponsor_address == 1) {
					name = this.prize_info.prize.sponsor;
					address = this.prize_info.prize.address;
					latitude = Number(this.prize_info.prize.latitude);
					longitude = Number(this.prize_info.prize.longitude);
				} else {
					name = this.prize_info.shop.shop_name;
					address = this.prize_info.shop.address;
					latitude = Number(this.prize_info.shop.latitude);
					longitude = Number(this.prize_info.shop.longitude);
				}
				// 使用应用内置地图查看位置
				uni.openLocation({
					latitude: latitude,
					longitude: longitude,
					name: name,
					address: address
				});
			}
		}
	}
</script>

<style>
	.content {
		position: fixed;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		background-image: url(../../static/bgg.png);
		text-align: center;
	}

	.bottom {
		position: fixed;
		bottom: 30px;
		font-size: 14px;
	}

	.color-shop {
		font-size: 22px;
		font-weight: bold;
	}

	.opentime {
		display: inline-block;
		height: 150px;
		width: 150px;
		line-height: 150px;
		border-radius: 150px;
		background-color: #E92F30;
		color: #fff;
		font-weight: bold;
		font-size: 50px;
	}
</style>
