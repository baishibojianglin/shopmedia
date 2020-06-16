<template>
	<view>
		<view class="uni-list" style="position: fixed;top: 89rpx;z-index: 99;">
			<view class="uni-list-cell">
				<view class="uni-list-cell-left">
					<text class="uni-text-gray">选择区域</text>
				</view>
				<!-- 省级 -->
				<view class="uni-list-cell-db uni-ellipsis">
					<picker :value="provinceIndex" :range="provinceArray" range-key="region_name" @change="bindRegionPickerChange($event, 1)">
						<view class="uni-input">{{provinceArray[provinceIndex].region_name}}</view>
					</picker>
				</view>
				<!-- 市级 -->
				<view class="uni-list-cell-db uni-ellipsis">
					<picker :value="cityIndex" :range="cityArray" range-key="region_name" @change="bindRegionPickerChange($event, 2)">
						<view class="uni-input">{{cityArray[cityIndex].region_name}}</view>
					</picker>
				</view>
				<!-- 区县 -->
				<view class="uni-list-cell-db uni-ellipsis">
					<picker :value="countyIndex" :range="countyArray" range-key="region_name" @change="bindRegionPickerChange($event, 3)">
						<view class="uni-input">{{countyArray[countyIndex].region_name}}</view>
					</picker>
				</view>
				<!-- 乡镇街道 -->
				<view class="uni-list-cell-db uni-ellipsis">
					<picker :value="townIndex" :range="townArray" range-key="region_name" @change="bindRegionPickerChange($event, 4)">
						<view class="uni-input">{{townArray[townIndex].region_name}}</view>
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
				provinceArray: [{region_id: '', region_name: '', parent_id: '', level: ''}],
				provinceIndex: 0,
				cityArray: [{region_id: '', region_name: '', parent_id: '', level: ''}],
				cityIndex: 0,
				countyArray: [{region_id: '', region_name: '', parent_id: '', level: ''}],
				countyIndex: 0,
				townArray: [{region_id: '', region_name: '', parent_id: '', level: ''}],
				townIndex: 0,
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
			this.getRegionList(0);
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
			 * 获取区域列表
			 * @param {Object} parent_id
			 * @param {Object} level
			 */
			getRegionList(parent_id, level) {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/get_region_list',
					data: {
						parent_id: parent_id,
					},
					header: {
						'commonheader': this.$store.state.commonheader,
						'access-user-token': this.userInfo.token
					},
					success(res) {
						if (res.data.status == 1) {
							if (parent_id == 0 && typeof(level) == 'undefined') {
								self.provinceArray = res.data.data;
								self.provinceArray.unshift({region_id: 0, region_name: '请选择…'});
							} else if(level == 2) {
								self.cityArray = res.data.data;
								self.cityArray.unshift({region_id: 0, region_name: '请选择…'});
							} else if(level == 3) {
								self.countyArray = res.data.data;
								self.countyArray.unshift({region_id: 0, region_name: '请选择…'});
							} else if(level == 4) {
								self.townArray = res.data.data;
								self.townArray.unshift({region_id: 0, region_name: '请选择…'});
							}
						}
					}
				});
			},
			
			/**
			 * 改变选择区域
			 * @param {Object} e
			 */
			bindRegionPickerChange: function(e, _level) {
				// console.log('picker发送选择改变，携带值为：' + e.detail.value)
				// console.log('_level：', _level)
				if (_level == 1) {
					this.provinceIndex = e.detail.value;
					let parent_id = this.provinceArray[this.provinceIndex].region_id;
					let level = this.provinceArray[this.provinceIndex].level + 1;
					this.getRegionList(parent_id, level);
					// console.log('省', this.provinceArray[this.provinceIndex].region_id)
				} else if (_level == 2) {
					this.cityIndex = e.detail.value;
					let parent_id = this.cityArray[this.cityIndex].region_id;
					let level = this.cityArray[this.cityIndex].level + 1;
					this.getRegionList(parent_id, level);
					// console.log('市', this.cityArray[this.cityIndex].region_id)
				} else if (_level == 3) {
					this.countyIndex = e.detail.value;
					let parent_id = this.countyArray[this.countyIndex].region_id;
					let level = this.countyArray[this.countyIndex].level + 1;
					this.getRegionList(parent_id, level);
					// console.log('区县', this.countyArray[this.countyIndex].region_id)
				} else if (_level == 4) {
					this.townIndex = e.detail.value;
					let town_id = this.townArray[this.townIndex].region_id;
					// console.log('乡镇街道', this.townArray[this.townIndex].region_id)
				}
				this.getList();
			},
			
			/**
			 * 广告屏列表
			 */
			getList() {
				var data = {};
				// 区域查询条件
				console.log(123, this.last_id)
				if (this.provinceArray[this.provinceIndex].region_id) {
					// this.listData = [];
					data.province_id = this.provinceArray[this.provinceIndex].region_id;
				} else if (this.cityArray[this.cityIndex].region_id) {
					data.city_id = this.cityArray[this.cityIndex].region_id;
				}
				if (this.last_id) {
					//说明已有数据，目前处于上拉加载
					this.status = 'loading';
					data.minId = this.last_id;
					// data.time = new Date().getTime() + '';
					data.size = 5;
				}
				uni.request({
					url: this.$serverUrl + 'api/device_all_list',
					data: data,
					header: {
						'commonheader': this.$store.state.commonheader,
						'access-user-token': this.userInfo.token
					},
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
