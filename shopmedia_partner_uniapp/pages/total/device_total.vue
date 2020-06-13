<template>
	<view class="content">


		<uni-card :is-shadow="true">
			<view>
				<view class="listcon">
					<view class="listcon-item-1">
						<text>屏号</text>
					</view>
					<view class="listcon-item-2">
						<text>店名</text>
					</view>
					<view class="listcon-item-3">
						<text v-show="false">详情</text>
					</view>
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

			}
		},
		computed: {
			...mapState(['hasLogin', 'forcedLogin', 'userInfo', 'commonheader'])
		},
		onLoad() {

		},
		onNavigationBarButtonTap(e) {
			this.$common.actionSheetTap();
		},
		methods: {
	
		}
	}
</script>

<style>
	.content {
		margin: 0;
		padding: 0;
		text-align: center;
	}



	.countcon {
		display: flex;
		flex-direction: row;
		justify-content: center;
		border-bottom: 1px solid #E7E6DF;
		font-weight: bold;
	}

	.countcon-item {
		flex: 1;
		line-height: 40px;
	}

	.listcon {
		display: flex;
		flex-direction: row;
		justify-content: center;
		border-bottom: 1px solid #E7E6DF;
	}

	.listcon-item-1 {
		line-height: 40px;
		flex-basis: 70px;
	}

	.listcon-item-2 {
		flex: 1;
		line-height: 40px;
	}

	.listcon-item-3 {
		line-height: 40px;
		flex-basis: 30px;
	}

	.icon-size {
		font-size: 16px;
	}
</style>
