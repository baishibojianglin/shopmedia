<template>
  <div id="app">
    <router-view/>
  </div>
</template>

<script>
	import axios from 'axios';
	
	export default {
		beforeMount() { // 在实例挂载开始之前被调用
			// this.checkLogin();
		},
		beforeUpdate() { // 数据更新时调用
			this.commonHeaders();
			// this.checkLogin();
		},
		methods: {
			/**
			 * 公用请求头配置
			 * 其中登录页还未生成缓存，不传入以下请求头；home页需要单独传入以下请求头，否则会出错
			 */
			commonHeaders() {
				if (localStorage.getItem('admin_user')) {
					axios.defaults.headers.common['admin-user-id'] = JSON.parse(localStorage.getItem('admin_user')).id;
					axios.defaults.headers.common['admin-user-token'] = JSON.parse(localStorage.getItem('admin_user')).token;
				}
			},
			
			/**
			 * 判断登录是否失效
			 * 两种情况：1.客户端管理用户登录的缓存信息被清空；2.服务端token过期
			 */
			checkLogin() {
				// 判断是否在登录页
				if (this.$route.path != '/') {
					// 当客户端缓存被清空时
					if (!localStorage.getItem('admin_user'))  {
						this.$message({
							message: '登录失效，请重新登录',
							type: 'warning'
						});
						
						// 跳转登录页
						this.$router.replace({path:'/'});
					}
					
					// TODO：当服务端token过期时
				}
			}
		}
	}
</script>

<style lang="scss">
	
</style>
