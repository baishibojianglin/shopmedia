<template>
	<view class="content">
		<view class="text-area" v-if="prize_no">
			<text class="title">谢谢惠顾，下次再来</text>
		</view>
		
		<view v-if="prize_yes">
			
		</view>
		<view class="bottom">店通智能屏&nbsp;&nbsp;&nbsp;&nbsp;智通天下市</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				prize_no:false,
				prize_yes:false
			}
		},
		onLoad(option){
            //console.log(option.id)
			this.prize(1);
		},
		methods:{
			
			//调取奖品数据接口
            prize(shop_id){	 
				let self=this;
				uni.request({
						url: this.$serverUrl + 'api/get_prize',
						data: {
							shop_id:shop_id,
						},
						method: 'POST',
						success: function(res) {
							console.log(res)
							if(res.data.status==0){ //未中奖
								self.prize_no=true;
							}
						}
				}) 			
			 }
			 
			 
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
