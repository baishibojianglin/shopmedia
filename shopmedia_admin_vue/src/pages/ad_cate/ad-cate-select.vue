<template>
	<el-select v-model="value" placeholder="请选择…" filterable>
		<el-option
			v-for="item in adCateList"
			:key="item.cate_id"
			:label="item.cate_name"
			:value="item.cate_id">
		</el-option>
	</el-select>
</template>

<script>
	export default {
		data() {
			return {
				adCateList: [] // 广告类别列表
			}
		},
		props: ['value'],
		mounted() {
			this.getAdCateList(); // 获取广告类别列表
		},
		methods: {
			/**
			 * 获取广告类别列表
			 */
			getAdCateList() {
				let self = this;
				this.$axios.get(this.$url + 'ad_cate_list')
				.then(function(res) {
					if (res.data.status == 1) {
						// 广告类别列表
						self.adCateList = res.data.data;
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
