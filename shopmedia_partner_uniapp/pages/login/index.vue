<template>
	<view class="uni-container">
		<view class="contain-logo">
			<image class="logo" :src="logourl"></image>
		</view>
		<!-- <view class="uni-center logotext">
			<text>商市通</text>
		</view> -->

		<view class="content-view">
			<view>
				<navigator url="/pages/login/login">
					<button class="bg-main-color color-white submit">手机号登录</button>
				</navigator>
			</view>
		</view>
		<!-- 对话框 s -->
		<uni-popup id="popupDialog" ref="popupDialog" type="dialog">
			<uni-popup-dialog type="info" title="用户及隐私协议" content="请你仔细阅读《商市通用户及隐私协议》，如果你同意协议内容，请点击“确定”开始接受我们的服务。" :before-close="true" @confirm="dialogConfirm" @close="dialogClose"></uni-popup-dialog>
		</uni-popup>
		<!-- 对话框 s -->
	</view>
</template>

<script>
	import uniPopup from '@/components/uni-popup/uni-popup.vue'
	import uniPopupMessage from '@/components/uni-popup/uni-popup-message.vue'
	import uniPopupDialog from '@/components/uni-popup/uni-popup-dialog.vue'
	
	export default {
		components: {
			uniPopup,
			uniPopupMessage,
			uniPopupDialog
		},
		data() {
			return {
				logourl: '/static/img/logo.png'
			}
		},
		onReady() {
			// 页面打开自动打开对话框
			setTimeout(() => {
				this.msgType = 'success'
				this.$refs.popupDialog.open()
			}, 500)
		},
		methods: {
			/**
			 * 用户协议提示框
			 */
			open(){
				this.$refs.popup.open()
			},
			
			/**
			 * 对话框点击确认按钮
			 */
			dialogConfirm(done) {
				
				// 需要执行 done 才能关闭对话框
				done()
			},
			
			/**
			 * 对话框取消按钮
			 */
			dialogClose(done) {
				// 需要执行 done 才能关闭对话框
				done()
			}
		}
	}
</script>

<style>
	.contain-logo {
		margin-top: 20px;
		text-align: center;
	}
	.logotext{
		font-size: 30px;
		margin-bottom: 0px;
	}
	.logo {
		height: 120px;
		width: 120px;
		border-radius: 20px;
	}

	.content-view {
		width: 90%;
		margin: 50px auto;
		text-align: center;
	}

	.input-list {
		position: relative;
		border-bottom: 1px solid #F1F1F1;
		padding-bottom: 5px;
		margin-bottom: 30px;
	}

	.verify-button {
		position: absolute;
		right: 0;
		bottom: 3px;
		font-size: 13px;
		width: 100px;
	}

	.contain-checkbox {
		margin-top: 20px;
	}

	.checkbox {
		font-size: 10rpx;
	}

	.submit {
		margin-top: 35px;
	}
</style>
