<template>
	<view>
		<view class="uni-list" style="position: fixed;top: 89rpx;z-index: 99;">
			<view class="uni-list-cell">
				<view class="uni-list-cell-left">
					选择区域
				</view>
				<view class="uni-list-cell-db">
					<picker :value="provinceIndex" :range="provinceArray" range-key="province_name" @change="bindProvincePickerChange">
						<view class="uni-input">{{provinceArray[provinceIndex].province_name}}</view>
					</picker>
				</view>
			</view>
		</view>
		
		<view class="uni-list" style="margin-top: 100rpx;">
			<view class="uni-list-cell" hover-class="uni-list-cell-hover" v-for="(value, key) in listData" :key="key" @click="toDeviceDetail2(value.device_id)">
				<view class="uni-media-list">
					<image class="uni-media-list-logo" :src="value.thumb"></image>
					<view class="uni-media-list-body">
						<view class="uni-media-list-text-top">
							<text>屏编号{{ value.device_id }}</text>
							<text class="uni-common-pl">【店铺】{{ value.shop_name }}</text>
						</view>
						<view class="uni-media-list-text-bottom">
							<!-- <text>{{ value.device_id }}</text> -->
							<text class="uni-ellipsis">{{ value.address }}</text>
						</view>
					</view>
				</view>
			</view>
		</view>
		<uni-load-more :status="status" :icon-size="16" :content-text="contentText" />
	</view>
</template>

<script>
	import {mapState} from 'vuex';

	export default {
		data() {
			return {
				/* 选择区域 s */
				provinceArray: [{province_id: 1, province_name:'中国'},{province_id: 2, province_name: '美国'}, {province_id: 3, province_name:'巴西'}, {province_id: 3, province_name:'日本'}],
				provinceIndex: 0,
				/* 选择区域 e */
				
				/* 广告屏列表 s */
				listData: [],
				last_id: '',
				reload: false,
				status: 'more',
				contentText: {
					contentdown: '上拉加载更多',
					contentrefresh: '加载中',
					contentnomore: '没有更多'
				}
				/* 广告屏列表 e */
			};
		},
		computed: {
			...mapState(['hasLogin', 'forcedLogin', 'userInfo', 'commonheader'])
		},
		onLoad() {
			this.getList();
		},
		onPullDownRefresh() {
			this.reload = true;
			this.last_id = '';
			this.getList();
		},
		onReachBottom() {
			this.status = 'more';
			this.getList();
		},
		onNavigationBarButtonTap(e) {
			this.$common.actionSheetTap();
		},
		methods: {
			/**
			 * 改变选择省级区域
			 * @param {Object} e
			 */
			bindProvincePickerChange: function(e) {
				console.log('picker发送选择改变，携带值为：' + e.detail.value)
				this.index = e.detail.value
			},
			
			/**
			 * 广告屏列表
			 */
			getList() {
				var data = {};
				if (this.last_id) {
					//说明已有数据，目前处于上拉加载
					this.status = 'loading';
					data.minId = this.last_id;
					// data.time = new Date().getTime() + '';
					data.size = 5;
				}
				uni.request({
					url: this.$serverUrl + 'api/device_all_list',
					header: {
						'commonheader': this.$store.state.commonheader,
						'access-user-token': this.userInfo.token
					},
					data: data,
					success: res => {
						if (res.statusCode == 200) {
							// let list = res.data.data.data;
							let list = this.setTime(res.data.data.data);
							this.listData = this.reload ? list : this.listData.concat(list);
							this.last_id = list[list.length - 1].device_id;
							this.reload = false;
						} else if (res.statusCode == 404) {
							// 判断数据已经全部加载
							if (this.last_id == res.data.data.maxId) {
								this.status = '';
								return;
							}
						}
					},
					fail: (error, code) => {
						// console.log('fail' + JSON.stringify(error));
					}
				});
			},
			
			setTime: function(items) {
				var newItems = [];
				items.forEach(e => {
					let thumb = typeof(JSON.parse(e.url_image)[0]) != 'undefined' ? JSON.parse(e.url_image)[0].url : '';
					newItems.push({
						device_id: e.device_id,
						shop_name: e.shop_name,
						address: e.address,
						thumb: thumb
					});
				});
				return newItems;
			},
			
			/**
			 * 跳转广告屏详情页
			 * @param {Object} device_id
			 */
			toDeviceDetail2(device_id) {
				uni.navigateTo({
					url: '/pages/device/device-detail2?device_id=' + device_id
				});
			}
		}
	};
</script>

<style>
	.uni-media-list-logo {
		width: 180rpx;
		height: 140rpx;
	}

	.uni-media-list-body {
		height: auto;
		justify-content: space-around;
	}

	.uni-media-list-text-top {
		height: 74rpx;
		font-size: 28rpx;
		overflow: hidden;
	}

	.uni-media-list-text-bottom {
		display: flex;
		flex-direction: row;
		justify-content: space-between;
	}
</style>
