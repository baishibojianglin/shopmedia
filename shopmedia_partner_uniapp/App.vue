<script>
	import common from '@/common/common.js';
	
	export default {
		components: {},
		// 全局变量
		globalData: {
			systemInfo: '', // 设备系统信息
			version: 1, // 应用大版本号
			did: '12345dg', // 设备号
			commonHeaders: {} // 公用请求头
		},
		onLaunch: function() {		
			let self = this;	
			// 获取设备系统信息
			uni.getSystemInfo({
				success: function (res) {
					self.globalData.systemInfo = res; // getApp().globalData.systemInfo = res
				}
			});
			
			// 公用请求头配置
			this.globalData.commonHeaders = {
				'content-type': "application/json; charset=utf-8",
				'sign': common.sign(), // 验签，TODO：对参数如did等进行AES加密，生成sign如：'6IpZZyb4DOmjTaPBGZtufjnSS4HScjAhL49NFjE6AJyVdsVtoHEoIXUsjrwu6m+o'
				'version': getApp().globalData.version, // 应用大版本号
				'model': getApp().globalData.systemInfo.model, // 手机型号
				'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
				'did': getApp().globalData.did // 设备号
			}
		},
		onShow: function() {
			console.log('App Show');
		},
		onHide: function() {
			console.log('App Hide');
		}
	}
	
	/**
	 * 判断是否登录（非vuex管理登录状态）
	 */
	/* global.isLogin = function() {
		try{
			var login_info = uni.getStorageSync('login_info');
		}catch(e){
			//TODO handle the exception
			console.log(e)
		}
		if(login_info == ''){
			return false;
		}else{
			return login_info;
		}
	} */
</script>

<style>
	/*阿里图标库*/
	@font-face {
	  font-family: 'iconfont';  /* project id 1721327 */
	  src: url('//at.alicdn.com/t/font_1721327_ri7fc55t8l.eot');
	  src: url('//at.alicdn.com/t/font_1721327_ri7fc55t8l.eot?#iefix') format('embedded-opentype'),
	  url('//at.alicdn.com/t/font_1721327_ri7fc55t8l.woff2') format('woff2'),
	  url('//at.alicdn.com/t/font_1721327_ri7fc55t8l.woff') format('woff'),
	  url('//at.alicdn.com/t/font_1721327_ri7fc55t8l.ttf') format('truetype'),
	  url('//at.alicdn.com/t/font_1721327_ri7fc55t8l.svg#iconfont') format('svg');
	}	
	.icon{
		font-family: iconfont;
		font-size: 24px;
	}		
	/*颜色*/
	.color-blue{
		color:#3F45F2;
	}
	
	/*行列转换*/
	.inline{
		display: inline-block;
	}
	
	/*对齐*/
	.text-right{
		text-align: right;
	}
	.text-left{
		text-align: left;
	}
	/*加粗*/
	.weight{
		font-weight: bold;
	}
	
	
	
	
	
	
	
	
	
	/* 头条小程序需要把 iconfont 样式放到组件外 */
	@import "components/m-icon/m-icon.css";

	/*每个页面公共css */
	/* uni.css - 通用组件、模板样式库，可以当作一套ui库应用 */
	@import './common/uni.css';
	
	page {
		min-height: 100%;
		display: flex;
		font-size: 16px;
	}

	/* #ifdef MP-BAIDU */
	page {
		width: 100%;
		height: 100%;
		display: block;
	}

	swan-template {
		width: 100%;
		min-height: 100%;
		display: flex;
	}

	/* 原生组件模式下需要注意组件外部样式 */
	custom-component {
		width: 100%;
		min-height: 100%;
		display: flex;
	}

	/* #endif */

	/* #ifdef MP-ALIPAY */
	page {
		min-height: 100vh;
	}

	/* #endif */

	/* 原生组件模式下需要注意组件外部样式 */
	m-input {
		width: 100%;
		/* min-height: 100%; */
		display: flex;
		flex: 1;
	}

	.content {
		display: flex;
		flex: 1;
		flex-direction: column;
		background-color: #fff;
		padding: 10px;
		text-align: center;
	}

	.input-group {
		background-color: #ffffff;
		margin-top: 20px;
		position: relative;
	}

	.input-group::before {
		position: absolute;
		right: 0;
		top: 0;
		left: 0;
		height: 1px;
		content: '';
		-webkit-transform: scaleY(.5);
		transform: scaleY(.5);
		background-color: #c8c7cc;
	}

	.input-group::after {
		position: absolute;
		right: 0;
		bottom: 0;
		left: 0;
		height: 1px;
		content: '';
		-webkit-transform: scaleY(.5);
		transform: scaleY(.5);
		background-color: #c8c7cc;
	}

	.input-row {
		display: flex;
		flex-direction: row;
		position: relative;
		font-size: 18px;
		line-height: 40px;
	}

	.input-row .title {
		width: 72px;
		padding-left: 15px;
	}

	.input-row.border::after {
		position: absolute;
		right: 0;
		bottom: 0;
		left: 8px;
		height: 1px;
		content: '';
		-webkit-transform: scaleY(.5);
		transform: scaleY(.5);
		background-color: #c8c7cc;
	}

	.btn-row {
		margin-top: 25px;
		padding: 10px;
	}

	button.primary {
		background-color: #504AF2; // #0faeff
	}
</style>
