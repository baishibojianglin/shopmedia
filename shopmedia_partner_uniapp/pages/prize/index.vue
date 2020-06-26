<template>
	<view class="content">
		
			<view v-show="showseconds">
					<view style="width: 100%; margin-top: 50px;">
					   <image style="width:80%;" :mode="mode" :src="src0"></image>	
					</view>	
					<view style="margin-top: 30px;">
						<text class="opentime">{{seconds}}</text>
					</view>
			</view>	
				
			<view v-show="!showseconds">
				<view v-if="prize_no"><!--未中奖 s-->
					<view style="width: 100%; margin-top:60px;">
					   <image style="width:90%;" :mode="mode" :src="src1"></image>	
					</view>
					<view style="margin-top:20px;">
						<text class="color-shop" style="color:#007AFF; background-color: #fff; padding: 10px 40px; border-radius: 10px;">{{prize_info.shop_name}}</text>
					</view>
					<view style="margin-top:30px;">
						<text>很遗憾未中奖，期待下次为您服务！</text>
					</view>			
				</view><!--未中奖 e-->

				
				<view v-if="prize_yes">
					<view style="width: 100%; margin-top:60px;">
					   <image style="width:80%;" :mode="mode" :src="src2"></image>	
					</view>	
					<view style="color:#f00;font-size: 35px; font-weight: bold;">{{prize_info.prize.prize_name}}</view>

					<view style="margin-top:0px;">
						特别鸣谢
						<br/>
						<text style="font-weight: bold;color:#007AFF; font-size: 22px; line-height: 50px;">{{prize_info.prize.sponsor}}</text>
						<br/>
						对该奖品的独家赞助
					</view>			
							
					<view>
						<button type="primary" style="width: 95%; margin-top:30px;">领取奖品</button>
					</view>		
				</view>
			</view>	
		
		
		    <view class="bottom">店通智能屏&nbsp;&nbsp;&nbsp;&nbsp;智通天下市</view>
		
	</view>
</template>

<script>
	export default {
		data() {
			return {
				mode: 'widthFix',
				src0: '/static/img/openprize.png',
				src1: '/static/img/xxhg.png',
				src2: '/static/img/gxzj.png',
				prize_no:false,
				prize_yes:false,
				prize_info:{},
				
				seconds:5 ,//倒计时秒数
				showseconds:true ,//显示倒计时
				timesign:'' //定时器标志
			}
		},
		onLoad(option){
            //console.log(option.id)
			//倒计时
			let self=this;
			this.timesign=setInterval(function(){ self.countseconds(); },1000);
			//获取奖品
			this.prize(1);
			
		},
		methods:{
			/**
			 * 倒计时函数
			 */
			countseconds() {
			       if(this.seconds>0){
					   this.seconds--;			   
				   }else{
					   this.showseconds=false;
					   clearInterval(this.timesign);
					   this.seconds=5;
					   
				   }
			},
			/**
			 * 获取奖品
			 * @param {Object} shop_id
			 */
            prize(shop_id){	 
				let self=this;
				uni.request({
						url: this.$serverUrl + 'api/get_prize',
						data: {
							shop_id:shop_id,
						},
						method: 'POST',
						success: function(res) {
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
		background-image: url(../../static/img/bgg.png);
		text-align: center;
	}
    .bottom{
		position: fixed;
		bottom: 60px;
		text-align: center;
		width: 100%;
	}
	.color-shop{
		font-size: 22px;
		font-weight: bold;
	}
	.opentime{
		display: inline-block;
		height: 150px;
		width: 150px;
		line-height: 150px;
		border-radius: 150px;
		background-color: #E92F30;
		color:#fff;
		font-weight: bold;
		font-size: 50px;
	}
</style>
