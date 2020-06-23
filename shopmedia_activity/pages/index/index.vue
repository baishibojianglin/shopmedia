<template>
	<view class="content">
		<view class="text-area">
			<text class="title">谢谢惠顾，下次再来</text>
		</view>
		<view class="bottom">店通智能屏&nbsp;&nbsp;&nbsp;&nbsp;智通天下市</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
			}
		},
		onLoad(){

		},
		methods:{

		}
	}
</script>

<style>
	.content {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
	}
	.bottom{
		position: fixed;
		bottom: 30px;
		font-size: 14px;
	}
</style>
