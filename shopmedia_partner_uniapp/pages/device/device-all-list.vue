<template>
	<view class="uni-comment-body">
		<uni-card style="background-color:#ECECEC;" :is-shadow='true'>
			当前在线广告位未满的屏如下，如需更多请等待其他屏广告位空出
		</uni-card>	
		<view class="uni-list" style="position: fixed;top:150rpx;z-index: 99;" v-show="false">
			<view class="uni-list-cell">
				<view class="uni-list-cell-left">
					<text class="uni-text-gray">选择区域</text>
				</view>
				<!-- 省级 -->
				<view class="uni-list-cell-db uni-ellipsis">
					<picker :value="provinceIndex" :range="provinceArray" range-key="region_name" @change="bindRegionPickerChange($event, 1)">
						<view class="uni-input">{{provinceArray[provinceIndex].region_name}} <text class="uni-icon uni-icon-arrowdown fon14"></text></view>
					</picker>
				</view>
				<!-- 市级 -->
				<view class="uni-list-cell-db uni-ellipsis" v-if="provinceIndex">
					<picker :value="cityIndex" :range="cityArray" range-key="region_name" @change="bindRegionPickerChange($event, 2)">
						<view class="uni-input">{{cityArray[cityIndex].region_name}} <text class="uni-icon uni-icon-arrowdown fon14"></text></view>
					</picker>
				</view>
				<!-- 区县 -->
				<view class="uni-list-cell-db uni-ellipsis" v-if="cityIndex">
					<picker :value="countyIndex" :range="countyArray" range-key="region_name" @change="bindRegionPickerChange($event, 3)">
						<view class="uni-input">{{countyArray[countyIndex].region_name}} <text class="uni-icon uni-icon-arrowdown fon14"></text></view>
					</picker>
				</view>
				<!-- 乡镇街道 -->
				<view class="uni-list-cell-db uni-ellipsis" v-if="countyIndex">
					<picker :value="townIndex" :range="townArray" range-key="region_name" @change="bindRegionPickerChange($event, 4)">
						<view class="uni-input">{{townArray[townIndex].region_name}} <text class="uni-icon uni-icon-arrowdown fon14"></text></view>
					</picker>
				</view>
			</view>
		</view>
		
		
		<!-- 广告设备类别 SegmentedControl 分段器 s -->
		<view>
			<uni-segmented-control :current="deviceCateSegmentedControl.current" :values="deviceCateSegmentedControl.items" @clickItem="onClickDeviceCateItem" style-type="text" active-color="#409EFF"></uni-segmented-control>
		</view>
		<!-- 广告设备类别 SegmentedControl 分段器 e -->
		
		<view class="uni-list" style="margin-top:5rpx;">
			<view class="uni-list-cell" hover-class="uni-list-cell-hover" v-for="(value, key) in listData" :key="key" @click="toDeviceDetail2(value.device_id)" v-show="(deviceCateSegmentedControl.current == 0 && value.device_cate == 1) || (deviceCateSegmentedControl.current == 1 && value.device_cate == 2)">
				<view class="uni-media-list">
					<image class="uni-media-list-logo" :src="value.thumb"></image>
					<view class="uni-media-list-body">
						<view class="uni-media-list-text-top">
							<!-- <text>屏编号：{{ value.device_id }}</text> -->
							<text class="">【店铺】{{ value.shop_name }}</text>
						</view>
						<view class="uni-media-list-text-bottom">
							<!-- <text>{{ value.device_id }}</text> -->
							<text class="uni-ellipsis"><text class="uni-icon uni-icon-location-filled color-blue"></text> <text style="position: relative;top:4px;">{{ value.address }}</text></text>
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
				
				// 广告设备类别 SegmentedControl 分段器
				deviceCateSegmentedControl: {
					items: ['广告屏', '广告框'],
					current: 0
				},
				
				/* 广告屏列表 s */
				listData: [],
				last_id: '',
				reload: false,
				status: 'more',
				contentText: {
					contentdown: '上拉加载更多',
					contentrefresh: '加载中',
					contentnomore: '没有更多'
				},
				
				// 区域查询条件
				shop_province_id: '',
				shop_city_id: '',
				shop_county_id: '',
				shop_town_id: ''
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
								self.provinceArray.unshift({region_id: 0, region_name: '全国'});
							} else if(level == 2) {
								self.cityArray = res.data.data;
								self.cityArray.unshift({region_id: 0, region_name: '请选择'});
							} else if(level == 3) {
								self.countyArray = res.data.data;
								self.countyArray.unshift({region_id: 0, region_name: '请选择'});
							} else if(level == 4) {
								self.townArray = res.data.data;
								self.townArray.unshift({region_id: 0, region_name: '请选择'});
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
				let region_id;
				let level;
				if (_level == 1) { // 省
					this.provinceIndex = e.detail.value;
					region_id = this.provinceArray[this.provinceIndex].region_id;
					level = this.provinceArray[this.provinceIndex].level + 1;
					this.getRegionList(region_id, level);
					// console.log('省', this.provinceArray[this.provinceIndex].region_id);
					this.shop_province_id = region_id;
				} else if (_level == 2) { // 市
					this.cityIndex = e.detail.value;
					region_id = this.cityArray[this.cityIndex].region_id;
					level = this.cityArray[this.cityIndex].level + 1;
					this.getRegionList(region_id, level);
					// console.log('市', this.cityArray[this.cityIndex].region_id)
					this.shop_city_id = region_id;
				} else if (_level == 3) { // 区县
					this.countyIndex = e.detail.value;
					region_id = this.countyArray[this.countyIndex].region_id;
					level = this.countyArray[this.countyIndex].level + 1;
					this.getRegionList(region_id, level);
					// console.log('区县', this.countyArray[this.countyIndex].region_id)
					this.shop_county_id = region_id;
				} else if (_level == 4) { // 乡镇街道
					this.townIndex = e.detail.value;
					region_id = this.townArray[this.townIndex].region_id;
					// console.log('乡镇街道', this.townArray[this.townIndex].region_id)
					this.shop_town_id = region_id;
				}
				this.getList();
			},
			
			/**
			 * 广告设备类别 SegmentedControl 分段器组件触发点击事件时触发
			 * @param {Object} e
			 */
			onClickDeviceCateItem(e) {
				if (this.deviceCateSegmentedControl.current !== e.currentIndex) {
					this.deviceCateSegmentedControl.current = e.currentIndex;
				}
			},
			
			/**
			 * 广告屏列表
			 */
			getList() {
				var data = {};
				// 区域查询条件
				data.province_id = this.shop_province_id; // 省
				data.city_id = this.shop_city_id; // 市
				data.county_id = this.shop_county_id; // 区县
				data.town_id = this.shop_town_id; // 乡镇街道
				if (this.shop_province_id || this.shop_city_id || this.shop_county_id ||  this.shop_town_id) {
					this.listData = [];
					this.last_id = '';
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
							if (this.last_id == res.data.data.maxId || !this.last_id) {
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
						device_cate: e.device_cate,
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
		height: 118rpx;
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
