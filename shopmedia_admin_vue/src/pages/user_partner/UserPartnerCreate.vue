<template>
	<div class="user_partner_create">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>新增广告屏合作商</span></el-col>
					<el-col :span="3">
						<el-button size="mini" icon="el-icon-back" title="返回" @click="back()">返回</el-button>
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- Form 表单 s -->
				<el-form ref="ruleForm" :model="form" :rules="rules" label-width="200px" size="small" class="demo-form-inline">
					<el-form-item label="供应商" prop="company_id">
						<el-select v-model="form.company_id" :disabled="companySelectDisabled" placeholder="请选择…" filterable>
							<el-option
								v-for="item in companyOptions"
								:key="item.id"
								:label="item.name"
								:value="item.id">
							</el-option>
						</el-select>
					</el-form-item>
					<el-form-item prop="user_name" label="广告屏合作商名称">
						<el-input v-model="form.user_name" placeholder="输入广告屏合作商名称" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="avatar" label="证件照(头像)">
						<el-input v-model="form.avatar" v-show="false" style="width:350px;"></el-input>
						<el-upload :action="this.$url+'upload'" name="avatar" :on-success="handleUploadSuccess" :limit="1">
						<el-button size="medium" type="primary" plain icon="el-icon-upload">上传证件照</el-button>
						</el-upload>
					</el-form-item>
					<el-form-item prop="account" label="广告屏合作商号">
						<el-input v-model="form.account" placeholder="输入广告屏合作商号" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="password" label="登录密码">
						<el-input v-model="form.password" placeholder="请输入密码" clearable show-password style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="phone" label="电话号码">
						<el-input v-model="form.phone" placeholder="输入广告屏合作商电话号码" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="ratio" label="提成比例">
						<el-input v-model="form.ratio" placeholder="输入广告屏合作商提成比例" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="status" label="状态">
						<el-radio-group v-model="form.status">
							<el-radio :label="1">启用</el-radio>
							<el-radio :label="0">禁用</el-radio>
						</el-radio-group>
					</el-form-item>
					<el-form-item label="角色" prop="group_id">
						<el-select v-model="form.group_id" placeholder="请选择…" filterable>
							<el-option
								v-for="item in authGroupOptions"
								:key="item.id"
								:label="item.title"
								:value="item.id">
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
				form: {
					company_id: '', // 供应商ID
					user_name: '', // 广告屏合作商名称
					avatar: '', // 广告屏合作商证件照
					account: '', // 广告屏合作商号
					password: '', // 密码
					phone: '', // 电话号码
					ratio: '', // 提成比例
					status: '', // 状态 
					group_id: '', // 角色ID
				},
				rules: { // 验证规则
					company_id: [
						{ required: true, message: '请选择供应商', trigger: 'change' }
					],
					user_name: [
						{ required: true, message: '请输入广告屏合作商名称', trigger: 'blur' },
						{ min: 2, max: 20, message: '长度在 2 到 20 个字符', trigger: 'blur' }
					],
					/* avatar: [
						{ required: true, message: '请上传广告屏合作商证件照', trigger: 'blur' }
					], */
					account: [
						{ required: true, message: '请输入广告屏合作商号', trigger: 'blur' },
						{ min: 2, max: 20, message: '长度在 2 到 20 个字符', trigger: 'blur' }
					],
					password: [
						{ required: true, message: '请输入密码', trigger: 'blur' },
						{ min: 6, max: 20, message: '长度在 6 到 20 个字符', trigger: 'blur' }
					],
					phone: [
						{ required: true, pattern: /^1[34578]\d{9}$/, message: '目前只支持中国大陆的手机号码', trigger: 'blur' }
					],
					group_id: [
						{ required: true, message: '请选择角色', trigger: 'change' }
					],
				},
				companyOptions: [], // 供应商下拉框列表
				companySelectDisabled: false, // 供应商下拉框禁用状态
				authGroupOptions: [], // 角色下拉框列表
			}
		},
		created() {
			this.getCompanyTree(); // 获取供应商列表树
			this.getAuthGroupTree(); // 获取角色列表树
			
			// 供应商ID
			this.form.company_id = JSON.parse(localStorage.getItem('admin_user')).company_id;
			if (this.form.company_id != 0) {
				this.companySelectDisabled = true;
			}
		},
		methods: {
			/**
			 * 获取供应商列表树
			 */
			getCompanyTree() {
				let self = this;
				this.$axios.get(this.$url + 'company_tree')
				.then(function(res) {
					if (res.data.status == 1) {
						self.companyOptions = res.data.data;
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
			 * 获取角色列表树
			 */
			getAuthGroupTree() {
				let self = this;
				this.$axios.get(this.$url + 'auth_group_tree', {
					// 请求头配置
					/* headers: {
						'admin-user-id': JSON.parse(localStorage.getItem('admin_user')).user_id,
						'admin-user-token': JSON.parse(localStorage.getItem('admin_user')).token
					} */
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
			 * 新增广告屏合作商提交表单
			 * @param {Object} formName
			 */
			submitForm(formName) {
				let self = this;
				this.$refs[formName].validate((valid) => {
					if (valid) {
						this.$axios.post(this.$url + 'company_user', {
							// 参数
							company_id: this.form.company_id,
							user_name: this.form.user_name,
							avatar: this.form.avatar,
							account: this.form.account,
							password: this.form.password,
							phone: this.form.phone,
							ratio: this.form.ratio,
							status: this.form.status,
							group_id: this.form.group_id
						}, {
							// 请求头配置
							/* headers: {
								'admin-user-id': JSON.parse(localStorage.getItem('admin_user')).user_id,
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
			},
			
			/**
			 * 返回上一页
			 */
			back(){
				this.$router.go(-1);
			},
			
			/**
			 * 文件上传成功时的钩子
			 * @param {Object} response
			 * @param {Object} file
			 * @param {Object} fileList
			 */
			handleUploadSuccess(response, file, fileList){
				console.log(file);
			}
		}
	}
</script>

<style>
</style>
