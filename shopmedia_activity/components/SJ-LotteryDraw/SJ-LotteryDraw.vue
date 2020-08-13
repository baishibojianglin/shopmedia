<template>
	<view class="lottery_container">
		<view class="grid_wrap">
			<view class="lottery_wrap">
				<ul class="lottery_grid">
					<li v-for="(item, index) in grid_info_arr" :class="{ active: current_index == index && index != 8 }" :key="index"
					 @click="luck_draw" :data-index="index">
						<image v-if="index != 8" class="grid_img" mode='aspectFit' :src="item.logo" alt="">
							{{ index == 8 ? '抽奖' : '' }}
					</li>
				</ul>
			</view>
			<view class="lottery_wrap_border">
				<ul v-for="(item, index) in 4" :key="index">
					<li v-for="(item, index) in 12" :key="index"></li>
				</ul>
			</view>
		</view>
		<!-- 抽奖完成弹出的窗口 -->
		<!-- <view class="lottery_finish_wrap">
			
		</view> -->
	</view>

</template>

<script>
	import LotteryDraw from './SJ-LotteryDraw.js';
	let grid_info = [
		{
			logo: "../../static/SJ-LotteryDraw/8.png",
			text: "40积分"
		},
		{
			logo: "../../static/SJ-LotteryDraw/6.png",
			text: "免费广告"
		},
		{
			logo: "../../static/SJ-LotteryDraw/4.png",
			text: "20积分"
		},
		{
			logo: "../../static/SJ-LotteryDraw/3.jpg",
			text: "抽纸一提"
		},
		{
			logo: "../../static/SJ-LotteryDraw/5.png",
			text: "30积分"
		},
		{
			logo: "../../static/SJ-LotteryDraw/2.png",
			text: "运动鞋"
		},
		{
			logo: "../../static/SJ-LotteryDraw/7.png",
			text: "10积分"
		},{
			logo: "../../static/SJ-LotteryDraw/1.png",
			text: "大米两斤"
		},
		{
			logo: "../../static/SJ-LotteryDraw/SJ-LotteryDraw.png",
			text: "谢谢参与"
		},
	];
	export default {
		data() {
			return {
				current_index: -1,
			};
		},
		props: {
			grid_info_arr: {
				type: Array,
				default: function() {
					return grid_info
				}
			},
		},
		onLoad(){
		},

		methods: {
			
			luck_draw(event) {
				let index = event.currentTarget.dataset.index;
				let that = this;
				if (index == 8) {
					// 点击抽奖之后知道获奖位置，修改父组件中lottery_draw_param的值
					this.$emit('get_winingIndex', function(res){
						// console.log(res);
						let lottery_draw_param=res;
						let win = new LotteryDraw({
								domData: that.grid_info_arr,
								...lottery_draw_param
							},
							function(index, count) {
								that.current_index = index;
								// console.log()
								if (lottery_draw_param.winingIndex == index && lottery_draw_param.totalCount == count) {
									that.$emit('luck_draw_finish', that.grid_info_arr[index])
									// console.log('抽到了')
								}
							}
						);
					});
					
				}
			}
		}
	};
</script>

<style scoped lang="css">
	@import './SJ-LotteryDraw.css';
</style>
