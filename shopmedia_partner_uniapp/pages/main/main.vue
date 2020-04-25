<template>
	<view class="content">
		   <view>
                 <video class="vedio-con"  autoplay="false" loop="false" controls="true" src="https://sustock-app.oss-cn-chengdu.aliyuncs.com/sustock-logo.mp4"></video>
		   </view>
			   
           <view >
			   <uni-grid  class="view-grid-con" :column="3">
			       <uni-grid-item>
			           <text class="text-grid-title">屏总量</text>
					   <text class="text-grid">5000+</text>
			       </uni-grid-item>
			       <uni-grid-item>
			           <text class="text-grid-title">覆盖城市</text>
					   <text class="text-grid">3</text>
			       </uni-grid-item>
			       <uni-grid-item>
			           <text class="text-grid-title">服务商家</text>
					   <text class="text-grid">20000+</text>
			       </uni-grid-item>
			   </uni-grid>
			</view>
			
			<view>
				<text class="user-title"> <text class="color-blue">—</text> 我与店通 <text class="color-blue">—</text></text>
			</view>

            <view class="navcon">
						<view class="navcon-item">
							<text class="iconposition icon color-blue iconbg">&#xe636;</text>
							<br/>
							<text>投放广告</text>
						</view>
						<view @click="toRole(2)" class="navcon-item">
							<text class="iconposition icon color-red iconbg">&#xe637;</text>
							<br/>
							<text>合作广告屏</text>
						</view>
						<view class="navcon-item">
							<text class="iconposition icon iconbg" style="color:#1AA034;">&#xe61b;</text>
							<br/>
							<text>我的店铺</text>
						</view>
						<view class="navcon-item">
							<text class="iconposition icon color-blue iconbg" style="color:#205C6D;">&#xe63d;</text>
							<br/>
							<text>业务参与</text>
						</view>
						<view class="navcon-item">
							<text class="iconposition icon color-blue iconbg" style="color:#F7D810;">&#xe652;</text>
							<br/>
							<text>店通快讯</text>
						</view>
						<view class="navcon-item">
							<text class="iconposition icon color-blue iconbg" style="color:#04EAFB;">&#xe74f;</text>
							<br/>
							<text>投诉建议</text>
						</view>
			</view>

																					  
	</view>
</template>

<script>
	import common from '@/common/common.js';
	import {mapState, mapMutations} from 'vuex';
	export default {
		data() {
			return {
				role:{
					device:false, //广告屏合作者
					shop:false, //店铺
					saleperson:false //业务员
				}
			}
		},
		computed: mapState(['forcedLogin','hasLogin','userInfo','commonheader']),
		onLoad() {
			//调用-判断用户角色
            this.is_role();
		},
		methods: {
			/**
			 * 指定角色跳转
			 * @param {Object} role_ids
			 */
			toRole(role_ids){
				
				//广告屏合作商
				if( (role_ids==3)&&(this.role.device==true)){  
					uni.navigateTo({
					    url: '../user-partner/user-partner'
					});
				}else{
					uni.showModal({
					    title: '提示',
					    content: '您还不是广告屏合作商,申请加入？',
					    success: function (res) {
					        if (res.confirm) {
					          uni.navigateTo({
					              url: "../user/apply-partner"
					          });  
					        } else if (res.cancel) {
					          
					        }
					    }
					});
				}
				
				
			},
			/**
			 * 判断用户角色
			 */
			is_role(){
				let self=this;
				let role_str=this.$store.state.userInfo.role_ids;
				let role_array=role_str.split(',');
				role_array.forEach((value,index)=>{
					 switch(parseInt(value)) {
					      case 2:
					         self.role.device=true;
					         break;
					      case 3:
					         self.role.shop=true;
					         break;
					      default:
					         self.role.saleperson=true;
					 } 
				})
			}
	
		}
	}
</script>

<style>
.content{
	margin: 0;
	padding: 0;
	text-align: center;
}
.vedio-con{
	width: 100%;
}
.view-grid-con{
	margin: 0px 5px;
}
.text-grid-title{
	margin-top: 10px;
}
.text-grid{
	line-height: 80px;
	font-weight: bolder;
	font-size: 17px;
	color:#504AF2;
}
.user-title{
	line-height: 50px;
	font-size:16px;
}
.iconbg{
    height: 50px;
	width: 50px;
	border-radius: 50px;
	border:1px solid #F3F3F3;
	line-height: 50px;
	display: inline-block;
}
.navcon{
	display: flex;
	 flex-flow: row wrap;
	justify-content: left;
    text-align: center;
}
.navcon-item{
	flex: 0 0 33%;
	padding: 10px 0;
}
</style>
