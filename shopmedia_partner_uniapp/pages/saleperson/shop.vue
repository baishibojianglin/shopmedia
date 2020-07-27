<template>
	<view class="uni-padding-wrap">

		<view>
			<view class="input-line-height">
				<view class="input-line-height-1">电话 <text class="main-color line-blue">|</text></view>
				<input class="input-line-height-2" type="text" v-model="phone" placeholder="联系手机号码" />
			</view>
			<view class="input-line-height">
				<view class="input-line-height-1">店名 <text class="main-color line-blue">|</text></view>
				<input class="input-line-height-2" type="text" v-model="shop_name" />
			</view>
			<view class="input-line-height" >
				<view class="input-line-height-1">类型 <text class="main-color line-blue">|</text></view>
				<picker @change="bindShopCatePickerChange" class="input-line-height-2" :value="shopCateIndex" :range="shopCateList" range-key="cate_name">
					<view style="font-size: 15px;">{{shopCateList[shopCateIndex].cate_name}}</view>
					<input v-show="false" type="text" v-model="cate" />
				</picker>
			</view>
			<view class="input-line-height" >
				<view class="input-line-height-1">环境 <text class="main-color line-blue">|</text></view>
				<picker @change="bindShopHjPickerChange" class="input-line-height-2" :value="environmentindex" :range="environmentlist" range-key="en_name">
					<view style="font-size: 15px;">{{environmentlist[environmentindex].en_name}}</view>
					<input v-show="false" type="text" v-model="environment" />
				</picker>
			</view>
			<view class="input-line-height">
				<view class="input-line-height-1">面积 <text class="main-color line-blue">|</text></view>
				<input class="input-line-height-2" type="number"  v-model="shop_area" />㎡
			</view>
			<view class="input-line-height">
				<view class="input-line-height-1">位置 <text class="main-color line-blue">|</text></view>
				<view class="input-line-height-2">
					<button v-show="!address" size="mini" @click="getlocation()" style="font-size: 14px; margin-top: 7px;">点击获取</button>
					<input v-show="address" type="text" style="font-size: 15px;" v-model="address" />
				</view>
			</view>
			<view class="input-line-height" v-show="false">
				<view class="input-line-height-1">经度 <text class="main-color line-blue">|</text></view>
				<input class="input-line-height-2" type="number"   v-model="longitude" />
			</view>

			
			<view class="input-line-height" v-show="false">
				<view class="input-line-height-1">纬度 <text class="main-color line-blue">|</text></view>
				<input class="input-line-height-2" type="number"  v-model="latitude" />
			</view>

			<view class="input-line-height">
				<view class="input-line-height-1">实景 <text class="main-color line-blue">|</text></view>
				<view class="input-line-height-2">
                    <button size="mini" style="margin-top: 7px;font-size: 14px;"  @click="takePhoto">上传照片</button>
				</view>
			</view>		
			
			<view class="navcon">
				<view v-for="(value, index) in imagelist" :key="index" class="navcon-item">
					<image style="width:95%; height:100px;"  :src="value"></image>
					<text class="iconposition icon" style="color:#E3E0D5;" @click="deleimg(index)">&#xe65e;</text>
				</view>
			</view>
			
		</view>
		
		<view class="input-line-height">
			<view class="input-line-height-1">描述 <text class="main-color line-blue">|</text></view>
			<textarea class="input-line-height-2" style="padding: 20upx 0;" auto-height v-model="shop_describe" />
		</view>

		<view>
			<button class="login-button" @click="submitShopInfo">上 传</button>
		</view>

        <navigator url="./signshop" class="input-line-height" style="border-bottom: none;">
			<view class="input-line-height-1 text-center"><text class="main-color">《店铺合作协议》</text></view>
		</navigator>		
		
	</view>
</template>

<script>
	import {mapState, mapMutations} from 'vuex';

	export default {
		components: {},
		data() {
			return {
				phone: '', // 店家电话号码
				shop_name: '', // 店名
				cate: '', // 店铺分类
				environment: '', // 店铺环境
				shop_area: '', // 店铺面积
				address: '', // 店铺位置
				longitude: '', // 经度
				latitude: '', // 纬度
				image: [], // 实景图片
				imagelist: [],
				shop_describe: '', // 店铺描述
				
				shopCateList: [{cate_id: '', cate_name: ''}], // 店铺类别列表
				shopCateIndex: 0,
				environmentlist: [{en_id: '', en_name: ''}], // 店铺环境列表
				environmentindex: 0
			}
		},
		computed: mapState(['forcedLogin','hasLogin','userInfo','commonheader']),
		onLoad(){
			this.getShopCateList();
			this.getShopEnvironment();
		},
		onNavigationBarButtonTap(e) {
			this.$common.actionSheetTap();
		},
		methods: {
			/**
			 * 获取店铺环境列表
			 */
			getShopEnvironment(){
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/shop_environment',
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'GET',
					success: function(res) {
						if (res.data.status == 1) {
							res.data.data.forEach((value,index)=>{
								self.$set(self.environmentlist,index,{en_id:value.en_id,en_name:value.en_name});
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
			 * 获取店铺类别列表
			 */
			getShopCateList() {
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
							res.data.data.forEach((value,index)=>{
								self.$set(self.shopCateList,index,{cate_id:value.cate_id,cate_name:value.cate_name});
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
			 * 上传店铺信息
			 */			
			submitShopInfo(){
				// 验证表单
				if (this.phone == '') {
					uni.showToast({
						icon: 'none',
						title: '请输入手机号码'
					});
					return false;
				}
				if (!this.phone.match(/^(13[0-9]|14[0-9]|15[0-9]|16[0-9]|17[0-9]|18[0-9]|19[0-9])\d{8}$/)){
					uni.showToast({
						icon: 'none',
						title: '手机号不正确'
					});
					return false;
				}
				this.cate = this.shopCateList[this.shopCateIndex].cate_id;
				this.environment = this.environmentlist[this.environmentindex].en_id;
				// 请求接口
				uni.request({
					url: this.$serverUrl + 'api/shop',
					data: {
						user_id: this.userInfo.user_id, // 业务员所属用户ID
						phone: this.phone,
						shop_name: this.shop_name,
						cate: this.cate , // 选中的店铺类别ID
						shop_area: this.shop_area,
						address: this.address,
						longitude: this.longitude,
						latitude: this.latitude,
						image: this.image,
						environment: this.environment,
						shop_describe: this.shop_describe
					},
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'POST',
					success: function(res) {
						if (res.statusCode == 201 && res.data.status == 1) {
							uni.showModal({
								title: '提示',
								content:res.data.message,
								showCancel: false,
								success:function(res) {
									if (res.confirm) {
										uni.switchTab({
											url: '/pages/main/main'
										});
									}
								}
							});
						} else {
							uni.showToast({
								icon: 'none',
								title: res.data.message
							});
						}
					},
					fail:function(error){
						uni.showToast({
							icon: 'none',
							title: '请求异常'
						});
					}
				})
			},
			
            /**
			 * 上传照片
			 */
			takePhoto(){
				let self=this;
				if(this.image.length>=3){
					uni.showToast({
						icon:'none',
					    title: '最多上传3张',
					    duration: 2000
					});
					return false;
				}
				uni.chooseImage({
				    count: 3, //图片张数
				    sizeType: ['compressed'], //可以指定是原图还是压缩图，默认二者都有
				    sourceType: ['album','camera'], //从相册选择
				    success: function (res) {
						self.upload_image(res.tempFilePaths)											
						//图片预览
						res.tempFilePaths.forEach((value,index)=>{
							self.imagelist.push(value)
						})
				    }
				});
			},	
			/**
			 * 上传图片到oss
			 */
			upload_image(info){
				let self=this;
				info.forEach((value,index)=>{

					uni.uploadFile({
						url: this.$serverUrl + 'api/upload', 
						filePath: value,
						header: {
							'commonheader': this.commonheader,
							'access-user-token':this.userInfo.token
						},
						name: 'file',
						success: (uploadFileRes) => {
							self.image.push({name:JSON.parse(uploadFileRes.data).name,url:JSON.parse(uploadFileRes.data).url});
						}
					});
					
				})
			},
			
			/**
			 * 删除图片
			 */			
			deleimg(index){
                let self=this;
				uni.request({
					url: this.$serverUrl + 'api/deleimg',
					data: {
						name:this.image[index].name
					},
					header: {
						'commonheader': this.commonheader,
						'access-user-token':this.userInfo.token
					},
					method: 'PUT',
					success: function(res) {
                      self.image.splice(index,1);
					  self.imagelist.splice(index,1);
					}
				})				
			},
			
			/**
			 * 获取位置信息
			 */
			getlocation(){
				let self=this;
				uni.chooseLocation({
					success: function (res) {
						self.address=res.address;
						self.latitude=res.latitude;
						self.longitude=res.longitude;
					}
				});
			},

			/**
			 * 改变选择店铺类别
			 * @param {Object} e
			 */
			bindShopCatePickerChange: function(e) {
				// console.log('picker发送选择改变，携带值为', e.target.value)
				this.shopCateIndex = e.target.value;
			},

			/**
			 * 改变选择店铺的环境
			 * @param {Object} e
			 */
			bindShopHjPickerChange: function(e) {
				// console.log('picker发送选择改变，携带值为', e.target.value)
				this.environmentindex = e.target.value;
			}
		}
	}
</script>

<style>
	.contain-logo {
		margin-top: 50px;
		margin-bottom: 20px;
		text-align: center;
	}
	.logo {
		height: 140px;
	}
	.input-line-height{
		display: flex;
		align-items:center;
		line-height: 50px;
		border-bottom:1px solid #ECECEC; 
		font-size:15px;
	}
	.input-line-height-1{
		flex:1;
		line-height: 50px;
		font-size:15px;
		padding-left: 5px;
	}
	.input-line-height-2{
		flex:3;
		font-size: 15px;
		text-align: left;
	}
	.login-button{
		background-color: #409EFF;
		color:#fff;
		margin-top: 20px;
	}
	.bottom-con{
		display: flex;
		margin-top: 20px;
		flex-direction: row;
		justify-content:center;
		font-size: 14px;
	}
	.bottom-con-1{
		padding: 0 8px;
	}
	.location-text{
		padding: 5px 0;
		line-height: 40px;
		flex-wrap:wrap;
	}
	.navcon{
		display: flex;
		flex-flow: row wrap;
		justify-content: left;
		text-align: center;
	}
	.navcon-item{
		flex: 0 0 33.3%;	
		padding: 10px 0;
		position: relative;
	}
    .iconposition{
		position: absolute;
		right: -5px;
		top:-5px;
	}
	.line-blue{
		font-size: 18px;
		margin-left: 5px;
	}
</style>
