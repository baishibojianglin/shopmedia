<template>
	<view class="uni-comment-body">
		<view class="uni-list" v-if="shopList.length != 0">
			<view class="uni-list-cell" hover-class="uni-list-cell-hover" v-for="(value, key) in shopList" :key="key">
				<view class="uni-media-list">
					<image class="uni-media-list-logo" :src="value.thumb"></image>
					<view class="uni-media-list-body">
						<view class="uni-media-list-text-top">
							<text>{{ value.shop_name }}</text>
							<text class="uni-common-pl uni-text-small">【{{ value.status_msg }} {{ value.reject_reason }}】</text>
						</view>
						<view class="uni-media-list-text-bottom">
							<!-- <text>{{ value.device_id }}</text> -->
							<text class="uni-ellipsis">{{ value.address }}</text>
						</view>
					</view>
				</view>
			</view>
		</view>
	
		<view class="uni-common-mt uni-center" v-else>
			没有更多
		</view>
	</view>
</template>

<script>
	import {mapState, mapMutations} from 'vuex';
	
	export default {
		data() {
			return {
				shopList: [] // 店铺列表
			}
		},
		computed: {
			...mapState(['hasLogin', 'forcedLogin', 'userInfo', 'commonheader'])
		},
		onLoad(option) {
			this.getSalesmanShopList(option.shop_status);
		},
		onNavigationBarButtonTap(e) {
			this.$common.actionSheetTap();
		},
		methods: {
			/**
			 * 获取店铺业务员开拓店铺列表
			 */
			getSalesmanShopList(shop_status) {
				let self = this;
				let data = {
					user_id: this.userInfo.user_id,
					role_id: 6
				}
				
				if (typeof(shop_status) !== 'undefined') {
					data.shop_status = shop_status;
				}
				
				uni.request({
					url: this.$serverUrl + 'api/salesman_shop_list',
					data: data,
					method: 'GET',
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					success: function(res) {
						if (res.data.status == 1) {
							res.data.data.forEach((value, index) => {
								// 广告屏列表
								let thumb = typeof(JSON.parse(value.shop_pic)[0]) != 'undefined' ? JSON.parse(value.shop_pic)[0].url : '';
								self.$set(self.shopList, index, {
									shop_id: value.shop_id.toString(),
									shop_name: value.shop_name,
									address: value.address,
									thumb: thumb,
									status: value.status,
									status_msg: value.status_msg,
									reject_reason: value.reject_reason
								});
							})
						}
					},
					fail(error) {
						uni.showToast({
							icon: 'none',
							title: '请求异常'
						});
					}
				});
			}
		}
	}
</script>

<style>

</style>
