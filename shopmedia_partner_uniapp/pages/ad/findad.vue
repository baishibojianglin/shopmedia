<template>
	<view class="content">
		
		<view class="line4 fon16 main-color">填写基本信息</view>
		<uni-card :is-shadow="true">
			<view>
				<view class="input-line-height" >
					<view class="input-line-height-1">所属行业 <text class="main-color line-blue">|</text></view>
					<picker @change="bindCatePickerChange" class="input-line-height-2" :value="CateIndex" :range="CateList" range-key="cate_name">
						<view style="font-size: 15px; padding-left: 15px;">{{CateList[CateIndex].cate_name}}</view>
					</picker>
				</view>
				<view class="input-line-height">
					<view class="input-line-height-1">投放天数 <text class="main-color line-blue">|</text></view>
					<input class="input-line-height-2" style="font-size: 15px; padding-left: 15px;" type="number"  v-model="days" />天
				</view>
				<view class="input-line-height">
					<view class="input-line-height-1">开始日期 <text class="main-color line-blue">|</text></view>
					<view class="input-line-height-2">
				 
					</view>
					<uni-calendar
					ref="calendar"
					:insert="false"
					@confirm="confirm"
					 />	
					<button style="font-size: 15px;width: 80px;" @click="open">选择</button>
				</view>
			</view>	
		</uni-card>
		
		<view class="line4 fon16 main-color">选择投放区域</view>
		<uni-card :is-shadow="true">
			<view>
				<view class="input-line-height" >
					<view class="input-line-height-1">投放省份 <text class="main-color line-blue">|</text></view>
					<picker @change="bindProvincePickerChange" class="input-line-height-2" :value="ProvinceIndex" :range="ProvinceList" range-key="province_name">
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
	
	export default {
		data() {
			return {
				cate:'',//广告类别id
				days:'',//投放天数
				province:'',//省份id
	
				CateList: [{cate_id: '', cate_name: ''}], // 广告类别列表
				CateIndex:0,
				
				ProvinceList:[{region_id:'',region_name:''}],
				ProvinceIndex:0
			}
		},
		computed: {
			...mapState(['hasLogin', 'forcedLogin', 'userInfo', 'commonheader'])
		},
		onLoad() {
			this.getShopCateList();
			this.getProvince(0);
		},
		onNavigationBarButtonTap(e) {
			this.$common.actionSheetTap();
		},
		methods: {
			        open(){
			            this.$refs.calendar.open();
			        },
			        confirm(e) {
			            console.log(e);
			        },
			
			/**
			 * 获取省份
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
