<template>
	<div class="user_salesman_create">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>新增业务员</span></el-col>
					<el-col :span="3">
						<el-button size="mini" icon="el-icon-back" title="返回" @click="back()">返回</el-button>
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- Form 表单 s -->
				<el-form ref="ruleForm" :model="form" :rules="rules" label-width="200px" size="small" class="demo-form-inline">
					<!-- <el-form-item prop="user_name" label="用户名称">
						<el-input v-model="form.user_name" disabled style="width:350px;"></el-input>
					</el-form-item> -->
					<el-form-item prop="phone" label="电话号码">
						<el-input v-model="form.phone" style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="password" label="登录密码">
						<el-input v-model="form.password" clearable show-password style="width:350px;"></el-input>
					</el-form-item>
					<!-- <el-form-item prop="avatar" label="头像">
						<el-input v-model="form.avatar" v-show="false" style="width:350px;"></el-input>
						<el-image :src="form.avatar" fit="fill" style="width: 100px; height: 100px"></el-image>
					</el-form-item> -->
					<el-form-item prop="role_id" label="业务员角色">
						<el-select v-model="form.role_id" placeholder="选择角色" filterable>
							<el-option
								v-for="item in userRoleList"
								:key="item.id"
								:label="item.title"
								:value="item.id">
							</el-option>
						</el-select>
					</el-form-item>
					<el-form-item prop="comm_ratio" label="业务员提成比例" required>
						<el-input v-model="form.comm_ratio" placeholder="请输入业务员提成比例" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="parent_comm_ratio" label="向上级业务员提成比例" required>
						<el-input v-model="form.parent_comm_ratio" placeholder="请输入向上级业务员的提成比例" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="auth_son_ratio" label="授权配置下级提成比例" required>
						<el-radio-group v-model="form.auth_son_ratio">
							<el-radio :label="1">允许</el-radio>
							<el-radio :label="0">禁止</el-radio>
						</el-radio-group>
					</el-form-item>
					<el-form-item prop="auth_open_user" label="授权开通目标客户" required>
						<el-radio-group v-model="form.auth_open_user">
							<el-radio :label="1">允许</el-radio>
							<el-radio :label="0">禁止</el-radio>
						</el-radio-group>
					</el-form-item>
					<el-form-item prop="status" label="角色状态" required>
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
					/* user_name: '', // 用户名称
					avatar: '', // 用户头像
					phone: '', // 电话号码
					status: '', // 状态 */
				},
				rules: { // 验证规则中
					phone: [
						{ required: true, pattern: /^1[34578]\d{9}$/, message: '目前只支持中国大陆的手机号码', trigger: 'blur' }
					],
					password: [
						{ required: true, message: '请输入密码', trigger: 'blur' },
						{ min: 6, max: 20, message: '长度在 6 到 20 个字符', trigger: 'blur' }
					],
					role_id: [
						{ required: true, message: '请选择业务员角色', trigger: 'change' }
					]
				},
				
				userRoleList: [] // 用户角色列表
			}
		},
		created() {
			this.getUserRoleList();
		},
		methods: {
			/**
			 * 获取用户角色（业务员）列表
			 */
			getUserRoleList() {
				let self = this;
				this.$axios.get(this.$url + 'user_role_list', {
					params: {
						parent_id: 1
					}/* ,
					headers: {
						'admin-user-id': JSON.parse(localStorage.getItem('admin_user')).id,
						'admin-user-token': JSON.parse(localStorage.getItem('admin_user')).token
					} */
				})
				.then(function(res) {
					if (res.data.status == 1) {
						// 用户角色列表
						self.userRoleList = res.data.data;
					} else {
						self.$message({
							message: '网络忙，请重试',
							type: 'warning'
						});
					}
				})
				.catch(function(error) {
					self.$message({
						message: error.response.data.message,
						type: 'warning'
					});
				});
			},
			
			/**
			 * 新增用户提交表单
			 * @param {Object} formName
			 */
			submitForm(formName) {
				let self = this;
				this.$refs[formName].validate((valid) => {
					if (valid) {
						this.$axios.post(this.$url + 'user_salesman', {
							// 参数
							phone: this.form.phone,
							password: this.form.password,
							role_id: this.form.role_id,
							comm_ratio: this.form.comm_ratio,
							parent_comm_ratio: this.form.parent_comm_ratio,
							auth_son_ratio: this.form.auth_son_ratio,
							auth_open_user: this.form.auth_open_user,
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
				this.getParams(); // 不写没有数据
				this.getUser();
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
