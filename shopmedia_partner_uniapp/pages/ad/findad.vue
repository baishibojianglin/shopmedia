<template>
	<view class="content">
		<!-- <view class="line4 fon16 main-color">选择投放区域</view> -->
		<uni-card :is-shadow="true">
			<!-- 广告投放区域 SegmentedControl 分段器 s -->
			<view>
				<uni-segmented-control :current="segmentedControl.current" :values="segmentedControl.items" @clickItem="onClickItem" style-type="button" active-color="#4C85FC"></uni-segmented-control>
				<view class="">
					<view v-if="segmentedControl.current === 0">
						<view class="input-line-height">
							<view class="input-line-height-1">投放距离 <text class="main-color line-blue">|</text></view>
							<picker @change="bindDistancePickerChange" class="input-line-height-2" :value="distanceIndex" :range="distanceList" range-key="distance">
								<view style="font-size: 15px; padding-left: 15px;">{{distanceList[distanceIndex].distance}}㎞</view>
							</picker>
						</view>
						
						<map :latitude="latitude" :longitude="longitude" :scale="scale" :markers='markers' :circles="circles" style="height: 500rpx; width: 100%;"></map>
					</view>
					<view v-if="segmentedControl.current === 1">
						<!-- scroll-view 纵向滚动 s -->
						<scroll-view :scroll-top="scrollTop" scroll-y="true" class="scroll-Y" @scroll="scroll">
							<!-- 区域 Tree 树形数据 -->
							<ly-tree ref="tree" v-if="isReady" :props="props" node-key="region_id" :load="loadNode" lazy show-checkbox @check="handleCheck" />
						</scroll-view>
						<view v-if="old.scrollTop > 200" @tap="goTop" class="uni-link uni-center uni-common-mt">返回顶部</view>
						<!-- scroll-view 纵向滚动 e -->
					</view>
				</view>
			</view>
			<!-- 广告投放区域 SegmentedControl 分段器 e -->
		</uni-card>
		
		<!-- <view class="line4 fon16 main-color">填写基本信息</view> -->
		<uni-card :is-shadow="true">
			<view>
				<view class="input-line-height">
					<view class="input-line-height-1">广告类别 <text class="main-color line-blue">|</text></view>
					<picker @change="bindAdCatePickerChange" class="input-line-height-2" :value="adCateIndex" :range="adCateList" range-key="cate_name">
						<view style="font-size: 15px; padding-left: 15px;">{{adCateList[adCateIndex].cate_name}}</view>
					</picker>
				</view>
				<view class="input-line-height">
					<view class="input-line-height-1">投放天数 <text class="main-color line-blue">|</text></view>
					<input class="input-line-height-2" style="font-size: 15px; padding-left: 15px;" type="number" v-model="form.play_days" @input="playDaysInput" />天</view>
				<view class="input-line-height">
					<view class="input-line-height-1">开始日期 <text class="main-color line-blue">|</text></view>
					<view class="input-line-height-2" style="font-size: 15px; padding-left: 15px;">{{form.startdate}}</view>
					<uni-calendar ref="calendar" :insert="false" @confirm="confirm" />
					<button style="font-size: 15px;width: 80px;" @click="open">选择</button>
				</view>
			</view>
		</uni-card>
		
		<!-- <view class="line4 fon16 main-color">选择投放广告屏</view> -->
		<uni-card :is-shadow="true" v-if="deviceList.length != 0">
			<!-- 广告设备类别 SegmentedControl 分段器 s -->
			<view>
				<uni-segmented-control :current="deviceCateSegmentedControl.current" :values="deviceCateSegmentedControl.items" @clickItem="onClickDeviceCateItem" style-type="button" active-color="#4C85FC"></uni-segmented-control>
				<view class="uni-list">
					<checkbox-group @change="deviceCheckboxChange">
						<label class="uni-list-cell uni-list-cell-pd" v-for="item in deviceList" :key="item.device_id" v-show="(deviceCateSegmentedControl.current == 0 && item.device_cate == 1) || (deviceCateSegmentedControl.current == 1 && item.device_cate == 2)">
							<view class="uni-flex uni-row">
								<view>
									<checkbox :value="item.device_id" :checked="item.checked" />
								</view>
								<image class="uni-media-list-logo uni-common-pl" :src="item.thumb"></image>
								<view>【店铺】{{item.shop_name}}<!-- 屏编号：{{item.device_id}}，（地址：{{item.address}}） --></view>
							</view>
							<text class="uni-icon uni-icon-arrowright fon14" @click.stop="toDeviceDetail2(item.device_id)"></text>
						</label>
					</checkbox-group>
				</view>
			</view>
			<!-- 广告设备类别 SegmentedControl 分段器 e -->
		</uni-card>
		
		<uni-card :is-shadow="true" class="uni-bold">
			<view class="uni-flex uni-row">
				<view class="text-left" style="width: 360rpx;">广告设备总数</view>
				<view class="uni-common-pl text-right" style="-webkit-flex: 1;flex: 1;">{{deviceList.length}}</view>
			</view>
			<view class="uni-flex uni-row">
				<view class="text-left" style="width: 360rpx;">广告屏总数</view>
				<view class="uni-common-pl text-right" style="-webkit-flex: 1;flex: 1;">{{device1Count}}</view>
			</view>
			<view class="uni-flex uni-row">
				<view class="text-left" style="width: 360rpx;">广告框总数</view>
				<view class="uni-common-pl text-right" style="-webkit-flex: 1;flex: 1;">{{device2Count}}</view>
			</view>
			
			<view class="uni-flex uni-row">
				<view class="text-left" style="width: 360rpx;">投放设备数量</view>
				<view class="uni-common-pl text-right" style="-webkit-flex: 1;flex: 1;">{{checkedDeviceCount}}</view>
			</view>
			<view class="uni-flex uni-row">
				<view class="text-left" style="width: 360rpx;">投放广告屏数量</view>
				<view class="uni-common-pl text-right" style="-webkit-flex: 1;flex: 1;">{{checkedDevice1Count}}</view>
			</view>
			<view class="uni-flex uni-row">
				<view class="text-left" style="width: 360rpx;">投放广告框数量</view>
				<view class="uni-common-pl text-right" style="-webkit-flex: 1;flex: 1;">{{checkedDevice2Count}}</view>
			</view>
			
			<view class="uni-flex uni-row">
				<view class="text-left" style="width: 360rpx;">投放广告屏总价</view>
				<view class="uni-common-pl text-right color-red" style="-webkit-flex: 1;flex: 1;">￥{{form.ad_price1}}</view>
			</view>
			<view class="uni-flex uni-row">
				<view class="text-left" style="width: 360rpx;">投放广告框总价</view>
				<view class="uni-common-pl text-right color-red" style="-webkit-flex: 1;flex: 1;">￥{{form.ad_price2}}</view>
			</view>
			<view class="uni-flex uni-row">
				<view class="text-left" style="width: 360rpx;">广告总价</view>
				<view class="uni-common-pl text-right color-red" style="-webkit-flex: 1;flex: 1;font-size: 36upx;">￥{{form.ad_price}}</view>
			</view>
		</uni-card>
		
		<view class="uni-padding-wrap uni-common-mt mb">
			<button @click="submitForm()" type="" class="bg-main-color color-white">确认提交</button>
			
			<button v-if="userInfo.user_id == 1 ? true : false" @click="pay()" class="uni-common-mt">支付</button>
		</view>
	</view>
</template>

<script>
	import {mapState} from 'vuex';
	import LyTree from '@/components/ly-tree/ly-tree.vue'

	var _self;

	export default {
		components: {
			LyTree
		},
		data() {
			return {
				form: {
					play_days: 7, // 投放天数
					startdate: '', // 开始日期
					region_ids: [], // 区域ID集合（数组）
					ad_cate_id: [], // 广告所属行业类别ID
					device_ids: [], // 投放广告设备ID集合
					ad_price: 0, // 广告设备（屏+框）投放总价格
					ad_price1: 0, // 广告屏投放总价格
					ad_day_price1: 0, // 广告屏投放每天总价格
					ad_price2: 0, // 广告框投放总价格
					ad_day_price2: 0 // 广告框投放每天总价格
				},

				adCateList: [{cate_id: 1, cate_name: '餐饮'}], // 广告所属行业类别列表
				adCateIndex: 0,

				// SegmentedControl 分段器
				segmentedControl: {
					items: ['附近', '全区域'],
					current: 0
				},
				
				distanceList: [{distance_id: 1, distance: 5}, {distance_id: 2, distance: 10}], // 投放距离列表
				distanceIndex: 0,
				longitude: '', // 经度 104.065922
				latitude: '', // 纬度 30.659903
				scale: 11,
				circles: [{
					latitude: '', // 30.659903
					longitude: '', // 104.065922
					radius: 5000,
					strokeWidth: 2,
					color: '#4C85FC01',
					fillColor: '#4C85FC33'
				}],

				/* scroll-view 纵向滚动 s */
				scrollTop: 0,
				old: {
					scrollTop: 0
				},
				/* scroll-view 纵向滚动 e */

				/* 区域 Tree 树形数据 s */
				isReady: false, // 为了确保页面加载完成后才去调用load方法，this指向正确
				props: {
					label: 'region_name',
					// children: 'zones',
					isLeaf: 'leaf'
				},
				/* 区域 Tree 树形数据 e */
				
				// 广告设备类别 SegmentedControl 分段器
				deviceCateSegmentedControl: {
					items: ['广告屏', '广告框'],
					current: 0
				},
				
				deviceList: [], // 广告设备（屏+框）列表 [{device_id: '', shop_name: ''}]
				device1Count: 0, // 广告屏总数量
				device2Count: 0, // 广告框总数量
				markers: [], //地图标记点
				checkedDeviceCount: 0, // 选中的广告设备（屏+框）数量
				checkedDevice1Count: 0, // 选中的广告屏数量
				checkedDevice2Count: 0 // 选中的广告框数量
			}
		},
		computed: {
			...mapState(['hasLogin', 'forcedLogin', 'userInfo', 'commonheader'])
		},
		onLoad() {
			this.getAdCateList();
			
			//初始化日历
			let mydate=new Date();
			let month=mydate.getMonth()+1;
			this.form.startdate=mydate.getFullYear()+'-'+month+'-'+mydate.getDate();
			
			this.getLocation();

			_self = this;
			this.isReady = true;
		},
		onNavigationBarButtonTap(e) {
			this.$common.actionSheetTap();
		},
		methods: {
			/**
			 * 日历
			 */
			open(){
				this.$refs.calendar.open();
			},
			confirm(e) {
				this.form.startdate = e.fulldate;
			},
			
			/**
			 * 投放天数输入框输入时触发
			 * @param {Object} event
			 */
			playDaysInput(event) {
				this.form.play_days = event.detail.value;
				/* if (!(/(^[1-9]\d*$)/.test(this.form.play_days))) {
					uni.showToast({
						icon: 'none',
						title: '请输入正整数'
					});
					return;
				} */
				
				// 广告总价
				this.form.ad_price1 = (this.form.ad_day_price1 * this.form.play_days).toFixed(2);
				this.form.ad_price2 = (this.form.ad_day_price2 * this.form.play_days).toFixed(2);
				this.form.ad_price = (Number(this.form.ad_price1) + Number(this.form.ad_price2)).toFixed(2);
			},

			/**
			 * 获取广告所属行业类别列表
			 */
			getAdCateList() {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/ad_cate_list',
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'GET',
					success: function(res) {
						if (res.data.status == 1) {
							res.data.data.forEach((value, index) => {
								self.$set(self.adCateList, index, {
									cate_id: value.cate_id,
									cate_name: value.cate_name
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
				})
			},

			/**
			 * 改变选择广告所属行业类别
			 * @param {Object} e
			 */
			bindAdCatePickerChange: function(e) {
				// console.log('picker发送选择改变，携带值为', e.target.value)
				this.adCateIndex = e.target.value;
				if (typeof(this.adCateIndex) != 'undefined') {
					this.getDeviceList();
				}
			},
			
			/**
			 * 改变选择广告投放距离
			 * @param {Object} e
			 */
			bindDistancePickerChange: function(e) {
				// console.log('picker发送选择改变，携带值为', e.target.value)
				this.distanceIndex = e.target.value;
				if (typeof(this.distanceIndex) != 'undefined') {
					// this.getDeviceList();
					this.getLocation();
				}
			},

			/**
			 * 广告投放区域 SegmentedControl 分段器组件触发点击事件时触发
			 * @param {Object} e
			 */
			onClickItem(e) {
				if (this.segmentedControl.current !== e.currentIndex) {
					this.segmentedControl.current = e.currentIndex;
					
					// 当选择附近区域时，获取当前地理位置
					if (this.segmentedControl.current == 0) {
						this.getLocation();
						
						// 初始化区域ID集合
						this.form.region_ids = [];
					} else if (this.segmentedControl.current == 1) {
						// 初始化设备列表、数量及广告价格
						// this.deviceList.splice(0, this.deviceList.length);
						this.deviceList.forEach((item, index) => {
							this.$set(this.deviceList, index, {})
						})
						this.deviceList = [];
						this.device1Count = 0;
						this.device2Count = 0;
						this.markers = [];
						this.checkedDeviceCount = 0;
						this.checkedDevice1Count = 0;
						this.checkedDevice2Count = 0;
						this.form.ad_price1 = 0;
						this.form.ad_day_price1 = 0;
						this.form.ad_price2 = 0;
						this.form.ad_day_price2 = 0;
						this.form.ad_price = 0;
						
						// 初始化广告投放定位距离、经纬度
						this.longitude = '';
						this.latitude = '';
					}
				}
			},
			
			/**
			 * 获取当前地理位置
			 */
			getLocation() {
				let self = this;
				uni.getLocation({
					type: 'wgs84',
					success: function (res) {
						 //console.log('当前位置的经度：' + res.longitude);
						 //console.log('当前位置的纬度：' + res.latitude);
						self.longitude = res.longitude;
						self.latitude = res.latitude;
						if (self.longitude && self.latitude) {
							self.getDeviceList();
							
							self.circles = [{
								latitude: self.latitude,
								longitude: self.longitude,
								radius: self.distanceList[self.distanceIndex].distance * 1000,
								strokeWidth: 2,
								color: '#4C85FC01',
								fillColor: '#4C85FC33'
							}]
						}
					}
				});
			},
			
			/* scroll-view 纵向滚动 s */
			scroll: function(e) {
				// console.log(e)
				this.old.scrollTop = e.detail.scrollTop
			},
			goTop: function(e) {
				// 解决view层不同步的问题
				this.scrollTop = this.old.scrollTop
				this.$nextTick(function() {
					this.scrollTop = 0
				});
				/* uni.showToast({
					icon:"none",
					title:"纵向滚动 scrollTop 值已被修改为 0"
				}) */
			},
			/* scroll-view 纵向滚动 e */

			/* 区域 Tree 树形数据 s */
			/**
			 * 懒加载（区域） Tree 树形数据
			 * @param {Object} node
			 * @param {Object} resolve
			 */
			loadNode(node, resolve) {
				// _self.xxx; 这里用_self而不是this
				
				// 首次进入查询第一级
				let parent_id = 33008; // 成都市
				let level = 3;
				if (node.data) { // 逐级查询
					parent_id = node.data.region_id;
					level = node.data.level + 1;
				}
				
				uni.request({
					url: this.$serverUrl + 'api/lazy_load_region_tree',
					data: {
						parent_id: parent_id //父级ID
					},
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'GET',
					success:function(res){
						if (res.data.status == 1) {
							const data = res.data.data;
							data.forEach((value, index) => {
								// 当不存在子级时，指定节点为叶子节点
								if (value.children_count == 0) {
									value.leaf = true;
								}
							})
						
							setTimeout(() => {
								resolve(data);
							}, 500);
						}
					},
					fail(error) {
						uni.showToast({
							icon: 'none',
							title: '请求异常'
						});
					}
				})
			},
			
			// 只有在"点击"修改的指定节点会触发(也就是说这个方法只会触发一次)，obj中包含当前选中
			handleCheck(obj) {
				// obj: {
				// 	checkedKeys: [9, 5], // 当前选中节点的id数组
				// 	checkedNodes: [{...}, {...}], // 当前选中节点数组
				// 	data: {...}, // 当前节点的数据
				// 	halfCheckedKeys: [1, 4, 2], // 半选中节点的id数组
				// 	halfCheckedNodes: [{...}, {...}, {...}], // 半选中节点的数组
				// 	node: Node {...} // 当前节点实例
				// }
				
				// console.log('handleCheck', obj);
				
				// 获取投放区域ID集合（含全选与半选）
				let checkedRegionIds = this.$refs.tree.getCheckedKeys(); // 被选中的节点的 key 所组成的数组
				let halfCheckedRegionIds = this.$refs.tree.getHalfCheckedKeys(); // 半选中的节点的 key 所组成的数组
				this.form.region_ids = checkedRegionIds.length != 0 ? [checkedRegionIds, halfCheckedRegionIds] : []; // 判断全选是否为空 checkedRegionIds.length ?= 0，用于验证 Tree 树形在表单中的选中状态
				
				this.getDeviceList();
			},
			/* 区域 Tree 树形数据 e */
			
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
			 * 获取广告屏列表
			 */
			getDeviceList() {
				let self = this;
				
				// 初始化设备列表、数量及广告价格
				this.deviceList.forEach((item, index) => {
					// console.log(12311, typeof(item));return;
					this.$set(this.deviceList, index, {})
				})
				this.deviceList = [];
				this.device1Count = 0;
				this.device2Count = 0;
				this.markers = [];
				this.checkedDeviceCount = 0;
				this.checkedDevice1Count = 0;
				this.checkedDevice2Count = 0;
				this.form.ad_price1 = 0;
				this.form.ad_day_price1 = 0;
				this.form.ad_price2 = 0;
				this.form.ad_day_price2 = 0;
				this.form.ad_price = 0;
				
				this.form.device_ids = ''; // 初始化选中的广告屏
				this.form.ad_cate_id = this.adCateList[this.adCateIndex].cate_id;
				let _data; // 定义请求接口 data 参数
				// let showModalContent = '';
				// 判断是否投放附近区域
				if (this.segmentedControl.current === 0 && this.longitude && this.latitude && this.distanceList[this.distanceIndex].distance) { // 附近区域
					_data = {
						role_id: 7, // 广告主
						ad_cate_id: this.form.ad_cate_id, // 广告所属行业类别
						longitude: this.longitude,
						latitude: this.latitude,
						distance: this.distanceList[this.distanceIndex].distance
					}
					
					// showModalContent = '请重新选择“投放距离”或“广告所属行业类别”';
				} else if (this.segmentedControl.current === 1 && this.$refs.tree.getCheckedKeys().length != 0 && this.form.ad_cate_id) { // 全区域
					_data = {
						role_id: 7, // 广告主
						region_ids: this.$refs.tree.getCheckedKeys(), // 投放区域ID集合（只含全选）
						ad_cate_id: this.form.ad_cate_id // 广告所属行业类别
					}
					// showModalContent = '请重新选择“投放区域”或“广告所属行业类别”';
				}
				
				// 广告类别
				if (_data) {
					uni.request({
						url: this.$serverUrl + 'api/device_list',
						data: _data,
						header: {
							'commonheader': this.commonheader,
							'access-user-token': this.userInfo.token
						},
						method: 'GET',
						success: function(res) {
							if (res.data.status == 1) {
								let checkedDeviceCount = 0;
								let checkedDevice1Count = 0;
								let checkedDevice2Count = 0;
								let adPrice1 = 0;
								let adPrice2 = 0;
								res.data.data.forEach((value, index) => {
									// 广告设备（屏+框）列表
									let thumb = typeof(JSON.parse(value.url_image)[0]) != 'undefined' ? JSON.parse(value.url_image)[0].url : '';
									self.$set(self.deviceList, index, {
										device_id: value.device_id.toString(),
										device_cate: value.device_cate,
										shop_name: value.shop_name,
										address: value.address,
										ad_unit_price: value.ad_unit_price,
										thumb: thumb,
										checked: true
									});
									
									// 获取实际的广告屏或广告框数量
									if (value.device_cate == 1) {
										self.device1Count ++;
									}
									if (value.device_cate == 2) {
										self.device2Count ++;
									}
									
									// 地图标记点
									self.$set(self.markers, index, {
										title: value.shop_name /* + ' ' + (value.device_cate == 1 ? '[屏]' : '[框]') */,
										longitude: value.longitude,
										latitude: value.latitude
									});
									
									if (self.deviceList[index].checked == true) {
										checkedDeviceCount ++;
										if (value.device_cate == 1) {
											checkedDevice1Count ++;
											adPrice1 = adPrice1 + value.ad_unit_price;
										}
										if (value.device_cate == 2) {
											checkedDevice2Count ++;
											adPrice2 = adPrice2 + value.ad_unit_price;
										}
									}
								})
								
								// 广告设备数量
								self.checkedDeviceCount = checkedDeviceCount;
								self.checkedDevice1Count = checkedDevice1Count;
								self.checkedDevice2Count = checkedDevice2Count;
								// 广告总价
								self.form.ad_price1 = (adPrice1 * self.form.play_days).toFixed(2);
								self.form.ad_day_price1 = adPrice1.toFixed(2);
								self.form.ad_price2 = (adPrice2 * self.form.play_days).toFixed(2);
								self.form.ad_day_price2 = adPrice2.toFixed(2);
								self.form.ad_price = (Number(self.form.ad_price1) + Number(self.form.ad_price2)).toFixed(2);
							}/* else {
								uni.showModal({
									title: '获取广告屏失败',
									content: showModalContent,
									showCancel: false
								});
							} */
						},
						fail(error) {
							uni.showToast({
								icon: 'none',
								title: '请求异常'
							});
						}
					})
				} else {
					this.deviceList = []; // 初始化设备列表
					this.markers = [];
					this.form.ad_price1 = 0;
					this.form.ad_day_price1 = 0;
					this.checkedDeviceCount = 0;
				}
			},
			
			/**
			 * 改变选择广告屏
			 * @param {Object} e
			 */
			deviceCheckboxChange(e) {
				// console.log('deviceCheckboxChange', e)
				this.form.device_ids = e.detail.value;
				this.checkedDeviceCount = e.detail.value.length;
				
				// 计算广告设备数量、广告总价
				let checkedDevice1Count = 0;
				let checkedDevice2Count = 0;
				let adPrice1 = 0;
				let adPrice2 = 0;
				this.form.device_ids.forEach((item, index) => {
					this.deviceList.forEach((value, key) => {
						if (Number(item) === Number(value.device_id)) {
							if (value.device_cate == 1) {
								checkedDevice1Count ++;
								adPrice1 = adPrice1 + value.ad_unit_price;
							}
							if (value.device_cate == 2) {
								checkedDevice2Count ++;
								adPrice2 = adPrice2 + value.ad_unit_price;
							}
						}
					})
				})
				
				// 广告设备数量
				this.checkedDevice1Count = checkedDevice1Count;
				this.checkedDevice2Count = checkedDevice2Count;
				
				// 广告总价
				this.form.ad_price1 = (adPrice1 * this.form.play_days).toFixed(2);
				this.form.ad_day_price1 = adPrice1.toFixed(2);
				this.form.ad_price2 = (adPrice2 * this.form.play_days).toFixed(2);
				this.form.ad_day_price2 = adPrice2.toFixed(2);
				this.form.ad_price = (Number(this.form.ad_price1) + Number(this.form.ad_price2)).toFixed(2);
			},
			
			/**
			 * 投放广告提交表单
			 */
			submitForm() {
				// 自定义验证表单
				this.form.ad_cate_id = this.adCateList[this.adCateIndex].cate_id;
				if (this.form.ad_cate_id == '') {
					uni.showToast({
						icon: 'none',
						title: '请选择广告类别'
					});
					return false;
				}
				if (this.form.play_days == '') {
					uni.showToast({
						icon: 'none',
						title: '请输入投放天数'
					});
					return false;
				}
				if (this.segmentedControl.current === 1 && this.form.region_ids == '') {
					uni.showToast({
						icon: 'none',
						title: '请选择投放区域'
					});
					return false;
				}
				if (this.form.device_ids == '') {
					uni.showToast({
						icon: 'none',
						title: '请选择投放广告屏'
					});
					return false;
				}
				
				// 发起网络请求，提交服务端
				let distance = this.distanceList[this.distanceIndex].distance; // 广告投放定位距离
				if (this.segmentedControl.current == 1) {
					distance = '';
				}
				uni.request({
					url: this.$serverUrl + 'api/ad',
					data: {
						play_days: this.form.play_days,
						startdate: this.form.startdate,
						region_ids: this.form.region_ids,
						distance: distance, // 广告投放定位距离
						longitude: this.longitude, // 广告投放定位经度
						latitude: this.latitude, // 广告投放定位纬度
						ad_cate_id: this.form.ad_cate_id,
						device_ids: this.form.device_ids,
						ad_price: this.form.ad_price
					},
					header:{
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'POST',
					success: function(res) {
						if (0 == res.data.status) { // 提交失败
							uni.showToast({
								icon: 'none',
								title: res.data.message
							});
							return;
						} else { // 提交成功跳转
							if (res.data.data.ad_id) {
								let ad_id = res.data.data.ad_id;
								uni.showModal({
									title: '提示',
									content: '广告投放数据提交成功，去完成支付',
									showCancel: false,
									success: function (res) {
										if (res.confirm == true) {
											uni.navigateTo({
												url: '/pages/ad/payad?ad_id=' + ad_id
											})
											return false;
										}
									}
								})
							}
						}
					},
					fail: function(error) {
						
					}
				})
			},
			
			/**
			 * 支付测试
			 */
			pay(){
				let self = this;
				
				uni.request({
					url: this.$serverUrl + 'api/wxPay',
					// url: 'http://demo.tp5.com/index.php/api/test',
					header:{
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'POST',
					success: function(res) {
						// console.log(123, res);
						uni.showModal({
						    title: '提示 success',
						    content: res.data.message,
						})
						
						if (res.data.status == 1) {
							// 微信JSAPI调起支付
							if (typeof WeixinJSBridge == "undefined"){
								if ( document.addEventListener ){
									document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
								} else if (document.attachEvent){
									document.attachEvent('WeixinJSBridgeReady', onBridgeReady); 
									document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
								}
							}else{
								if (res.data.data.jsApiParameters){
									self.onBridgeReady(res.data.data.jsApiParameters);
								}
							}
						}
					},
					fail: function(error) {
						// console.log(220, error);
						uni.showModal({
						    title: '提示 fail',
						    content: error.data.message,
						})
					}
				})
			},
			
			/**
			 * 微信JSAPI调起支付
			 * @param {Object} jsApiParameters
			 * jsApiParameters示例：
			 * {
				 "appId":"wx2421b1c4370ec43b",     //公众号名称，由商户传入     
				 "timeStamp":"1395712654",         //时间戳，自1970年以来的秒数     
				 "nonceStr":"e61463f8efa94090b1f366cccfbbb444", //随机串     
				 "package":"prepay_id=u802345jgfjsdfgsdg888",     
				 "signType":"MD5",         //微信签名方式：     
				 "paySign":"70EA570631E4BB79628FBCA90534C63FF7FADD89" //微信签名 
			   }
			 */
			onBridgeReady(jsApiParameters){
				WeixinJSBridge.invoke(
					'getBrandWCPayRequest',
					JSON.parse(jsApiParameters), // 由JSON字符串转换为JSON对象
					function(res){
					if(res.err_msg == "get_brand_wcpay_request:ok" ){
						// 使用以上方式判断前端返回,微信团队郑重提示：
						//res.err_msg将在用户支付成功后返回ok，但并不保证它绝对可靠。
						uni.showModal({
							title: '提示 success',
							content: res.err_msg
						})
					}
				}); 
			},
			
			//跳转到设备详情
			toDeviceDetail2(device_id) {
				uni.navigateTo({
					url: '/pages/device/device-detail2?device_id=' + device_id
				});
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

	.input-line-height {
		display: flex;
		align-items: center;
		line-height: 50px;
		border-bottom: 1px solid #ECECEC;
		font-size: 15px;
	}

	.input-line-height-1 {
		flex-basis: 70px;
		line-height: 50px;
		font-size: 15px;
		text-align: right;
	}

	.input-line-height-2 {
		flex: 3;
		font-size: 15px;
		text-align: left;
	}

	.line-blue {
		font-size: 18px;
		margin-left: 5px;
	}
	
	/* scroll-view 纵向滚动 s */
	.scroll-Y {
		height: 600rpx;
	}
	/* scroll-view 纵向滚动 e */
</style>
