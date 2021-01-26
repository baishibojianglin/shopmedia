<template>
	<view>
		<uni-card>
			<view class="uni-title uni-bold">广告主信息</view>
			<view class="uni-list">
				<label class="uni-list-cell uni-list-cell-pd">
					<input type="text" v-model="form.user_name" placeholder="广告主名称" />
				</label>
				<label class="uni-list-cell uni-list-cell-pd">
					<input type="text" v-model="form.phone" placeholder="广告主电话" />
				</label>
				<label class="uni-list-cell uni-list-cell-pd">
					<input type="text" v-model="form.advertiser_address" placeholder="广告主详细地址" />
				</label>
			</view>
		</uni-card>
		
		<uni-card>
			<view class="uni-title uni-bold">广告名称</view>
			<view class="uni-list uni-common-mb">
				<label class="uni-list-cell uni-list-cell-pd">
					<input type="text" v-model="form.ad_name" placeholder="填写广告名称" />
				</label>
			</view>
			
			<view class="uni-title uni-bold">广告套餐</view>
			<uni-segmented-control :current="segmentedControl.current" :values="segmentedControl.items" @clickItem="onClickItem" style-type="text" active-color="#4C85FC"></uni-segmented-control>
			<view v-if="segmentedControl.current === 0">
				<view class="uni-list">
					<radio-group @change="radioChange">
						<label class="uni-list-cell uni-list-cell-pd" v-for="(item, index) in adComboList" :key="item.combo_id">
							<view>
								<radio :value="item.combo_id" /><!-- :checked="index === current" -->
							</view>
							<uni-grid :column="3" :square="false" :showBorder="false">
								<uni-grid-item>
									<view class="uni-common-pl">{{item.ad_days}}天</view>
								</uni-grid-item>
								<uni-grid-item>
									<view>{{item.device_quantity}}台广告机</view>
								</uni-grid-item>
								<uni-grid-item>
									<view class="color-red uni-bold uni-common-pl">￥{{item.combo_price}}</view>
								</uni-grid-item>
							</uni-grid>
						</label>
					</radio-group>
				</view>
			</view>
			<view v-if="segmentedControl.current === 1">
				<view class="uni-list">
					<label class="uni-list-cell uni-list-cell-pd">
						<input type="text" v-model="form.ad_days" placeholder="广告投放天数" />天
					</label>
					<label class="uni-list-cell uni-list-cell-pd">
						<input type="text" v-model="form.device_quantity" placeholder="广告投放设备数量" />台
					</label>
					<label class="uni-list-cell uni-list-cell-pd">
						<input type="text" v-model="form.combo_price" placeholder="广告套餐价格" />元
					</label>
				</view>
			</view>
		</uni-card>
		
		<view v-if="false" class="uni-padding-wrap uni-center">
			<checkbox-group @change="isAgreement">
				<label>
					<checkbox class="checkbox inline" value="1" color="#4C85FC" checked="true" />
				</label>
				<navigator class="inline text" url="/pages/ad-combo/ad-combo-agreement">
					阅读并同意<text class="color-blue">《广告套餐协议》</text>
				</navigator>
			</checkbox-group>
		</view>
		
		<view class="uni-padding-wrap uni-common-mt mb">
			<button type="primary"  style="margin-bottom: 20px;" @click="createOrderSubmit()">提交</button>
		</view>
	</view>
</template>

<script>
	import {mapState} from 'vuex';
	import Handwriting from "../../static/js/signature.js";
	
	export default {
		data() {
			return {
				form: {
					user_name: '', // 广告主名称
					phone: '', // 广告主电话
					advertiser_address: '', // 广告主详细地址
					ad_name: '', // 广告名称
					combo_id: '', // 广告套餐ID
					device_quantity: '', // 设备数量（台）
					ad_days: '', // 广告天数（天）
					combo_price: '', // 广告套餐价格
					is_agreement: 1 // 勾选协议
				},
				
				// SegmentedControl 分段器
				segmentedControl: {
					items: ['选择套餐', '定制套餐'],
					current: 0
				},
				
				adComboList: [], // 广告套餐列表
				current: 0,
				
				// 电子签名
				lineColor: 'black',
				slideValue: 50,
				handwriting: '',
				selectColor: 'black',
				color: '',
				showimg: '', // 签名图片地址
				share_popup: false
				
			}
		},
		computed: {
			...mapState(['hasLogin', 'forcedLogin', 'userInfo', 'commonheader'])
		},
		onLoad() {
			this.getAdComboList();
		},
		methods: {
			/* 电子签名 s */
			/**
			 * 选择画笔颜色
			 * @param {Object} event
			 */
			selectColorEvent(event) {
				this.selectColor = event;
				if (event == 'black') {
					this.color = '#1A1A1A'
				} else if (event == 'red') {
					this.color = '#ca262a'
				}
				this.handwriting.selectColorEvent(this.color)
			},
			retDraw() {
				this.handwriting.retDraw()
				this.showimg = '';
			},
			/**
			 * 笔迹粗细滑块
			 * @param {Object} e
			 */
			updateValue(e) {
				//console.log(e.detail);
				this.slideValue = e.detail.value;
				this.handwriting.selectSlideValue(this.slideValue);
			},
			uploadScaleStart(event) {
				this.handwriting.uploadScaleStart(event)
			},
			uploadScaleMove(event) {
				this.handwriting.uploadScaleMove(event)
			},
			uploadScaleEnd(event) {
				this.handwriting.uploadScaleEnd(event)
			},
			subCanvas() {
				this.handwriting.saveCanvas().then(res => {
					this.showimg = res;
					// console.log(res);
				}).catch(err => {
					// console.log(err);
				});
			},
			/* 电子签名 e */
			
			
			
			
			
			
			
			
			/**
			 * 获取广告套餐列表
			 */
			getAdComboList() {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/ad_combo_list',
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'GET',
					success: function(res) {
						if (res.data.status == 1) {
							res.data.data.forEach((value, index) => {
								self.$set(self.adComboList, index, {
									combo_id: String(value.combo_id),
									ad_days: value.ad_days,
									device_quantity: value.device_quantity,
									combo_price: value.combo_price
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
			 * SegmentedControl 分段器组件触发点击事件时触发
			 * @param {Object} e
			 */
			onClickItem(e) {
				if (this.segmentedControl.current !== e.currentIndex) {
					this.segmentedControl.current = e.currentIndex;
					
					if (this.segmentedControl.current == 1) {
						// 初始化广告套餐数据
						this.form.ad_days = '';
						this.form.device_quantity = '';
						this.form.combo_price = '';
					}
				}
			},
			
			/**
			 * 改变选择广告套餐
			 * @param {Object} evt
			 */
			radioChange(evt) {
				this.form.combo_id = evt.detail.value;
				for (let i = 0; i < this.adComboList.length; i++) {
					if (this.adComboList[i].combo_id === evt.detail.value) {
						this.current = i;
						break;
					}
				}
				this.form.combo_price = this.adComboList[this.current].combo_price;
				this.form.device_quantity = this.adComboList[this.current].device_quantity;
				this.form.ad_days = this.adComboList[this.current].ad_days;
			},
			
			/**
			 * 改变勾选协议
			 * @param {Object} e
			 */
			isAgreement(e) {
				this.form.is_agreement = e.detail.value.length;
			},
			
			/**
			 * 创建广告屏合作商订单提交表单
			 */
			createOrderSubmit() {
				let self = this;
				
				if (this.form.user_name == '') {
					uni.showToast({
						icon: 'none',
						title: '请输入广告主名称'
					});
					return false;
				}
				// 检查手机号码
				if (this.form.phone == '') {
					uni.showToast({
						icon: 'none',
						title: '请输入手机号码'
					});
					return false;
				}
				if (!(/^1(3|4|5|6|7|8|9)\d{9}$/.test(this.form.phone))) {
					uni.showToast({
						icon: 'none',
						title: '请输入正确的手机号码'
					});
					return false;
				}
				if (this.form.advertiser_address == '') {
					uni.showToast({
						icon: 'none',
						title: '请输入广告主详细地址'
					});
					return false;
				}
				if (this.form.ad_name == '') {
					uni.showToast({
						icon: 'none',
						title: '请输入广告名称'
					});
					return false;
				}
				if (this.segmentedControl.current == 0) {
					if (this.form.combo_id == '' || this.form.combo_price == '') {
						uni.showToast({
							icon: 'none',
							title: '请选择广告套餐'
						});
						return false;
					}
				} else if (this.segmentedControl.current == 1) {
					if (this.form.ad_days == '') {
						uni.showToast({
							icon: 'none',
							title: '请输入投放广告天数'
						});
						return false;
					}
					if (this.form.device_quantity == '') {
						uni.showToast({
							icon: 'none',
							title: '请输入投放设备数量'
						});
						return false;
					}
					if (this.form.combo_price == '') {
						uni.showToast({
							icon: 'none',
							title: '请输入广告套餐价格'
						});
						return false;
					}
				}
				if (this.form.is_agreement != 1) {
					uni.showToast({
						icon: 'none',
						title: '同意协议后才能提交订单'
					});
					return false;
				}
				
				/* uni.showModal({
				    title: '确认收款，创建订单',
				    content: '确认广告主已经付款成功?',
					// showCancel: false,
				    success: function (res) {
						if (res.confirm == true) {
							self.createOrder();
						}
				    }
				}) */
				self.createOrder();
			},
			
			/**
			 * 创建广告屏合作商订单
			 */
			createOrder() {
				let self = this;
				
				uni.request({
					url: this.$serverUrl + 'api/ad_combo_order',
					data: {
						salesman_user_id: this.userInfo.user_id,
						user_name: this.form.user_name,
						phone: this.form.phone,
						advertiser_address: this.form.advertiser_address,
						ad_name: this.form.ad_name,
						combo_id: this.form.combo_id,
						device_quantity: this.form.device_quantity,
						ad_days: this.form.ad_days,
						combo_price: this.form.combo_price,
					},
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'POST',
					success: function(res) {
						if (res.data.status == 1) {
							if (res.data.data.order_id) {
								let order_id = res.data.data.order_id;
								uni.showModal({
									title: '提示',
									content: '广告套餐订单提交成功，去完成支付',
									showCancel: false,
									success: function (res) {
										if (res.confirm == true) {
											uni.navigateTo({
												url: '/pages/ad-combo/ad-combo-order-pay?order_id=' + order_id
											})
											return false;
										}
									}
								})
							}
						} else {
							uni.showToast({
								icon: 'none',
								title: '创建订单失败'
							});
							return false;
						}
					}
				})
			}
		}
	}
</script>

<style>
	.contain-logo {
		margin-top: 50px;
		text-align: center;
	}
	.logotext{
		font-size: 24px;
		margin-bottom: 20px;
	}
	.logo {
		height: 120px;
		width: 120px;
		border-radius: 20px;
	}
	.input-line-height{
		display: flex;
		align-items:center;
		line-height: 50px;
		border-bottom:1px solid #ECECEC; 
		font-size:16px;
		position: relative;
	}
	.input-line-height-1{
        position: absolute;
		left: 5px;
		padding:15px 0 10px 0;
	}
	.input-line-height-2{
		flex:1;
		font-size: 16px;
		text-align: center;
        padding: 15px 0 10px 0;
	}
	.login-button{
		color:#fff;
		margin-top: 20px;
	}
	.bottom-con{
		display: flex;
		flex-direction: row;
		justify-content:center;
		font-size: 14px;
	}
	.bottom-con-1{
		padding: 0 8px;
	}
    .bottom{
		position: fixed;
		bottom: 40px;
		left:0;
		right:0;
	}
	.title {
		line-height: 50px;
	}

	.content {
		display: flex;
		;
		flex-flow: row wrap;
		text-align: left;
		margin-bottom: 15px;
		line-height: 30px;
	}

	.content-text {
		display: flex;
		flex-flow: row wrap;
		text-align: left;
	}

	.content-left {
		flex-basis: 60px;
	}

	.content-left-text {
		flex-basis: 80px;
	}

	.content-right {
		flex: 1;
	}

	.sign-border {
		border-bottom: 1px solid #333333;
		text-align: center;
		font-size: 14px;
	}

	.sign-border-text {
		border-bottom: 1px solid #333333;
		text-align: center;
		font-size: 14px;
	}

	.text-space {
		text-indent: 20px;
	}

	.text-word {
		display: inline-block;
		width: 80px;
		border-bottom: 1px solid #000;
		text-align: center;
		font-size: 14px;
	}

	.content-con {
		margin-bottom: 60px;
	}

	.signname {
		margin-top: 10px;
	}

	.sign-con {
		display: flex;
		flex-flow: row wrap;
		text-align: center;
	}

	.sign-con-item {
		flex: 1;
	}


	.wrapper {
		margin: 30upx 0;
		overflow: hidden;
		display: flex;
		align-content: center;
		flex-direction: column;
		justify-content: center;
		font-size: 28upx;
	}

	.handWriting {
		background: #fff;
		width: 100%;
		height: 350upx;
	}

	.handRight {
		align-items: center;
	}

	.handCenter {
		border: 4upx dashed #e9e9e9;
		flex: 5;
		overflow: hidden;
		box-sizing: border-box;
		width: 90%;
		margin: 0 auto;
	}

	.handTitle {
		flex: 1;
		color: #666;
		justify-content: center;
		font-size: 30upx;
	}

	.handBtn {
		flex-direction: column;
		padding: 40upx 20upx;
	}

	.buttons {
		width: 100%;
		margin-top: 20upx;
		justify-content: space-between;
	}

	.buttons>button {
		font-size: 30upx;
		height: 80upx;
		width: 120upx;
	}

	.delBtn {
		color: #666;
	}

	.color {
		align-items: center;
	}

	.color>text {
		margin-right: 20upx;
	}

	.subBtn {
		background: #008ef6;
		color: #fff;
		text-align: center;
		justify-content: center;
	}

	.black-select {
		width: 60upx;
		height: 60upx;
	}

	.black-select.color_select {
		width: 90upx;
		height: 90upx;
	}

	.red-select {
		width: 60upx;
		height: 60upx;
	}

	.red-select.color_select {
		width: 90upx;
		height: 90upx;
	}

	.slide-wrapper {
		align-items: center;
		margin-bottom: 20upx;
	}

	.slider {
		width: 400upx;
		padding-left: 20upx;
	}

	.drop {
		width: 50upx;
		height: 50upx;
		border-radius: 50%;
		background: #FFF;
		position: absolute;
		left: 0upx;
		top: -10upx;
		box-shadow: 0px 1px 5px #888888;
	}

	.slide {
		width: 250upx;
		height: 30upx;
	}

	.showimg {
		border: 0upx solid #e9e9e9;
		overflow: hidden;
		width: 90%;
		margin: 0 auto;
		background: none;
		height: 350upx;
		margin-top: 40upx;
		align-items: center;
		justify-content: center;
	}

	.showimg>image {
		width: 100%;
		height: 100%;
	}

	.showimg>text {
		font-size: 40upx;
		color: #888;
	}
	.fix-text{
		display: inline-block;
		border-bottom:1px solid #000;
        font-weight: bold;
		padding: 0 10px;
	}
</style>
