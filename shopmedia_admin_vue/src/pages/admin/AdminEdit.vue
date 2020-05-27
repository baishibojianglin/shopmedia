<template>
	<div class="admin_edit">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>编辑管理员</span></el-col>
					<el-col :span="3">
						<el-button size="mini" icon="el-icon-back" title="返回" @click="back()">返回</el-button>
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- Form 表单 s -->
				<el-form ref="ruleForm" :model="form" :rules="rules" label-width="200px" size="small" class="demo-form-inline">
					<el-form-item prop="company_id" label="所属分公司">
						<el-select v-model="form.company_id" :disabled="companySelectDisabled" filterable @change="getAuthGroupTree">
							<el-option-group key="平台" label="平台">
								<el-option label="公司总平台" :value="0"></el-option>
							</el-option-group>
							<el-option-group key="分公司" label="分公司">
								<el-option
									v-for="item in companyOptions"
									:key="item.company_id"
									:label="item.company_name"
									:value="item.company_id">
								</el-option>
							</el-option-group>
						</el-select>
					</el-form-item>
					<!-- <el-form-item prop="user_name" label="管理员名称">
						<el-input v-model="form.user_name" placeholder="输入管理员名称" clearable style="width:350px;"></el-input>
					</el-form-item> -->
					<!-- <el-form-item prop="avatar" label="证件照(头像)">
						<el-input v-model="form.avatar" v-show="false" style="width:350px;"></el-input>
						<el-upload :action="this.$url+'upload'" name="avatar" :on-success="handleUploadSuccess" :limit="1">
						<el-button size="medium" type="primary" plain icon="el-icon-upload">上传证件照</el-button>
						</el-upload>
					</el-form-item> -->
					<el-form-item prop="account" label="管理员账号">
						<el-input v-model="form.account" placeholder="输入管理员账号" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="password" label="登录密码">
						<el-input v-model="form.password" placeholder="请输入密码" clearable show-password style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="phone" label="电话号码">
						<el-input v-model="form.phone" placeholder="输入管理员电话号码" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="status" label="状态">
						<el-radio-group v-model="form.status">
							<el-radio :label="1">启用</el-radio>
							<el-radio :label="0">禁用</el-radio>
						</el-radio-group>
					</el-form-item>
					<el-form-item prop="group_id" label="角色">
						<el-select v-model="form.group_id" filterable>
							<el-option-group v-for="group in authGroupOptions" :key="group.label" :label="group.label">
								<el-option
									v-for="item in group.options"
									:key="item.id"
									:label="item.title"
									:value="item.id">
								</el-option>
							</el-option-group>
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
				admin: {
					company_id: '',
					group_id: ''
				},
				form: {
					/* user_name: '', // 管理员名称
					avatar: '', // 管理员头像
					account: '', // 管理员号
					phone: '', // 电话号码
					status: '', // 状态 */
				},
				rules: { // 验证规则
					company_id: [
						{ required: true, message: '请选择分公司', trigger: 'change' }
					],
					/* user_name: [
						{ required: true, message: '请输入管理员名称', trigger: 'blur' },
						{ min: 2, max: 20, message: '长度在 2 到 20 个字符', trigger: 'blur' }
					], */
					/* avatar: [
						{ required: true, message: '请上传管理员头像', trigger: 'blur' }
					], */
					account: [
						{ required: true, message: '请输入管理员账号', trigger: 'blur' },
						{ min: 2, max: 20, message: '长度在 2 到 20 个字符', trigger: 'blur' }
					],
					password: [
						{ min: 6, max: 20, message: '长度在 6 到 20 个字符', trigger: 'blur' }
					],
					phone: [
						{ required: true, pattern: /^1[34578]\d{9}$/, message: '目前只支持中国大陆的手机号码', trigger: 'blur' }
					],
					group_id: [
						{ required: true, message: '请选择角色', trigger: 'change' }
					]
				},
				companyOptions: [], // 分公司下拉框列表
				companySelectDisabled: false, // 分公司下拉框禁用状态
				authGroupOptions: [], // 角色下拉框列表
			}
		},
		created() {
			this.getParams();
			this.getAdmin(); // 获取指定的管理员信息
			this.getCompanyTree(); // 获取分公司列表树
			// this.getAuthGroupTree(); // 获取角色列表树
		},
		methods: {
			/**
			 * 获取路由带过来的参数
			 */
			getParams() {
				this.form.id = this.$route.query.id;
			},
			
			/**
			 * 获取指定的管理员信息
			 */
			getAdmin() {
				let self = this;
				this.$axios.get(this.$url + 'admin/' + this.form.id, {
					/* headers: {
						'admin-user-id': JSON.parse(localStorage.getItem('admin_user')).id,
						'admin-user-token': JSON.parse(localStorage.getItem('admin_user')).token
					} */
				})
				.then(function(res) {
					if (res.data.status == 1) {
						// 管理员信息
						self.admin.company_id = res.data.data.company_id;
						self.admin.group_id = res.data.data.group_id;
						self.form = res.data.data;
						self.getAuthGroupTree(self.form.company_id);
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
			 * 获取分公司列表树
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
			getAuthGroupTree(company_id) {
				let self = this;
				
				// 初始化角色ID
				if (company_id != this.admin.company_id) {
					this.form.group_id = '';
				} else {
					this.form.group_id = this.admin.group_id;
				}
				
				this.$axios.get(this.$url + 'auth_group_tree', {
					params: {
						company_id: company_id
					},
					// 请求头配置
					headers: {
						'admin-user-id': JSON.parse(localStorage.getItem('admin_user')).id,
						'admin-user-token': JSON.parse(localStorage.getItem('admin_user')).token
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
			 * 编辑管理员提交表单
			 * @param {Object} formName
			 */
			submitForm(formName) {
				let self = this;
				this.$refs[formName].validate((valid) => {
					if (valid) {
						this.$axios.put(this.$url + 'admin/' + this.form.id, {
							// 参数
							company_id: this.form.company_id,
							// user_name: this.form.user_name,
							// avatar: this.form.avatar,
							account: this.form.account,
							password: this.form.password,
							phone: this.form.phone,
							status: this.form.status,
							group_id: this.form.group_id
						}, {
							// 请求头配置
							headers: {
								'admin-user-id': JSON.parse(localStorage.getItem('admin_user')).id,
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
				this.getAdmin();
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
