<template>
	<view>
       <uni-card>
			<view>
				<view class="input-line-height">
					<text class="input-line-height-1">认证码 |</text>
					<input class="input-line-height-2" type="text" v-model="applycode"  placeholder="填写认证码" />
				</view>
				<view class="input-line-height">
					<text class="input-line-height-1">申请者 |</text>
					<input class="input-line-height-2" type="text"  v-model="this.userInfo.phone" disabled="true"  />
				</view>
			</view>
       </uni-card>
	   <view>
	   	  <button class="login-button" @click="applySaleperson()">提交申请</button>
	   </view>
	</view>
</template>

<script>
	import {mapState, mapMutations} from 'vuex';

	export default {
		components: {},
		data() {
			return {
                  applycode:'', //邀请码
				  role_id:'' //角色id
			}
		},
		computed: mapState(['forcedLogin','hasLogin','userInfo','commonheader']),
		onLoad(options){
			this.role_id=options.role_id;
			this.applycode=options.code;
		},
		onNavigationBarButtonTap(e) {
			this.$common.actionSheetTap();
		},
		methods: {
			/**
			 * 业务员角色类型申请
			 */
             applySaleperson(){
				 let self=this;
				 uni.request({
				 	url: this.$serverUrl + 'api/apply_salesman',
				 	data: {
				 		user_id: this.userInfo.user_id, //用户id
						invitation_code: this.applycode, //邀请码
						role_id: this.role_id //角色id
				 	},
				 	header: {
				 		'commonheader': this.commonheader,
				 		'access-user-token':this.userInfo.token
				 	},
				 	method: 'POST',
				 	success: function(res) {
				 		if(res.data.status == 1){
						   uni.showModal({
						       title: '提示',
						       content: '申请已提交，审核中...',
							   showCancel:false,
						       success: function (res) {
						           if (res.confirm) {
						               uni.navigateTo({
						               	url: './center'
						               });
						           }
						       }
						   })
				 		}else{
				 			uni.showToast({
				 				icon:'none',
				 			    title: res.data.message,
				 			    duration: 2000
				 			});
				 		}
				 	}
				 })
			 }
		}
	}
</script>

<style>
	.content {
		margin: 0;
		padding: 0;
		text-align: center;
	}
	.input-line-height{
		display: flex;
		align-items:center;
		line-height: 50px;
		border-bottom:1px solid #ECECEC; 
		font-size:16px;
	}
	.input-line-height-1{
		flex:1;
		padding-left: 5px;
		text-align: right;
	}
	.input-line-height-2{
		flex:3;
		font-size: 16px;
		text-align: left;
		padding-left: 15px;
	}
	.login-button{
		background-color: #409EFF;
		color:#fff;
		margin: 40px 10px 20px 10px;
	}	
</style>
