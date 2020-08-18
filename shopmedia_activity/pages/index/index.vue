<template>
	<view class="content">

		<view v-if="showbut">
			<view class="content-cj">
				<LotteryDraw @get_winingIndex='recordRaffleLog' @luck_draw_finish='luck_draw_finish'></LotteryDraw>
			</view>
			<view class="wb100">
				<image class="src3css" :mode="mode" :src="src3"></image>
				<view class="guize">
					<view>1、扫描屏幕上的二维码关注公众号即可抽奖</view>
					<view>2、奖品在扫码店面领取，部分奖品需到指定店领取</view>
					<view>3、领取奖品时向店家提供中奖后所填电话号码</view>
					<view>4、积累积分2000以上可以兑换奖品</view>
					<view>5、本活动最终解释权归狄霖店通传媒所有</view>
				</view>
			</view>
		</view>

		<view v-if="show_result" class="covercss"></view>
		<!--遮罩层 -->

		<view v-if="show_result">
			<!--中奖 s-->
			<view class="aimprize" v-if="prize_yes">
				<view class="wb100">
					<image style="width:90%;height: 100px;" :src="src2"></image>
				</view>
				<view class="wpbj" v-if="!isAward">{{prize_info.prize.prize_name}}</view>

				<view style="margin-top: 0px;">
					特别鸣谢
					<br />
					<text style="font-weight: bold;color:#007AFF; font-size: 18px; line-height: 50px;">{{prize_info.prize.sponsor}}</text>
					<br />
					对该奖品的独家赞助
				</view>

				<view v-if="!isAward">
					<button type="primary" @click="confirmDialog" style="width: 95%; margin-top:10px; margin-bottom: 20px;">领取奖品</button>

					<!-- 提交信息 -->
					<uni-popup ref="dialogInput" type="dialog" @change="change">
						<uni-popup-dialog mode="input" title="" value="" placeholder="请输入领奖手机号" @confirm="dialogInputConfirm"></uni-popup-dialog>
					</uni-popup>
				</view>

				<view v-if="isAward">
					<uni-card :is-shadow="true" class="uni-bold" note="温馨提示：到店提供电话号码即可领取">
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
			<!--中奖 e-->

			<!--未中奖 s-->
			<view class="aimprize1" v-if="prize_no">
				<view class="wb100">
					<image style="width:100%;height: 400px;" :src="src1"></image>
				</view>
			</view>
			<!--未中奖 e-->

		</view>

	</view>
</template>

<script>
	import uniPopupDialog from '@/components/uni-popup/uni-popup-dialog.vue';
	import LotteryDraw from '../../components/SJ-LotteryDraw/SJ-LotteryDraw.vue';

	export default {
		components: {
			uniPopupDialog,
			LotteryDraw
		},
		data() {
			return {
				mode: 'aspectFit',
				src0: '/static/openprize.png',
				src1: '/static/xxhg.png',
				src2: '/static/gxzj.png',
				src3: '/static/cjgz.png',
				prize_id: 0,
				showbut: true,
				prize_no: false,
				prize_yes: false,
				num_id: 0,
				show_result: false,
				prize_info: {
					shop: {
						shop_name: ''
					}
				},
				phone: '', // 领奖电话号码
				isAward: false, // 判断是否领奖成功	
				create_time: 0, // （扫码/关注微信公众号）消息创建时间 （整型）
				device_id: 0, // 抽奖广告屏设备ID
				// 扫广告屏上二维码后获取的微信用户信息
				wxUserInfo: {
					'openid': '',
					'nickname': '',
					'headimgurl': ''
				},
				//抽奖插件
				lottery_draw_param: {
					startIndex: 0, //开始抽奖位置，从0开始
					totalCount: 4, //一共要转的圈数
					winingIndex: 4, //中奖的位置，从0开始
					speed: 50 //抽奖动画的速度 [数字越大越慢,默认100]
				}
			}
		},
		onLoad(option) {
			//判断传入的参数
			if (option) {
				if (option.scene_id) {
					this.create_time = option.create_time;
					this.device_id = option.scene_id;
					this.wxUserInfo.openid = option.openid;
					this.wxUserInfo.nickname = option.nickname;
					this.wxUserInfo.headimgurl = option.headimgurl;
				}
			}

			//抽奖
			this.prize(this.device_id);
		},
		methods: {
			/**
			 * @param {Object} param
			 */
			luck_draw_finish(param) {
				// this.is_look = false;
				this.show_result = true;
				// console.log(`抽到第${param+1}个方格的奖品`)
				// console.log(param)
			},

			/**
			 * 记录抽奖信息
			 * @param {Object} callback LotteryDraw组件get_winingIndex方法回调
			 */
			recordRaffleLog(callback) {
				let self = this;
				// 发起网络请求，提交服务端
				uni.request({
					url: this.$serverUrl + 'api/record_raffle_log',
					data: {
						create_time: this.create_time,
						device_id: this.device_id,
						openid: this.wxUserInfo.openid
					},
					method: 'POST',
					success: function(res) {
						if (res.data.status == 1) { //提交成功												
							self.lottery_draw_param.winingIndex = self.num_id;
							//props修改在小程序和APP端不成功，所以在这里使用回调函数传参，
							callback(self.lottery_draw_param);
						} else {
							uni.showModal({
								title: '提示',
								content: res.data.message, // '您今日在该店已经抽过奖了，请客官明日再来发现惊喜！'
								showCancel: false
							})
							return false;
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
			 * 获取奖品
			 * @param {Object} device_id
			 */
			prize(device_id) {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/get_prize',
					data: {
						device_id: device_id,
					},
					method: 'GET',
					success: function(res) {
						if (res.data.status == 0) { //未中奖
							self.prize_no = true;
						} else { //中奖
							self.prize_yes = true;
							self.prize_info = res.data;
							self.num_id = res.data.num_id;
							// console.log(self.prize_info)
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
				this.phone = val;
				if (!(/^1(3|4|5|6|7|8|9)\d{9}$/.test(this.phone))) {
					uni.showToast({
						icon: 'none',
						title: '请输入正确的手机号码'
					});
					return false;
				}

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
						device_id: this.device_id,
						phone: this.phone,
						openid: this.wxUserInfo.openid,
						prizewinner: this.wxUserInfo.nickname,
						headimgurl: this.wxUserInfo.headimgurl
					},
					/* header:{
						'commonheader': this.commonheader,
						// 'access-user-token': this.userInfo.token
					}, */
					method: 'POST',
					success: function(res) {
						// console.log('success', res);
						if (0 == res.data.status) { // 提交失败
							uni.showModal({
								title: '提示',
								content: res.data.message,
								showCancel: false
							})
							return;
						} else { // 提交成功
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
				let name = '',
					address = '',
					latitude = '',
					longitude = '';
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
	.content-cj {
		margin-top: 80px;
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
	}

	.content {
		position: fixed;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		background: #A7A4F9;
		text-align: center;
		overflow-y: scroll;
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

	.wb100 {
		width: 100%;
	}

	.src3css {
		width: 45%;
		height: 100px;
	}

	.guize {
		width: 90%;
		margin-left: 5%;
		background: #918EED;
		border-radius: 10px;
		padding: 10px 0px;
		text-align: left;
		text-indent: 10px;
		color: #464646;
		margin-bottom: 30px;
	}

	.covercss {
		position: fixed;
		z-index: 199;
		left: 0;
		right: 0;
		top: 0;
		bottom: 0;
		background-color: #000;
		opacity: 0.4;
	}

	.aimprize {
		position: fixed;
		z-index: 200;
		width: 85%;
		top: 10%;
		left: 6%;
		background-color: #FEFEFE;
		border: 8px solid #3C38B3;
		border-radius: 15px;
	}

	.wpbj {
		width: 100%;
		height: 230px;
		background-image: url(../../static/wpbj.png);
		background-size: 100% 100%;
		line-height: 240px;
		color: #BA0000;
		font-weight: bold;
		font-size: 18px;
	}

	.aimprize1 {
		position: fixed;
		z-index: 200;
		width: 85%;
		top: 20%;
		left: 7.5%;
		background-color: none;
		border: 0px solid #3C38B3;
		border-radius: 0px;
	}
</style>
