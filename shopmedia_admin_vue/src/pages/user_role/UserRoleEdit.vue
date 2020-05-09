<template>
	<div class="user_role_edit">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>编辑用户角色</span></el-col>
					<el-col :span="3">
						<el-button size="mini" icon="el-icon-back" title="返回" @click="back()">返回</el-button>
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- Form 表单 s -->
				<el-form ref="ruleForm" :model="form" :rules="rules" label-width="200px" size="small" class="demo-form-inline">
					<el-form-item prop="title" label="角色名称">
						<el-input v-model="form.title" disabled style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="parent_comm_ratio" label="上级提成比例">
						<el-input v-model="form.parent_comm_ratio" placeholder="输入上级提成比例" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="status" label="状态">
						<el-radio-group v-model="form.status">
							<el-radio :label="1">启用</el-radio>
							<el-radio :label="0">禁用</el-radio>
						</el-radio-group>
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
				form: {
					/* title: '', // 用户角色名称
					status: '', // 状态 */
				},
				rules: { // 验证规则
					parent_comm_ratio: [
						{ required: true, message: '请输入向上级用户的统一提成比例', trigger: 'blur' }
					]
				}
			}
		},
		created() {
			this.getParams();
			this.getUserRole(); // 获取指定的用户角色信息
		},
		methods: {
			/**
			 * 获取路由带过来的参数
			 */
			getParams() {
				this.form.id = this.$route.query.id;
			},
			
			/**
			 * 获取指定的用户角色信息
			 */
			getUserRole() {
				let self = this;
				this.$axios.get(this.$url + 'user_role/' + this.form.id, {
					/* headers: {
						'admin-user-id': JSON.parse(localStorage.getItem('admin_user')).id,
						'admin-user-token': JSON.parse(localStorage.getItem('admin_user')).token
					} */
				})
				.then(function(res) {
					if (res.data.status == 1) {
						// 用户角色信息
						self.form = res.data.data;
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
			 * 编辑用户角色提交表单
			 * @param {Object} formName
			 */
			submitForm(formName) {
				let self = this;
				this.$refs[formName].validate((valid) => {
					if (valid) {
						this.$axios.put(this.$url + 'user_role/' + this.form.id, {
							// 参数
							parent_comm_ratio: this.form.parent_comm_ratio,
							status: this.form.status
						}, {
							// 请求头配置
							/* headers: {
								'admin-user-id': JSON.parse(localStorage.getItem('admin_user')).id,
								'admin-user-token': JSON.parse(localStorage.getItem('admin_user')).token
							} */
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
				this.getUserRole();
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
