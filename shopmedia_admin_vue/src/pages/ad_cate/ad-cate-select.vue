<template>
	<el-select v-model="cate_id" placeholder="请选择…" clearable filterable @change="handleButton">
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
		props: ['value'],
		data() {
			return {
				cate_id: this.value, // 绑定值
				adCateList: [] // 广告类别列表
			}
		},
		mounted() {
			this.getAdCateList(); // 获取广告类别列表
		},
		watch: {
			//判断下拉框的值是否有改变
			value(val) {
				this.cate_id = val;//②监听外部对props属性result的变更，并同步到组件内的data属性myResult中
			},
			cate_id(val, oldVal){
				if(val!=oldVal) {
					this.$emit("input", val);//③组件内对myResult变更后向外部发送事件通知
				}
			}
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
			},
			
			// 下拉框点击事件
			handleButton(){
				/* 子组件通过事件发射向父组件传递事件及参数，$emit即为发射事件
				   第一个参数为向父组件传递的事件名，第二个参数为向父组件传递的参数 */
				this.$emit( 'handleButton', this.cate_id);
			}
		}
	}
</script>

<style>
</style>
