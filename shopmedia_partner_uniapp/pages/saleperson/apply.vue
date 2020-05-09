<template>
	<view>
       <uni-card>
			<view>
				<view class="input-line-height">
					<text class="input-line-height-1">认证码</text>
					<input class="input-line-height-2" type="text" v-model="applycode"  placeholder="填写认证码" />
				</view>
				<view class="input-line-height">
					<text class="input-line-height-1">申请者</text>
					<input class="input-line-height-2" type="text"  v-model="this.userInfo.phone" disabled="true"  />
				</view>
			</view>
       </uni-card>
	   <view>
	   	<button class="login-button" @click="applyPartner()">提交申请</button>
	   </view>
	</view>
</template>

<script>
	import common from '@/common/common.js';
	import {mapState, mapMutations} from 'vuex';

	export default {
		components: {},
		data() {
			return {

			}
		},
		computed: mapState(['forcedLogin','hasLogin','userInfo','commonheader']),
		onLoad(){
			
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
