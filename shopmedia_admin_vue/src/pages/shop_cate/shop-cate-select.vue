<template>
	<el-select v-model="cate_id" placeholder="请选择…" clearable filterable>
		<el-option
			v-for="item in shopCateList"
			:key="item.cate_id"
			:label="item.cate_name"
			:value="item.cate_id">
		</el-option>
	</el-select>
</template>

<script>
	export default {
		props: ['value'],
		data() {
			return {
				cate_id: this.value, // 绑定值
				shopCateList: [] // 店铺类别列表
			}
		},
		mounted() {
			this.getShopCateList(); // 获取店铺类别列表
		},
		methods: {
			/**
			 * 获取店铺类别列表
			 */
			getShopCateList() {
				let self = this;
				this.$axios.get(this.$url + 'shop_cate_list')
				.then(function(res) {
					if (res.data.status == 1) {
						// 店铺类别列表
						self.shopCateList = res.data.data;
					} else {
						self.$message({
							message: '网络忙，请重试',
							type: 'warning'
						});
					}
				})
				.catch(function (error) {
					self.$message({
						message: error.response.data.message,
						type: 'warning'
					});
				});
			}
		}
	}
</script>

<style>
</style>
