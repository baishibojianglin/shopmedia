<template>
	<div class="partner_device_edit">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>编辑广告屏合作商份额</span></el-col>
					<el-col :span="3">
						<el-button size="mini" icon="el-icon-back" title="返回" @click="back()">返回</el-button>
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- Form 表单 s -->
				<el-form ref="ruleForm" :model="form" :rules="rules" label-width="200px" size="small" class="demo-form-inline">
					<el-form-item prop="user_name" label="广告屏合作商">
						<el-input v-model="form.user_name" readonly style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="device_id" label="设备编号">
						<el-input v-model="form.device_id" readonly style="width:350px;"></el-input>
					</el-form-item>
					<!-- <el-form-item prop="agreement" label="租用协议">
						<el-input v-model="form.agreement" readonly style="width:350px;"></el-input>
					</el-form-item> -->
					<el-form-item prop="share" label="用户份额">
						<el-input v-model="form.share" placeholder="输入该设备用户所占份额" clearable style="width:350px;"></el-input>
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
					partner_device_id: '',
					user_id: '',
					user_name: '',
					device_id: '',
					share: '',
					agreement: ''
				},
				rules: { // 验证规则
					share: [
						{ required: true, message: '请输入用户份额', trigger: 'blur' }
					]
				}
			}
		},
		created() {
			this.getParams();
		},
		methods: {
			/**
			 * 获取路由带过来的参数
			 */
			getParams() {
				this.form.partner_device_id = this.$route.query.partner_device_id;
				this.form.user_id = this.$route.query.user_id;
				this.form.user_name = this.$route.query.user_name;
				this.form.device_id = this.$route.query.device_id;
				this.form.share = this.$route.query.share;
			},
			
			/**
			 * 编辑广告屏合作商份额提交表单
			 * @param {Object} formName
			 */
			submitForm(formName) {
				let self = this;
				this.$refs[formName].validate((valid) => {
					if (valid) {
						this.$axios.put(this.$url + 'partner_device/' + this.form.partner_device_id, {
							// 参数
							device_id: this.form.device_id,
							share: this.form.share
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
