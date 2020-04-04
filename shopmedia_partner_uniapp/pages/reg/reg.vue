<template>

	<view class="content">
		
        <u-row>
            <u-col span="24" class="contain-logo">
				<image class="logo" mode="aspectFit" :src="logourl"></image>
			</u-col>
        </u-row>
		<view class="content-view">
			<u-row>		
				<u-col span="24" class="input-list">
					    <text class="iconposition icon color-blue">&#xe7c6;</text>
					    <input name="please" v-model="please" placeholder="请输邀请码" />
				</u-col>				
				<u-col span="24" class="input-list">
					    <text class="iconposition icon color-blue">&#xe7d5;</text>
						<input name="phone" type="number" v-model="phone" placeholder="请输手机号" />
				</u-col>
				<u-col span="24" class="input-list">
					    <text class="iconposition icon color-blue">&#xe7b8;</text>
						<input name="password" :password='true' v-model="password" placeholder="请设置密码" />
				</u-col>
				<u-col span="24" class="input-list">
					    <text class="iconposition icon color-blue">&#xe7d6;</text>
						<input name="verifycode" type="number" v-model="verifycode" placeholder="手机验证码" />
						<button type="primary" @click="getverifycode()"  class="verify-button">获取验证码</button>
				</u-col>
				<u-col span="24" class="book">
						<checkbox-group @change="seevalue">
							<label>
								    <checkbox class="checkbox inline" value="1" color="#3F45F2" checked="true" />
							</label>
							<navigator class="inline text" url="book">
										阅读并同意<text class="color-blue">《商市通用户协议》</text>
							</navigator>												
							
						</checkbox-group>
				</u-col>

				<u-col span="24">
                     <button @click="submitdata()" type="primary" class="submit">注 册</button>
				</u-col>					
	
			</u-row>	
							
            <uni-popup ref="popup" type="center">
				<text class="tip">{{tipwords}}</text>
			</uni-popup>					
					
		</view>

	</view>
	
</template>

<script>
	import Vue from 'vue'
	import Row from '@/components/dl-grid/row.vue'
	import Col from '@/components/dl-grid/col.vue'
	import uniPopup from "@/components/uni-popup/uni-popup.vue"	
	Vue.component('u-row', Row); //<row>和<col>为H5原生标签, 不能直接用, 可起名<u-row>或者其他的
	Vue.component('u-col', Col);
	export default {
		components: {
             uniPopup
		},
		data() {
			return {
				please:'',//邀请码
				phone:'',//手机号
				password:'',//密码
				verifycode:'',//验证码
				value:1,//勾选协议状态
                logourl:'/static/img/logo.png',
				tipwords:''
			}
		},
		mounted(){

		},
		methods: {
			/**
			 * 监测是否勾选用户协议
			 * @param {Object} e
			 */
			seevalue(e){
				this.value=e.detail.value.length;
			},
			getverifycode(){
					//检查电话
					if(this.phone==''){
						this.tipwords='请填写电话号码';
						this.$refs.popup.open();
						return false;
					}
					if(!(/^1(3|4|5|6|7|8|9)\d{9}$/.test(this.phone))){ 
						this.tipwords='请填写正确的电话号码';
						this.$refs.popup.open();
						return false; 
					}
					uni.request({
						url: this.$serverUrl +'sendmsg',
						data: {
						   phone:this.phone
						},
						method: 'POST',
						success:function(res){

						}
					})				
			},
			/**
			 * 提交数据
			 */
			submitdata(){	
				//检查邀请码
				if(this.please==''){
					this.tipwords='请填写邀请码';
					this.$refs.popup.open();
					return false;
				}
				//检查电话
				if(this.phone==''){
					this.tipwords='请填写电话号码';
					this.$refs.popup.open();
					return false;
				}
			    if(!(/^1(3|4|5|6|7|8|9)\d{9}$/.test(this.phone))){ 
					this.tipwords='请填写正确的电话号码';
					this.$refs.popup.open();
				    return false; 
				} 
				//检查密码
				if(this.password==''){
					this.tipwords='请设置密码';
					this.$refs.popup.open();
					return false; 
				}
				//验证码
				if(this.verifycode==''){
					this.tipwords='请输入验证码';
					this.$refs.popup.open();
					return false; 
				}
				//勾选协议
				if(this.value==0){
					this.tipwords='请阅读并同意用户协议';
					this.$refs.popup.open();
					return false; 
				}
				//提交后台
				uni.request({
					url: this.$serverUrl +'api/register',
					data: {
					   please:this.please,
					   phone:this.phone,
					   password:this.password,
					   verifycode:this.verifycode
					},
					header: getApp().globalData.commonHeaders,
					method: 'POST',
					success:function(res){
						console.log(res)
					},
					fail:function(error){
						console.log(error)
						// console.log('fail' + JSON.stringify(error));
					}
				})
			}
	    }
	}
</script>

<style>
	.contain-logo{
		margin-top: 30px;
		text-align: center;
	}
	 .logo{
		height: 130px;
	 }
	 .logo-text{
		 font-size: 20px;
		 font-weight: bold;
	 }
	 .content-view{
		 width: 90%;
		 margin: 50px auto;
	 }
	 .iconposition{
		 position: absolute;
		 left:20px;
		 bottom: 4px;
	 }
	 .input-list{
         position: relative;
		 border-bottom: 1px solid #F1F1F1;
		 padding-bottom: 5px;
		 margin-bottom: 30px;
	 }
	 .verify-button{
		 position: absolute;
		 right: 0;
		 bottom: 3px;
		 font-size: 13px;
		 background-color: #3F45F2;
	 }
	 .contain-checkbox{
		 margin-top: 20px;
	 }
	 .checkbox{
		 font-size: 10rpx;
	 }
	 .text{
		position: relative;
		top:3px;
	 }
	 .book{
		 text-align: center;
		 margin-top: 15px;
	 }
	 .submit{
		 background-color: #3F45F2;
		 margin-top: 35px;
	 }
	 .tip{
		 background: rgba(255,255,255,0.8);
		 padding: 8px 30px;
		 border-radius: 5px;
	 }
</style>
