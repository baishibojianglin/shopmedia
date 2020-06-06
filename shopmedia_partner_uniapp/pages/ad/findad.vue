<template>
	<view class="content">

		<view class="line4 fon16 main-color">填写基本信息</view>

		<uni-card :is-shadow="true">
			<view>
				<view class="input-line-height">
					<view class="input-line-height-1">所属行业 <text class="main-color line-blue">|</text></view>
					<picker @change="bindCatePickerChange" class="input-line-height-2" :value="CateIndex" :range="CateList" range-key="cate_name">
						<view style="font-size: 15px; padding-left: 15px;">{{CateList[CateIndex].cate_name}}</view>
					</picker>
				</view>
				<view class="input-line-height">

					<view class="input-line-height-1">投放天数 <text class="main-color line-blue">|</text></view>
					<input class="input-line-height-2" style="font-size: 15px; padding-left: 15px;" type="number" v-model="days" />天</view>
				<view class="input-line-height">
					<view class="input-line-height-1">开始日期 <text class="main-color line-blue">|</text></view>
					<view class="input-line-height-2">

					</view>
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
							<ly-tree v-if="isReady" :props="props" node-key="region_id" :load="loadNode" lazy show-checkbox @check="handleCheck" />
						</scroll-view>
						<view v-if="old.scrollTop > 200" @tap="goTop" class="uni-link uni-center uni-common-mt">返回顶部</view>
						<!-- scroll-view 纵向滚动 e -->
					</view>
					<view v-if="segmentedControl.current === 1">
						1
					</view>
				</view>
			</view>
			<!-- SegmentedControl 分段器 e -->

			<view v-if="false">
				<view class="input-line-height">
					<view class="input-line-height-1">投放省份 <text class="main-color line-blue">|</text></view>
					<picker @change="bindProvincePickerChange" class="input-line-height-2" :value="ProvinceIndex" :range="ProvinceList"
					 range-key="province_name">
						<view style="font-size: 15px; padding-left: 15px;">{{ProvinceList[ProvinceIndex].region_name}}</view>
						<input v-show="false" type="text" v-model="cate" />
					</picker>
				</view>
			</view>

		</uni-card>
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

				cate: '', //广告类别id
				days: '', //投放天数
				province: '', //省份id

				CateList: [{
					cate_id: '',
					cate_name: ''
				}], // 广告类别列表
				CateIndex: 0,

				ProvinceList: [{
					region_id: '',
					region_name: ''
				}],
				ProvinceIndex: 0,
				

				// SegmentedControl 分段器
				segmentedControl: {
					items: ['全区域', '附近'],
					current: 0
				},

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
				}
				/* 区域 Tree 树形数据 e */
			}
		},
		computed: {
			...mapState(['hasLogin', 'forcedLogin', 'userInfo', 'commonheader'])
		},
		onLoad() {
			this.getShopCateList();//类别
			//初始化日历
			let mydate=new Date();
			let month=mydate.getMonth()+1;
			this.startdate=mydate.getFullYear()+'-'+month+'-'+mydate.getDate();
			//广告屏数量
			this.getDeviceNumber();
			//获取区域
			this.getProvince(0);

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
				this.startdate=e.fulldate;
			},
			
			/**

			 * 获取区域
			 */
			getProvince(parent_id) {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/get-province',
					data: {
						parent_id: parent_id
					},
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'POST',
					success: function(res) {
						res.data.forEach((value, index) => {
							//省级
							if (value.level == 1) {
								self.$set(self.ProvinceList, index, {
									region_id: value.region_id,
									region_name: value.region_name
								});
							}
						})

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
								self.$set(self.CateList, index, {
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
			 * 广告类别
			 * @param {Object} e
			 */
			bindCatePickerChange: function(e) {
				// console.log('picker发送选择改变，携带值为', e.target.value)
				this.CateIndex = e.target.value;
				this.cate = e.target.value;
			},
			/**
			 * 投放范围
			 * @param {Object} e
			 */
			bindRangePickerChange: function(e) {
				// console.log('picker发送选择改变，携带值为', e.target.value)
				this.RangeIndex = e.target.value;
				this.range=e.target.value;
			},
			/**
			 * 投放范围
			 * @param {Object} e
			 */
			bindRangePickerChange: function(e) {
				// console.log('picker发送选择改变，携带值为', e.target.value)
				this.RangeIndex = e.target.value;
				this.range=e.target.value;
			},
			/**
			 * 选择地区
			 * @param {Object} e
			 */
			bindProvincePickerChange: function(e) {
				// console.log('picker发送选择改变，携带值为', e.target.value)
				this.ProvinceIndex = e.target.value;
				this.province = e.target.value;
			},

			/**
			 * SegmentedControl 分段器组件触发点击事件时触发
			 * @param {Object} e
			 */
			onClickItem(e) {
				if (this.segmentedControl.current !== e.currentIndex) {
					this.segmentedControl.current = e.currentIndex;
				}
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
				console.log(221, node)
				// 首次进入查询第一级
				let parent_id = 33008; // 四川省
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
				
				console.log('handleCheck', obj);
			}
			/* 区域 Tree 树形数据 e */
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
