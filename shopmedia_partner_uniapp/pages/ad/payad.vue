<template>
	<view class="uni-container">
		<view class="uni-padding-wrap"> 
			<uni-card :is-shadow="true" class="uni-bold">
				<view class="uni-flex uni-row">
					<view class="text-left" style="width: 360rpx;">广告投放价格</view>
					<view class="uni-common-pl text-right color-red" style="-webkit-flex: 1;flex: 1;">￥{{ad.ad_price}}</view>
				</view>
			</uni-card>
			
			<view class="uni-common-mt mb">
				<button type="warn" @click="wxPay()">确认付款</button>
			</view>
		</view>
	</view>
</template>

<script>
	import {mapState} from 'vuex';
	
	export default {
		data() {
			return {
				ad_id: '', // 广告ID
				ad: {} // 广告数据
			}
		},
		computed: {
			...mapState(['hasLogin', 'forcedLogin', 'userInfo', 'commonheader'])
		},
		onLoad(option) {
			// 获取新增广告ID
			if (option.ad_id != undefined && option.ad_id) {
				this.ad_id = option.ad_id;
			}
			
			this.getAd();
		},
		methods: {
			/**
			 * 获取广告数据
			 */
			getAd() {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/ad/' + this.ad_id,
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'GET',
					success: function(res) {
						if (res.data.status == 1) {
							self.ad = res.data.data;
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
			 * 广告投放订单微信支付
			 */
			wxPay(){
				let self = this;
				
				uni.request({
					url: this.$serverUrl + 'api/adWxPay',
					data: {
						ad_id: this.ad_id,
						ad_price: this.ad.ad_price
					},
					header:{
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'POST',
					success: function(res) {
						// console.log(123, res);
						
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
						} else {
							uni.showModal({
							    title: '提示',
							    content: res.data.message,
							})
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
						/* uni.showModal({
							title: '提示 success',
							content: res.err_msg
						}) */
						uni.switchTab({
							url: '/pages/user/user'
						})
					}
				}); 
			}
		}
	}
</script>

<style>

</style>
