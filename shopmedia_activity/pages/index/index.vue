<template>
	<view class="content">
		<view v-if="prize_no"><!--未中奖 s-->
			<view style="width: 100%; margin-top: 30px;">
			   <image style="width:100%;" :mode="mode" :src="src1"></image>	
			</view>
			<view style="margin-top:20px;">
				<text class="color-shop">{{prize_info.shop_name}}</text>
			</view>
			<view style="margin-top:10px;">
				<text>很遗憾未中奖，期待下次为您服务！</text>
			</view>			
		</view><!--未中奖 e-->

		
		<view v-if="prize_yes">
			<view style="width: 100%; margin-top:20px;">
			   <image style="width:90%;" :mode="mode" :src="src2"></image>	
			</view>	
			<view style="color:#f00;font-size: 35px; font-weight: bold;">{{prize_info.prize.prize_name}}</view>

			<view style="margin-top: 20px;">
				特别鸣谢
				<br/>
				<text style="font-weight: bold;">{{prize_info.prize.sponsor}}</text>
				<br/>
				对该奖品的独家赞助
			</view>

<!-- 			<view style="margin-top: 30px; font-size: 16px;">
				感谢<text style="font-weight: bold;">{{prize_info.shop_name}}</text>大力支持
			</view>	 -->				
					
			<view>
				<button type="primary" style="width: 95%; margin-top:30px;">领取奖品</button>
			</view>		
		</view>
		<view class="bottom" style="width: 100%;">店通智能屏&nbsp;&nbsp;&nbsp;&nbsp;智通天下市</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				mode: 'aspectFit',
				src1: '/static/xxhg.png',
				src2: '/static/gxzj.png',
				prize_no:false,
				prize_yes:false,
				prize_info:{}
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
								self.prize_info=res.data;
							}else{ //中奖
								self.prize_yes=true;
								self.prize_info=res.data;
							}
						}
				}) 			
			 }
			 
			 
		}
	}
</script>

<style>
	.content {
        position: fixed;
		top:0;
		bottom: 0;
		left: 0;
		right: 0;
		background-image: url(../../static/bgg.png);
		text-align: center;
	}
	.bottom{
		position: fixed;
		bottom: 30px;
		font-size: 14px;
	}
	.color-shop{
		font-size: 18px;
		font-weight: bold;
	}
</style>
