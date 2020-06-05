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
				<view class="input-line-height">
					<view class="input-line-height-1">投放天数 <text class="main-color line-blue">|</text></view>
					<input class="input-line-height-2" style="font-size: 15px; padding-left: 15px;" type="number"  v-model="days" />天
				</view>
				<view class="input-line-height">
					<view class="input-line-height-1">投放范围 <text class="main-color line-blue">|</text></view>
					<picker @change="bindRangePickerChange" class="input-line-height-2" :value="RangeIndex" :range="RangeList" range-key="range_name">
						<view style="font-size: 15px; padding-left: 15px;">{{RangeList[RangeIndex].range_name}}</view>
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
	
	export default {
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
