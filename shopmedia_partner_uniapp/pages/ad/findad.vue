<template>
	<view class="content">
		<view class="line4 fon16 main-color">填写基本信息</view>
		<uni-card :is-shadow="true">
			<view>
				<view class="input-line-height">
					<view class="input-line-height-1">所属行业 <text class="main-color line-blue">|</text></view>
					<picker @change="bindShopCatePickerChange" class="input-line-height-2" :value="shopCateIndex" :range="shopCateList" range-key="cate_name">
						<view style="font-size: 15px; padding-left: 15px;">{{shopCateList[shopCateIndex].cate_name}}</view>
					</picker>
				</view>
				<view class="input-line-height">
					<view class="input-line-height-1">投放天数 <text class="main-color line-blue">|</text></view>
					<input class="input-line-height-2" style="font-size: 15px; padding-left: 15px;" type="number" v-model="form.play_days" />天</view>
				<view class="input-line-height">
					<view class="input-line-height-1">开始日期 <text class="main-color line-blue">|</text></view>
					<view class="input-line-height-2" style="font-size: 15px; padding-left: 15px;">{{form.startdate}}</view>
					<uni-calendar ref="calendar" :insert="false" @confirm="confirm" />
					<button style="font-size: 15px;width: 80px;" @click="open">选择</button>
				</view>
			</view>
		</uni-card>

		<view class="line4 fon16 main-color">选择投放区域</view>
		<uni-card :is-shadow="true">
			<!-- SegmentedControl 分段器 s -->
			<view>
				<uni-segmented-control :current="segmentedControl.current" :values="segmentedControl.items" @clickItem="onClickItem"
				 style-type="button" active-color="#409EFF"></uni-segmented-control>
				<view class="">
					<view v-if="segmentedControl.current === 0">
						<!-- scroll-view 纵向滚动 s -->
						<scroll-view :scroll-top="scrollTop" scroll-y="true" class="scroll-Y" @scroll="scroll">
							<!-- 区域 Tree 树形数据 -->
							<ly-tree ref="tree" v-if="isReady" :props="props" node-key="region_id" :load="loadNode" lazy show-checkbox @check="handleCheck" />
						</scroll-view>
						<view v-if="old.scrollTop > 200" @tap="goTop" class="uni-link uni-center uni-common-mt">返回顶部</view>
						<!-- scroll-view 纵向滚动 e -->
					</view>
					<view v-if="segmentedControl.current === 1">
						<view class="input-line-height">
							<view class="input-line-height-1">投放距离 <text class="main-color line-blue">|</text></view>
							<picker @change="bindDistancePickerChange" class="input-line-height-2" :value="distanceIndex" :range="distanceList" range-key="distance">
								<view style="font-size: 15px; padding-left: 15px;">{{distanceList[distanceIndex].distance}}㎞</view>
							</picker>
						</view>
						
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
			<!-- SegmentedControl 分段器 e -->
		</uni-card>
		
		<view class="line4 fon16 main-color">选择投放广告屏</view>
		<uni-card :is-shadow="true">
			<view class="uni-list">
				<checkbox-group @change="deviceCheckboxChange">
					<label class="uni-list-cell uni-list-cell-pd" v-for="item in deviceList" :key="item.device_id">
						<view>
							<checkbox :value="item.device_id" :checked="item.checked" />
						</view>
						<view>屏编号：{{item.device_id}}，店铺：{{item.shop_name}}（地址：{{item.address}}）</view>
					</label>
				</checkbox-group>
			</view>
		</uni-card>
		
		<uni-card :is-shadow="true">
			<view class="uni-bold">
				广告总价：
				<text class="color-red">￥{{form.ad_price}}</text>
			</view>
		</uni-card>
		
		<view class="uni-padding-wrap uni-common-mt mb">
			<button @click="submitForm()" class="bg-main-color color-white">提 交</button>
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
					shop_cate_ids: [], // 投放店铺类别ID集合 // cate: '', // 店铺类别id
					device_ids: [], // 投放广告设备ID集合
					ad_price: '' // 广告价格
				},

				shopCateList: [{cate_id: '', cate_name: ''}], // 店铺类别列表
				shopCateIndex: 0,

				// SegmentedControl 分段器
				segmentedControl: {
					items: ['全区域', '附近'],
					current: 0
				},
				
				distanceList: [{distance_id: 1, distance: 5}, {distance_id: 2, distance: 10}], // 投放距离列表
				distanceIndex: 0,
				longitude: '', // 经度
				latitude: '', // 纬度

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
				
				deviceList: [], // 广告屏列表 [{device_id: '', shop_name: ''}]
			}
		},
		computed: {
			...mapState(['hasLogin', 'forcedLogin', 'userInfo', 'commonheader'])
		},
		onLoad() {
			this.getShopCateList();
			//初始化日历
			let mydate=new Date();
			let month=mydate.getMonth()+1;
			this.form.startdate=mydate.getFullYear()+'-'+month+'-'+mydate.getDate();
			//广告屏数量
			this.getDeviceNumber();

			_self = this;
			this.isReady = true;
		},
		onNavigationBarButtonTap(e) {
			this.$common.actionSheetTap();
		},
		methods: {
			/**
			 * 获取广告屏数量
			 */
			getDeviceNumber(){
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/get-device-number',
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'GET',
					success: function(res) {
						self.device_number=res.data;
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
			 * 日历
			 */
			open(){
				this.$refs.calendar.open();
			},
			confirm(e) {
				this.form.startdate = e.fulldate;
			},

			/**
			 * 获取店铺类别列表
			 */
			getShopCateList() {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/shop_cate_list',
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'GET',
					success: function(res) {
						if (res.data.status == 1) {
							res.data.data.forEach((value, index) => {
								self.$set(self.shopCateList, index, {
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
			 * 改变选择店铺类别
			 * @param {Object} e
			 */
			bindShopCatePickerChange: function(e) {
				// console.log('picker发送选择改变，携带值为', e.target.value)
				this.shopCateIndex = e.target.value;
				if (typeof(this.shopCateIndex) != 'undefined') {
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
			},

			/**
			 * SegmentedControl 分段器组件触发点击事件时触发
			 * @param {Object} e
			 */
			onClickItem(e) {
				if (this.segmentedControl.current !== e.currentIndex) {
					this.segmentedControl.current = e.currentIndex;
					
					// 当选择附近区域时，获取当前地理位置
					if (this.segmentedControl.current == 1) {
						this.getLocation();
					}
				}
			},
			
			/**
			 * 获取当前地理位置
			 */
			getLocation() {
				let self = this;
				uni.showModal({
					title: '授权定位',
					content: '获取你的地理位置',
					success: function (res) {
						if (res.confirm) {
							uni.getLocation({
							    type: 'wgs84',
							    success: function (res1) {
							        console.log('当前位置的经度：' + res1.longitude);
							        console.log('当前位置的纬度：' + res1.latitude);
									self.longitude = res1.longitude;
									self.latitude = res1.latitude;
							    }
							});
						} else if (res.cancel) {
							self.segmentedControl.current = 0;
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
				console.log('longitude', this.longitude)
				console.log('latitude', this.latitude)
				console.log('distance', this.distanceList[this.distanceIndex].distance)
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
						parent_id: parent_id, //父级ID
						longitude: this.longitude, // 经度
						latitude: this.latitude, // 纬度
						distance: this.distanceList[this.distanceIndex].distance // 投放距离
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
			 * 获取广告屏列表
			 */
			getDeviceList() {
				let self = this;
				this.form.device_ids = ''; // 初始化选中的广告屏
				this.form.shop_cate_ids = this.shopCateList[this.shopCateIndex].cate_id;
				if (this.$refs.tree.getCheckedKeys().length != 0 && this.form.shop_cate_ids) {
					uni.request({
						url: this.$serverUrl + 'api/device_list',
						data: {
							region_ids: this.$refs.tree.getCheckedKeys(), // 投放区域ID集合（只含全选）
							shop_cate_ids: this.form.shop_cate_ids // 投放店铺类别ID集合（这里只有一个值）
						},
						header: {
							'commonheader': this.commonheader,
							'access-user-token': this.userInfo.token
						},
						method: 'GET',
						success: function(res) {
							self.deviceList = []; // 初始化设备列表
							if (res.data.status == 1) {
								console.log(123, res.data)
								// self.deviceList = res.data.data;
								let adPrice = 0;
								res.data.data.forEach((value, index) => {
									self.$set(self.deviceList, index, {
										device_id: value.device_id.toString(),
										shop_name: value.shop_name,
										address: value.address,
										ad_unit_price: value.ad_unit_price,
										checked: true
									});
									if (self.deviceList[index].checked == true) {
										adPrice = adPrice + value.ad_unit_price;
									}
								})
								self.form.ad_price = (adPrice * self.form.play_days).toFixed(2);
							}
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
				}
			},
			
			/**
			 * 改变选择广告屏
			 * @param {Object} e
			 */
			deviceCheckboxChange(e) {
				console.log('deviceCheckboxChange', e)
				this.form.device_ids = e.detail.value;
				
				// 计算广告总价
				let adPrice = 0;
				this.form.device_ids.forEach((item, index) => {
					this.deviceList.forEach((value, key) => {
						if (Number(item) === Number(value.device_id)) {
							adPrice = adPrice + value.ad_unit_price;
						}
					})
				})
				this.form.ad_price = (adPrice * this.form.play_days).toFixed(2);
			},
			
			/**
			 * 投放广告提交表单
			 */
			submitForm() {
				// 自定义验证表单
				this.form.shop_cate_ids = this.shopCateList[this.shopCateIndex].cate_id;
				if (this.form.shop_cate_ids == '') {
					uni.showToast({
						icon: 'none',
						title: '请选择所属行业'
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
				if (this.form.region_ids == '') {
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
				uni.request({
					url: this.$serverUrl + 'api/ad',
					data: {
						play_days: this.form.play_days,
						startdate: this.form.startdate,
						region_ids: this.form.region_ids,
						shop_cate_ids: this.form.shop_cate_ids,
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
						   uni.showModal({
						       title: '提示',
						       content: '广告投放成功',
							   showCancel: false,
						       success: function (res) {
						           if (res.confirm == true) {
										uni.switchTab({
											url: '/pages/user/user'
										})
						           }
						       }
						   })
						}
					},
					fail: function(error) {
						
					}
				})
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
