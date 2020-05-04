<template>
	<div class="feedback_handle">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>更新用户反馈状态</span></el-col>
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
					<el-form-item prop="phone" label="用户电话">
						<el-input v-model="form.phone" readonly style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="content" label="反馈内容">
						<el-input type="textarea" :rows="5" v-model="form.content" readonly></el-input>
					</el-form-item>
					<el-form-item prop="status" label="反馈处理状态">
						<el-radio-group v-model="form.status">
							<el-radio v-for="(item, index) in {0: '待处理', 1: '已处理', 2: '处理中'}" :key="index" :label="Number(index)">{{item}}</el-radio>
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
					status: '' // 反馈处理状态
				},
				rules: { // 验证规则
					status: [
						{ required: true, message: '请选择反馈处理状态', trigger: 'change' }
					]
				}
			}
		},
		created() {
			this.getParams();
			this.getFeedback(); // 获取指定的用户反馈信息
		},
		methods: {
			/**
			 * 获取路由带过来的参数
			 */
			getParams() {
				this.form.id = this.$route.query.id;
			},
			
			/**
			 * 获取指定的用户反馈信息
			 */
			getFeedback() {
				let self = this;
				this.$axios.get(this.$url + 'feedback/' + this.form.id)
				.then(function(res) {
					if (res.data.status == 1) {
						// 用户反馈信息
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
			 * 更新用户反馈处理状态提交表单
			 * @param {Object} formName
			 */
			submitForm(formName) {
				let self = this;
				this.$refs[formName].validate((valid) => {
					if (valid) {
						this.$axios.put(this.$url + 'feedback/' + this.form.id, {
							// 参数
							status: this.form.status
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
				this.getFeedback();
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
