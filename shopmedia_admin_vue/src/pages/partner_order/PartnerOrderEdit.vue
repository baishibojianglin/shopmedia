<template>
	<div class="partner_order_edit">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>编辑合作商订单</span></el-col>
					<el-col :span="3">
						<el-button size="mini" icon="el-icon-back" title="返回" @click="back()">返回</el-button>
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- Form 表单 s -->
				<el-form ref="ruleForm" :model="form" :rules="rules" label-width="200px" size="small" class="demo-form-inline">
					<el-form-item prop="order_sn" label="订单编号">
						<el-input v-model="form.order_sn" disabled style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="order_price" label="订单价格/元">
						<el-input-number v-model="form.order_price" :min="0" :step="1" :precision="2" controls-position="right"></el-input-number>
					</el-form-item>
					<el-form-item prop="pay_status" label="支付状态">
						<el-radio-group v-model="form.pay_status" @change="payStatusChange">
							<el-radio :label="0">未支付</el-radio>
							<el-radio :label="1">已支付</el-radio>
						</el-radio-group>
					</el-form-item>
					<el-form-item prop="order_status" label="订单状态">
						<el-radio-group v-model="form.order_status" @change="orderStatusChange">
							<el-radio :label="0">未付款</el-radio>
							<el-radio :label="1">已完成</el-radio>
							<el-radio :label="2">取消</el-radio>
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
				form: {},
				rules: { // 验证规则
					/* order_price: [
						{ required: true, message: '请输入订单价格', trigger: 'blur' }
					] */
				}
			}
		},
		created() {
			this.getParams();
			this.getPartnerOrder(); // 获取指定的订单信息
		},
		methods: {
			/**
			 * 获取路由带过来的参数
			 */
			getParams() {
				this.form.order_id = this.$route.query.order_id;
			},
			
			/**
			 * 获取指定的订单信息
			 */
			getPartnerOrder() {
				let self = this;
				this.$axios.get(this.$url + 'partner_order/' + this.form.order_id)
				.then(function(res) {
					if (res.data.status == 1) {
						// 订单信息
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
			 * 改变支付状态
			 * @param {Object} value
			 */
			payStatusChange(value) {
				this.form.order_status = value == 1 ? 1 : 0;
			},
			/**
			 * 改变订单状态
			 * @param {Object} value
			 */
			orderStatusChange(value) {
				this.form.pay_status = value == 1 ? 1 : 0;
			},
			
			/**
			 * 编辑订单提交表单
			 * @param {Object} formName
			 */
			submitForm(formName) {
				let self = this;
				this.$refs[formName].validate((valid) => {
					if (valid) {
						this.$axios.put(this.$url + 'partner_order/' + this.form.order_id, {
							// 参数
							order_price: this.form.order_price,
							order_status: this.form.order_status,
							pay_status: this.form.pay_status
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
							self.resetForm('ruleForm'); // 提交失败，重置表单
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
				this.getPartnerOrder();
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
