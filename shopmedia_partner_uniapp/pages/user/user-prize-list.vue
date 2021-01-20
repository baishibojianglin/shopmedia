<template>
	<view>
		<view v-if="userPrizeList.length > 0" class="uni-list">
			<view class="uni-list-cell" hover-class="uni-list-cell-hover" v-for="(value, key) in userPrizeList" :key="key">
				<view class="uni-media-list">
					<image class="uni-media-list-logo" :src="value.prize_pic"></image>
					<view class="uni-media-list-body" style="height: 200upx;">
						<view class="uni-media-list-text-top">【奖品】{{ value.prize_name }}</view>
						<view class="uni-media-list-text-bottom">
							<view class="uni-ellipsis">
								<text>【中奖时间】{{ value.raffle_time }}</text>
							</view>
							<view class="uni-ellipsis">
								<text>【领奖店铺】{{ value.shop_name }}</text>
							</view>
							<view @click="openLocation(value.latitude, value.longitude, value.shop_name, value.address)">
								<view class="uni-ellipsis">【地址】<text class="m-icon m-icon-location-filled uni-text-small"></text>{{ value.address }}</view>
							</view>
						</view>
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
				userPrizeList: [] ,// 统计奖品领取状态列表
				isSendPrize: false // 是否发放奖品
			}
		},
		computed: {
			...mapState(['hasLogin', 'forcedLogin', 'userInfo', 'commonheader'])
		},
		onLoad(event) {
			this.prize_status = event.prize_status;
			this.getUserPrizeList();
		},
		methods: {
			/**
			 * 获取用户获得的奖品列表
			 */
			getUserPrizeList() {
				let self=this;
				uni.request({
					url: this.$serverUrl + 'api/user_prize_list',
					data: {
						user_id: this.userInfo.user_id,
						phone: this.userInfo.phone,
						prize_status: this.prize_status
					},
					method: 'GET',
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					success: (res) => {
						if (res.data.status == 1) {
							let userPrizeList = res.data.data;
							userPrizeList.forEach((item, index) => {
								item.prize_pic = (item.prize_pic != '' && JSON.parse(item.prize_pic).length != 0) ? JSON.parse(item.prize_pic)[0].url : '/static/img/cj.png';
							})
							self.userPrizeList = userPrizeList;
						}
					}
				});
			},
			
			/**
			 * 查看位置
			 * @param {Object} latitude
			 * @param {Object} longitude
			 * @param {Object} name
			 * @param {Object} address
			 */
			openLocation(latitude, longitude, name, address) {
				// 使用应用内置地图查看位置
				uni.openLocation({
					latitude: Number(latitude),
					longitude: Number(longitude),
					name: name,
					address: address
				});
			}
		}
	}
</script>

<style>

</style>
