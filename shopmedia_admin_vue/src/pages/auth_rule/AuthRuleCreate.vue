<template>
	<div class="auth_rule_create">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>新增权限规则</span></el-col>
					<el-col :span="3">
						<el-button size="mini" icon="el-icon-back" title="返回" @click="back()">返回</el-button>
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- Form 表单 s -->
				<el-form ref="ruleForm" :model="form" :rules="rules" label-width="200px" size="small" class="demo-form-inline">
					<el-form-item prop="title" label="规则名称">
						<el-input v-model="form.title" placeholder="输入权限规则名称" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="name" label="权限规则">
						<el-input v-model="form.name" placeholder="输入权限规则" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="type" label="规则类型">
						<el-radio-group v-model="form.type">
							<el-radio :label="1">url</el-radio>
							<el-radio :label="2">menu</el-radio>
						</el-radio-group>
					</el-form-item>
					<el-form-item prop="condition" label="权限规则验证条件">
						<el-input v-model="form.condition" placeholder="输入权限规则验证条件" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="module" label="规则所属模块">
						<el-input v-model="form.module" placeholder="输入权限规则所属模块，默认为 admin" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item label="上级规则/序号" prop="pid">
						<el-select v-model="form.pid" placeholder="请选择…" filterable>
							<el-option :key="0" label="（顶级规则）" :value="0"></el-option>
							<el-option
								v-for="item in authRuleOptions"
								:key="item.id"
								:label="item.title + '/' + item.id"
								:value="item.id">
							</el-option>
						</el-select>
					</el-form-item>
					<el-form-item prop="icon" label="图标">
						<el-input v-model="form.icon" placeholder="输入图标" clearable :prefix-icon="form.icon" style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="sort" label="同级排序">
						<el-input type="number" v-model="form.sort" placeholder="输入同级权限规则序号" clearable style="width:350px;"></el-input>
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
					name: '', // 权限规则
					title: '', // 权限规则名称
					type: '', // 权限规则类型
					condition: '', // 权限规则验证条件
					module: 'admin', // 规则所属模块
					pid: '', // 上级ID
					icon: '', // 图标
					sort: '' // 排序
				},
				rules: { // 验证规则
					name: [
						{ required: true, message: '请输入权限规则', trigger: 'blur' },
						{ min: 1, max: 80, message: '长度在 1 到 80 个字符', trigger: 'blur' }
					],
					title: [
						{ required: true, message: '请输入权限规则名称', trigger: 'blur' },
						{ min: 1, max: 20, message: '长度在 1 到 20 个字符', trigger: 'blur' }
					],
					type: [
						{ required: true, message: '请选择规则类型', trigger: 'change' }
					],
					module: [
						{ required: true, message: '输入权限规则所属模块，如：admin', trigger: 'blur' }
					],
					pid: [
						{ required: true, message: '请选择上级', trigger: 'change' }
					]
				},
				authRuleOptions: [], // 上级权限规则下拉框列表
			}
		},
		created() {
			this.getAuthRuleTree(); // 获取权限规则列表树
		},
		methods: {
			/**
			 * 获取权限规则列表树
			 */
			getAuthRuleTree() {
				let self = this;
				this.$axios.get(this.$url + 'auth_rule_tree', {
					// 请求头配置
					headers: {
						'admin-user-id': JSON.parse(localStorage.getItem('admin_user')).user_id,
						'admin-user-token': JSON.parse(localStorage.getItem('admin_user')).token
					}
				})
				.then(function(res) {
					if (res.data.status == 1) {
						self.authRuleOptions = res.data.data;
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
			 * 新增权限规则提交表单
			 * @param {Object} formName
			 */
			submitForm(formName) {
				let self = this;
				this.$refs[formName].validate((valid) => {
					if (valid) {
						this.$axios.post(this.$url + 'auth_rule', {
							// 参数
							name: this.form.name,
							title: this.form.title,
							type: this.form.type,
							condition: this.form.condition,
							module: this.form.module,
							pid: this.form.pid,
							icon: this.form.icon,
							sort: this.form.sort
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
			back() {
				this.$router.go(-1);
			}
		}
	}
</script>

<style>
</style>
