<template>
	<view class="uni-padding-wrap">

		<view>
			<view class="input-line-height">
				<text class="input-line-height-1">店名</text>
				<input class="input-line-height-2" type="text" :focus="true" v-model="shop_name" />
			</view>
			<view class="input-line-height" >
				<text class="input-line-height-1">类型</text>
				<picker @change="bindShopCatePickerChange" class="input-line-height-2" :value="shopCateList[shopCateIndex].cate_id" :range="shopCateList" range-key="cate_name">
					<view style="font-size: 16px;">{{shopCateList[shopCateIndex].cate_name}}</view>
					<input v-show="false" type="text" v-model="cate" />
				</picker>
			</view>
			<view class="input-line-height">
				<text class="input-line-height-1">面积：</text>
				<input class="input-line-height-2" type="number"  v-model="shop_area" />
			</view>
			<view class="input-line-height">
				<text class="input-line-height-1">位置：</text>
				<view class="input-line-height-2">
					<button v-show="!address" @click="getlocation()" style="font-size: 16px;">点击获取</button>
					<input v-show="address" type="text" v-model="address" />
				</view>
			</view>
	
			<view class="input-line-height">
				<text class="input-line-height-1">经度：</text>
				<input class="input-line-height-2" type="number"   v-model="longitude" />
			</view>
			
			<view class="input-line-height">
				<text class="input-line-height-1">纬度：</text>
				<input class="input-line-height-2" type="number"  v-model="latitude" />
			</view>
	
			<view class="input-line-height">
				<text class="input-line-height-1">实景：</text>
				<view class="input-line-height-2">
                    <button  @click="takePhoto">上传照片</button>
				</view>
			</view>		
			
			<view class="navcon">
				<view v-for="(value, index) in imagelist" :key="index" class="navcon-item">
					<image style="width:95%; height:100px;"  :src="value"></image>
					<text class="iconposition icon color-gray" @click="deleimg(index)">&#xe65e;</text>
				</view>
			</view>
			
		</view>

		<view>
			<button class="login-button" @click="submitShopinfo">上 传</button>
		</view>
		
	</view>
</template>

<script>
	import common from '@/common/common.js';
	import {mapState, mapMutations} from 'vuex';

	export default {
		components: {},
		data() {
			return {
				shop_name:'',//店名
				cate:'',//店铺分类
				shop_area:'',//店铺面积
				address:'',//店铺位置
				latitude:'',//纬度
				longitude:'',//经度
				image:[],//实景图片
				imagelist:[],
				
				shopCateList: [], // 店铺类别列表
				shopCateIndex: 0
			}
		},
		computed: mapState(['forcedLogin','hasLogin','userInfo','commonheader']),
		onLoad(){
			this.getShopCateList();
		},
		methods: {
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
							self.shopCateList = res.data.data;
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
			submitShopinfo(){
				uni.request({
					url: this.$serverUrl + 'api/shop',
					data: {
						user_id: this.userInfo.user_id,
						shop_name: this.shop_name,
						cate: this.cate,
						address: this.address,
						longitude: this.longitude,
						latitude: this.latitude,
						image: this.image,
					},
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'POST',
					success: function(res) {
						console.log(123, res)
						if (res.statusCode == 201 && res.data.status == 1) {
							uni.showModal({
								title: res.data.message,
								showCancel: false,
								success() {
									uni.switchTab({
										url: '/pages/main/main'
									});
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
				this.shopCateIndex = e.target.value
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
		font-size:16px;
	}
	.input-line-height-1{
		flex:1;
		padding-left: 5px;
	}
	.input-line-height-2{
		flex:3;
		font-size: 16px;
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
</style>
