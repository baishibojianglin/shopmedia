<template>
	<div class="goods_cate_add">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>新增商品类别</span></el-col>
					<el-col :span="3">
						<el-button size="mini" icon="el-icon-back" title="返回" @click="back()">返回</el-button>
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- Form 表单 s -->
				<el-form ref="ruleForm" :model="form" :rules="rules" label-width="200px" size="small" class="demo-form-inline">
					<el-form-item label="商品类别" prop="cate_name">
						<el-input v-model="form.cate_name" placeholder="输入商品类别名称" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item label="上级类别" prop="parent_id">
						<el-select v-model="form.parent_id" placeholder="请选择…" filterable>
							<el-option :key="0" label="（一级类别）" :value="0"></el-option>
							<el-option
								v-for="item in options"
								:key="item.cate_id"
								:label="item.cate_name"
								:value="item.cate_id">
							</el-option>
						</el-select>
					</el-form-item>
					<el-form-item>
						<el-button type="primary" plain @click="submitForm('ruleForm')">提交</el-button>
						<el-button plain @click="resetForm('ruleForm')">重置</el-button>
					</el-form-item>
				</el-form>
				<!-- Form 表单 e -->
			</div>
		</el-card>
	</div>
</template>

<script>
	export default {
		data() {
			return {
				options: [], // 上级类别下拉框列表，如 [{value: '选项1', label: '黄金糕'}, {…}, …]
				form: {
					cate_name: '', // 商品类别名称
					parent_id: '', // 上级ID
				},
				rules: { // 验证规则
					cate_name: [
						{ required: true, message: '请输入商品类别名称', trigger: 'blur' },
						{ min: 1, max: 20, message: '长度在 1 到 20 个字符', trigger: 'blur' }
					],
					parent_id: [
						{ required: true, message: '请选择上级类别', trigger: 'change' }
					]
				}
			}
		},
		created() {
			this.getGoodsCateTree(); // 获取商品类别列表树
		},
		methods: {
			/**
			 * 获取商品类别列表树
			 */
			getGoodsCateTree() {
				let self = this;
				this.$axios.get(this.$url + 'goods_cate_tree')
				.then(function(res) {
					if (res.data.status == 1) {
						self.options = res.data.data;
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
			
			/**
			 * 新增商品类别提交表单
			 * @param {Object} formName
			 */
			submitForm(formName) {
				let self = this;
				this.$refs[formName].validate((valid) => {
					if (valid) {
						this.$axios.post(this.$url + 'goods_cate', {
							// 参数
							cate_name: this.form.cate_name,
							parent_id: this.form.parent_id
						}, {
							// 请求头配置
							headers: {
								'admin-user-id': JSON.parse(localStorage.getItem('admin_user')).user_id,
								'admin-user-token': JSON.parse(localStorage.getItem('admin_user')).token
							}
						})
						.then(function(res) {
							let type = res.data.status == 1 ? 'success' : 'warning';
							self.$message({
								message: res.data.message,
								type: type
							});
							self.$router.go(-1); // 返回上一页
						})
						.catch(function (error) {
							self.$message({
								message: error.response.data.message,
								type: 'warning'
							});
						});
					} else {
						self.$message({
							message: 'error submit!!',
							type: 'warning',
						});
						return false;
					}
				});
			},
			
			/**
			 * 重置表单
			 * @param {Object} formName
			 */
			resetForm(formName) {
				this.$refs[formName].resetFields();
			},
			
			/**
			 * 返回上一页
			 */
			back(){
				this.$router.go(-1);
			}
		}
	}
</script>

<style>
</style>
