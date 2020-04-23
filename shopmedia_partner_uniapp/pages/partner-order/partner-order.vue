<template>
	<view class="uni-padding-wrap">
		<view class="uni-page-head">
			广告屏合作协议
		</view>
		<view class="uni-page-body mb">
			广告屏合作协议内容……
		</view>
		
		<view class="goods-carts">
			<uni-goods-nav :options="options" :button-group="buttonGroup" :fill="true" @click="onClick" @buttonClick="buttonClick" />
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				/* GoodsNav 商品导航 s */
				options: [{
					icon: 'headphones',
					text: '联系客服'
				}],
				buttonGroup: [{
					text: '确认合作',
					backgroundColor: '#504AF2',
					color: '#fff'
				}]
				/* GoodsNav 商品导航 e */
			}
		},
		methods: {
			/* GoodsNav 商品导航 s */
			/**
			 * GoodsNav 左侧点击事件
			 * @param {Object} e
			 */
			onClick (e) {
				// 联系客服
				if (e.index == 0) {
					this.callPhone('114');
				}
			},
			/**
			 * GoodsNav 右侧按钮组点击事件
			 * @param {Object} e
			 */
			buttonClick (e) {
				// 确认合作
				if (e.index == 0) {
					uni.showToast({
						title: `点击${e.content.text}`,
						icon: 'none'
					})
				}
			},
			/* GoodsNav 商品导航 e */
			
			/**
			 * 拨打电话
			 * @param {Object} phone
			 */
			callPhone(phone){
				uni.makePhoneCall({
					phoneNumber: phone 
				});
			}
		}
	}
</script>

<style>
	
</style>
