<template>
	<view class="content">
        <u-row>
            <u-col span="24" class="contain-logo">
				<image class="logo" mode="aspectFit" :src="logourl"></image>
			</u-col>
        </u-row>
		<view class="content-view">
			<u-row>

				<input v-show='false' name="please" v-model="please" placeholder="邀请码" />
				
				<u-col span="24" class="input-list">
					    <text class="iconposition icon color-blue">&#xe7d5;</text>
						<input name="phone" v-model="phone" placeholder="请输手机号" />
				</u-col>
				<u-col span="24" class="input-list">
					    <text class="iconposition icon color-blue">&#xe7b8;</text>
						<input name="password" v-model="password" placeholder="请输入密码" />
				</u-col>
				<u-col span="24" class="input-list">
					    <text class="iconposition icon color-blue">&#xe7d6;</text>
						<input name="verifycode" v-model="verifycode" placeholder="手机验证码" />
						<button type="primary"  class="verify-button">获取验证码</button>
				</u-col>
				<u-col span="24" class="book">
						<checkbox-group>
							<label>
								    <checkbox class="checkbox inline" value="cb" color="#3F45F2" checked="true" />
									<navigator class="inline text" url="navigate/navigate?title=navigate">
										阅读并同意<text class="color-blue">《商市通用户协议》</text>
									</navigator>												
							</label>
						</checkbox-group>
				</u-col>

				<u-col span="24">
                     <button type="primary" class="submit">注 册</button>
				</u-col>					
	
			</u-row>			
		</view>

	</view>
</template>

<script>
	import Vue from 'vue'
	import Row from 'components/dl-grid/row.vue'
	import Col from 'components/dl-grid/col.vue'
	
	Vue.component('u-row', Row); //<row>和<col>为H5原生标签, 不能直接用, 可起名<u-row>或者其他的
	Vue.component('u-col', Col);
	
	export default {
		components: {
		
		},
		data() {
			return {
				please:'',//邀请码
				phone:'',//手机号
				password:'',//密码
				verifycode:'',//验证码
                logourl:'/static/img/logo.png'	
			}
		},
		methods: {
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
</style>
