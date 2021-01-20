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
	</view>
</template>

<script>
	export default {
		data() {
			return {
				logourl: '/static/img/logo.png'
			}
		},
		methods: {
			
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
