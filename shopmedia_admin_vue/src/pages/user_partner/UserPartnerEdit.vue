<template>
	<div class="user_partner_edit">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>编辑广告屏合作商</span></el-col>
					<el-col :span="3">
						<el-button size="mini" icon="el-icon-back" title="返回" @click="back()">返回</el-button>
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- Form 表单 s -->
				<el-form ref="ruleForm" :model="form" :rules="rules" label-width="200px" size="small" class="demo-form-inline">
					<el-form-item prop="user_name" label="用户名称">
						<el-input v-model="form.user_name" readonly style="width:350px;"></el-input>
					</el-form-item>
					<!-- <el-form-item prop="avatar" label="头像">
						<el-input v-model="form.avatar" v-show="false" style="width:350px;"></el-input>
						<el-image :src="form.avatar" fit="fill" style="width: 100px; height: 100px"></el-image>
					</el-form-item> -->
					<!-- <el-form-item prop="phone" label="电话号码">
						<el-input v-model="form.phone" readonly style="width:350px;"></el-input>
					</el-form-item> -->
					<el-form-item prop="money" label="剩余金额/元">
						<el-input v-model="form.money" placeholder="输入用户剩余金额" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="income" label="累计收益/元">
						<el-input v-model="form.income" placeholder="请输入累计收益金额" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="cash" label="累计提现/元">
						<el-input v-model="form.cash" placeholder="请输入累计提现金额" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="status" label="状态">
						<el-radio-group v-model="form.status">
							<el-radio v-for="(item, index) in {0: '禁用', 1: '启用', 2: '待审核', 3: '驳回'}" :label="Number(index)">{{item}}</el-radio>
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
				rules: { // 验证规则
					money: [
						{ required: true, message: '输入用户剩余金额', trigger: 'blur' }
					],
					income: [
						{ required: true, message: '请输入累计收益金额', trigger: 'blur' }
					],
					cash: [
						{ required: true, message: '请输入累计提现金额', trigger: 'blur' }
					]
				}
			}
		},
		created() {
			this.getParams();
			this.getUser(); // 获取指定的用户信息
		},
		methods: {
			/**
			 * 获取路由带过来的参数
			 */
			getParams() {
				this.form.user_id = this.$route.query.user_id;
			},
			
			/**
			 * 获取指定的用户（广告屏合作商）信息
			 */
			getUser() {
				let self = this;
				this.$axios.get(this.$url + 'user_partner/' + this.form.user_id, {
					/* headers: {
						'admin-user-id': JSON.parse(localStorage.getItem('admin_user')).id,
						'admin-user-token': JSON.parse(localStorage.getItem('admin_user')).token
					} */
				})
				.then(function(res) {
					if (res.data.status == 1) {
						// 用户（广告屏合作商）信息
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
			 * 编辑用户（广告屏合作商）提交表单
			 * @param {Object} formName
			 */
			submitForm(formName) {
				let self = this;
				this.$refs[formName].validate((valid) => {
					if (valid) {
						this.$axios.put(this.$url + 'user_partner/' + this.form.user_id, {
							// 参数
							money: this.form.money,
							income: this.form.income,
							cash: this.form.cash,
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
