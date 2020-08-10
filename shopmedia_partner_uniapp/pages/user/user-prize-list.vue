<template>
	<view>
		<view class="uni-list">
			<view class="uni-list-cell" hover-class="uni-list-cell-hover" v-for="(value, key) in userPrizeList" :key="key">
				<view class="uni-media-list">
					<!-- <image class="uni-media-list-logo" :src="value.thumb"></image> -->
					<view class="uni-media-list-body">
						<view class="uni-media-list-text-top">【奖品】{{ value.prize_name }}</view>
						<view class="uni-media-list-text-bottom">
							<text>店铺 {{ value.shop_name }}</text>
							<text>，地址 {{ value.address }}</text>
						</view>
					</view>
				</view>
			</view>
		</view>
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
							self.userPrizeList = res.data.data;
						}
					}
				});
			}
		}
	}
</script>

<style>

</style>
