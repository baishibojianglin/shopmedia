<template>
	<div class="auth_group_add">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>新增角色</span></el-col>
					<el-col :span="3">
						<el-button size="mini" icon="el-icon-back" title="返回" @click="back()">返回</el-button>
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- Form 表单 s -->
				<el-form ref="ruleForm" :model="form" :rules="rules" label-width="200px" size="small" class="demo-form-inline">
					<el-form-item label="上级" prop="parent_id">
						<el-select v-model="form.parent_id" placeholder="请选择…" filterable>
							<el-option
								v-for="item in authGroupOptions"
								:key="item.id"
								:label="item.title"
								:value="item.id">
							</el-option>
						</el-select>
					</el-form-item>
					<el-form-item prop="title" label="角色名称">
						<el-input v-model="form.title" placeholder="输入角色名称" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="type" label="角色类型">
						<el-radio-group v-model="form.type">
							<el-radio :label="0">私有角色</el-radio>
							<el-radio :label="1">通用角色</el-radio>
						</el-radio-group>
					</el-form-item>
					<el-form-item prop="status" label="状态">
						<el-radio-group v-model="form.status">
							<el-radio :label="1">启用</el-radio>
							<el-radio :label="0">禁用</el-radio>
						</el-radio-group>
					</el-form-item>
					<el-form-item label="授权配置下级权限">
						<el-switch v-model="form.auth_rules" active-text="允许" inactive-text="禁止" :active-value="1" :inactive-value="0"></el-switch>
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
					title: '', // 角色名称
					status: '', // 状态
					parent_id: '', // 上级ID
					type: 0, // 角色类型
					auth_rules: 0 // 授权配置下级权限
				},
				rules: { // 验证规则
					parent_id: [
						{ required: true, message: '请选择上级', trigger: 'change' }
					],
					title: [
						{ required: true, message: '请输入角色名称', trigger: 'blur' },
						{ min: 1, max: 20, message: '长度在 1 到 20 个字符', trigger: 'blur' }
					]
					
				},
				authGroupOptions: [], // 上级角色下拉框列表
			}
		},
		created() {
			this.getAuthGroupTree(); // 获取角色列表树
		},
		methods: {
			/**
			 * 获取角色列表树
			 */
			getAuthGroupTree() {
				let self = this;
				this.$axios.get(this.$url + 'auth_group_tree', {
					// 请求头配置
					headers: {
						'company-user-id': JSON.parse(localStorage.getItem('company')).user_id,
						'company-user-token': JSON.parse(localStorage.getItem('company')).token
					}
				})
				.then(function(res) {
					if (res.data.status == 1) {
						self.authGroupOptions = res.data.data;
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
			 * 新增角色提交表单
			 * @param {Object} formName
			 */
			submitForm(formName) {
				let self = this;
				this.$refs[formName].validate((valid) => {
					if (valid) {
						this.$axios.post(this.$url + 'auth_group', {
							// 参数
							title: this.form.title,
							status: this.form.status,
							parent_id: this.form.parent_id,
							type: this.form.type,
							auth_rules: this.form.auth_rules,
						}, {
							// 请求头配置
							headers: {
								'company-user-id': JSON.parse(localStorage.getItem('company')).user_id,
								'company-user-token': JSON.parse(localStorage.getItem('company')).token
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
			back() {
				this.$router.go(-1);
			}
		}
	}
</script>

<style>
</style>
