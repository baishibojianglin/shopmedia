<template>
	<view>
		<view v-if="rafflePrizeList.length > 0" class="uni-list">
			<view class="uni-list-cell" hover-class="uni-list-cell-hover" v-for="(value, key) in rafflePrizeList" :key="key">
				<view class="uni-media-list">
					<image class="uni-media-list-logo" :src="value.prize_pic"></image>
					<view class="uni-media-list-body" style="height: 200upx;">
						<view class="uni-media-list-text-top">【中奖电话】<!-- {{ value.prizewinner }} --> {{ value.phone }}</view>
						<view class="uni-media-list-text-bottom">
							<view class="uni-ellipsis">
								<text>【中奖时间】{{ value.raffle_time }}</text>
							</view>
							<view class="uni-ellipsis">
								<text>【奖品】{{ value.prize_name }}</text>
							</view>
							<view class="uni-ellipsis">
								<text>【店铺】{{ value.shop_name }}</text>
							</view>
						</view>
					</view>
					<view class="">
						<button v-if="!value.prize_status" type="default" size="mini" @click="sendPrize(value.raffle_id, value)">发放</button>
						<text v-if="value.prize_status">已发放</text>
					</view>
				</view>
			</view>
		</view>
		<view v-else class="uni-center">什么也没有</view>
	</view>
</template>

<script>
	import {mapState} from 'vuex';
	
	export default {
		data() {
			return {
				prize_status: '', // 奖品领取状态
				rafflePrizeList: [], // 统计奖品领取状态列表
			}
		},
		computed: {
			...mapState(['hasLogin', 'forcedLogin', 'userInfo', 'commonheader'])
		},
		onLoad(event) {
			this.prize_status = event.prize_status;
			this.getRafflePrizeList();
		},
		methods: {
			/**
			 * 统计奖品领取状态列表
			 */
			getRafflePrizeList() {
				let self=this;
				uni.request({
					url: this.$serverUrl + 'api/raffle_prize_list',
					data: {
						user_id: this.userInfo.user_id,
						prize_status: this.prize_status
					},
					method: 'GET',
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					success: (res) => {
						if (res.data.status == 1) {
							let rafflePrizeList = res.data.data;
							rafflePrizeList.forEach((item, index) => {
								item.prize_pic = (item.prize_pic != '' && JSON.parse(item.prize_pic).length != 0) ? JSON.parse(item.prize_pic)[0].url : '/static/img/cj.png';
							})
							self.rafflePrizeList = rafflePrizeList;
						}
					}
				});
			},
			
			/**
			 * 发放奖品
			 * @param {Object} raffle_id
			 */
			sendPrize(raffle_id, item) {
				let self = this;
				
				uni.showModal({
					title: '提示',
					content: '确认用户已经领取奖品?',
					success: function (res) {
						if (res.confirm) {
							uni.request({
								url: self.$serverUrl + 'api/update_prize_status',
								data: {
									raffle_id: raffle_id,
									prize_status: self.prize_status
								},
								method: 'PUT',
								header: {
									'commonheader': self.commonheader,
									'access-user-token': self.userInfo.token
								},
								success: (res) => {
									if (res.data.status == 1) {
										item.prize_status = !item.prize_status;
										//self.$forceUpdate(); // 强制重新渲染vue实例
										uni.showModal({
											title: '提示',
											content: '奖品发放成功',
											showCancel: false
										})
										return false;
									}
								}
							});
						} else if (res.cancel) {
							// console.log('用户点击取消');
						}
					}
				})
			}
		}
	}
</script>

<style>

</style>
