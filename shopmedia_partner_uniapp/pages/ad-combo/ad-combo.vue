<template>
	<view>
		<uni-card>
			<view class="uni-title uni-bold">填写广告主信息</view>
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
			
			<view class="uni-title uni-bold">选择广告套餐</view>
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
		</uni-card>
		
		<uni-card>
			<view class="uni-title uni-bold">付款码</view>
			<view style="width: 100%; text-align: center;">
				<image src="/static/img/pay_QRCode.png" :lazy-load="true" style="width: 300rpx;height: 300rpx;" @tap="previewPayQRCode('/static/img/pay_QRCode.png')"></image>
				<br/>
				<text class="uni-text-small">（点击放大）</text>
			</view>
		</uni-card>
		
		<view class="uni-padding-wrap uni-center">
			<checkbox-group @change="isAgreement">
				<label>
					<checkbox class="checkbox inline" value="1" color="#409EFF" checked="true" />
				</label>
				<navigator class="inline text" url="/pages/ad-combo/ad-combo-agreement">
					阅读并同意<text class="color-blue">《广告套餐协议》</text>
				</navigator>
			</checkbox-group>
		</view>
		
		<view class="uni-padding-wrap uni-common-mt mb">
			<button type="primary" @click="createOrderSubmit()">提交</button>
		</view>
	</view>
</template>

<script>
	import {mapState} from 'vuex';
	
	export default {
		data() {
			return {
				form: {
					user_name: '', // 广告主名称
					phone: '', // 广告主电话
					advertiser_address: '', // 广告主详细地址
					ad_name: '', // 广告名称
					combo_id: '', // 广告套餐ID
					combo_price: '', // 广告套餐价格
					is_agreement: 1 // 勾选协议
				},
				
				adComboList: [], // 广告套餐列表
				current: 0
			}
		},
		computed: {
			...mapState(['hasLogin', 'forcedLogin', 'userInfo', 'commonheader'])
		},
		onLoad() {
			this.getAdComboList();
		},
		methods: {
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
			},
			
			/**
			 * 预览付款二维码
			 * @param {Object} image
			 */
			previewPayQRCode(image) {
				var imgArr = [];
				imgArr.push(image);
				// 预览图片
				uni.previewImage({
					urls: imgArr,
					current: imgArr[0]
				});
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
				if (this.form.combo_id == '' || this.form.combo_price == '') {
					uni.showToast({
						icon: 'none',
						title: '请选择广告套餐'
					});
					return false;
				}
				if (this.form.is_agreement != 1) {
					uni.showToast({
						icon: 'none',
						title: '同意协议后才能提交订单'
					});
					return false;
				}
				
				uni.showModal({
				    title: '确认收款，创建订单',
				    content: '确认广告主已经付款成功?',
					// showCancel: false,
				    success: function (res) {
						if (res.confirm == true) {
							self.createOrder();
						}
				    }
				})
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
						combo_id: this.form.combo_id,
						ad_name: this.form.ad_name,
						combo_price: this.form.combo_price,
					},
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'POST',
					success: function(res) {
						if (res.data.status == 1) {
							uni.showModal({
							    // title: '确认收款',
							    content: res.data.message,
								showCancel: false,
							    success: function (res) {
									if (res.confirm == true) {
										// 跳转首页
										uni.switchTab({
											url: '/pages/main/main'
										})
									}
							    }
							})
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

</style>
