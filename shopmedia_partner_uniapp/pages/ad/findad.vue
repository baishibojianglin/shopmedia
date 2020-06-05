<template>
	<view class="content">
		
		<uni-card :is-shadow="true">
			<view>
				<view class="input-line-height" >
					<view class="input-line-height-1">所属行业 <text class="main-color line-blue">|</text></view>
					<picker @change="bindCatePickerChange" class="input-line-height-2" :value="CateIndex" :range="CateList" range-key="cate_name">
						<view style="font-size: 15px; padding-left: 15px;">{{CateList[CateIndex].cate_name}}</view>
					</picker>
				</view>
				<view class="input-line-height">
					<view class="input-line-height-1">开始日期 <text class="main-color line-blue">|</text></view>
					<view class="input-line-height-2"  style="font-size: 15px; padding-left: 15px;">{{startdate}}</view>
					<uni-calendar
					ref="calendar"
					:start-date="'2020-5-30'"
					:insert="false"
					@confirm="confirm"
					 />	
					<button class="bg-main-color color-white" style="font-size: 14px;width: 80px;" @click="open">选择</button>
				</view>
			</view>	
		</uni-card>
		
		<view class="line4 fon16 main-color">选择投放区域</view>
		<uni-card :is-shadow="true">
			
			<!-- SegmentedControl 分段器 s -->
			<view>
				<uni-segmented-control :current="segmentedControl.current" :values="segmentedControl.items" @clickItem="onClickItem" style-type="button" active-color="#409EFF"></uni-segmented-control>
				<view class="">
					<view v-if="segmentedControl.current === 0">
						<ly-tree v-if="isReady" :props="props" node-key="name" :load="loadNode" lazy />
					</view>
					<view v-if="segmentedControl.current === 1">
						1
					</view>
				</view>
			</view>
			<!-- SegmentedControl 分段器 e -->
			
			<view v-if="false">
				<view class="input-line-height" >
					<view class="input-line-height-1">投放省份 <text class="main-color line-blue">|</text></view>
					<picker @change="bindProvincePickerChange" class="input-line-height-2" :value="ProvinceIndex" :range="ProvinceList" range-key="province_name">
						<view style="font-size: 15px; padding-left: 15px;">{{ProvinceList[ProvinceIndex].region_name}}</view>
						<input v-show="false" type="text" v-model="cate" />
					</picker>
				</view>
				<view>
					<view>
						<view>广告屏：<span class="color-red">{{device_number}} 台</span></view>
						<view>广告费：<span class="color-red">{{device_money}} 元</span></view>						
					</view>

					<uni-list>
					    <uni-list-item title="标题文字" :show-arrow="false"></uni-list-item>
					</uni-list>
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
				cate:'',//广告类别id
				days:7,//投放天数
				startdate:'',//投放开始时间
				range:0,//投放范围
				province:'',//省份id
				
				device_number:'',//广告屏数量
				device_money:0,//广告费
	
				CateList: [{cate_id: '', cate_name: ''}], // 广告类别列表
				CateIndex:0,

				RangeList: [
					{range_id: 0, range_name: '全平台'},
					{range_id: 1, range_name: '分区域'}
				], // 广告类别列表
				RangeIndex:0,				
				
				ProvinceList:[{region_id:'',region_name:''}],
				ProvinceIndex:0,
				// SegmentedControl 分段器
				segmentedControl: {
					items: ['全区域', '附近'],
					current: 0
				},
				
				isReady: false,
				props: {
					label: 'name',
					children: 'zones',
					isLeaf: 'leaf'
				}
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
			getProvince(parent_id){
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/get-province',
					data:{
						parent_id:parent_id
					},
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'POST',
					success: function(res) {
							res.data.forEach((value,index)=>{
								//省级
								if(value.level==1){
								  self.$set(self.ProvinceList,index,{region_id:value.region_id,region_name:value.region_name});									
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
							res.data.data.forEach((value,index)=>{
								self.$set(self.CateList,index,{cate_id:value.cate_id,cate_name:value.cate_name});
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
				this.cate=e.target.value;
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
				this.province=e.target.value;
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
			
			loadNode(node, resolve) {
				// _self.xxx; 这里用_self而不是this
				
				if (node.level === 0) {
					setTimeout(() => {
						resolve([{
							name: 'region'
						}]);
					}, 1000);
				} else {
					if (node.level > 1) return resolve([]);
					
					setTimeout(() => {
						const data = [{
							name: 'leaf',
							leaf: true
						}, {
							name: 'zone'
						}];
					
						resolve(data);
					}, 500);
				}
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

	.input-line-height{
		display: flex;
		align-items:center;
		line-height: 50px;
		border-bottom:1px solid #ECECEC; 
		font-size:15px;
	}
	.input-line-height-1{
		flex-basis:70px;
		line-height: 50px;
		font-size:15px;
		text-align: right;
	}
	.input-line-height-2{
		flex:3;
		font-size: 15px;
		text-align: left;
	}
	.line-blue{
		font-size: 18px;
		margin-left: 5px;
	}
</style>
