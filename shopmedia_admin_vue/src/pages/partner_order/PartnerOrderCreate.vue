<template>
	<div class="partner_order_edit">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>新增合作商订单</span></el-col>
					<el-col :span="3">
						<el-button size="mini" icon="el-icon-back" title="返回" @click="back()">返回</el-button>
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- Form 表单 s -->
				<el-form ref="ruleForm" :model="form" :rules="rules" label-width="200px" size="small" class="demo-form-inline">
					<el-form-item prop="partner_id" label="广告屏合作商">
						<el-select v-model="form.partner_id" placeholder="请选择…" clearable filterable>
							<el-option v-for="item in userPartnerList" :key="item.partner_id" :label="item.user_name" :value="item.partner_id"></el-option>
						</el-select>
					</el-form-item>
					<el-form-item prop="device_id" label="广告屏">
						<el-select v-model="form.device_id" placeholder="请选择…" clearable filterable>
							<el-option v-for="item in deviceList" :key="item.device_id" :label="item.shopname + ' ' + item.province + '，' + item.city + '，' + item.county + '，' + item.street " :value="item.device_id">
								<span style="float: left">{{ item.shopname }}</span>
								<span style="float: right; color: #8492a6; font-size: 13px;margin-left: 1rem;">{{ item.province + '，' + item.city + '，' + item.county + '，' + item.street }}</span>
							</el-option>
						</el-select>
					</el-form-item>
					<el-form-item prop="order_price" label="订单价格/元">
						<el-input-number v-model="form.order_price" :min="0" :step="1" :precision="2" controls-position="right"></el-input-number>
					</el-form-item>
					<el-form-item prop="pay_status" label="支付状态">
						<el-radio-group v-model="form.pay_status">
							<el-radio :label="0">未支付</el-radio>
							<el-radio :label="1">已支付</el-radio>
						</el-radio-group>
					</el-form-item>
					<el-form-item prop="order_status" label="订单状态">
						<el-radio-group v-model="form.order_status">
							<el-radio :label="0">未付款</el-radio>
							<el-radio :label="1">已完成</el-radio>
							<!-- <el-radio :label="2">取消</el-radio> -->
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
					partner_id: [
						{ required: true, message: '请选择广告屏合作商', trigger: 'change' }
					],
					device_id: [
						{ required: true, message: '请选择广告屏', trigger: 'change' }
					],
					order_price: [
						{ required: true, message: '请输入订单价格', trigger: 'blur' }
					]
				},
				
				userPartnerList: [], // 广告屏合作商列表
				deviceList: [] // 广告屏列表
			}
		},
		created() {
			this.getUserPartnerList(); // 获取广告屏合作商列表
			this.getDeviceList(); // 获取广告屏列表
		},
		methods: {
			/**
			 * 获取广告屏合作商列表
			 */
			getUserPartnerList() {
				let self = this;
				this.$axios.get(this.$url + 'user_partner_list')
					.then(function(res) {
						if (res.data.status == 1) {
							// 广告屏合作商列表
							self.userPartnerList = res.data.data;
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
			 * 获取获取广告屏列表
			 */
			getDeviceList() {
				let self = this;
				this.$axios.get(this.$url + 'device_list')
					.then(function(res) {
						if (res.data.status == 1) {
							// 获取广告屏列表
							self.deviceList = res.data.data;
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
			 * 新增订单提交表单
			 * @param {Object} formName
			 */
			submitForm(formName) {
				let self = this;
				this.$refs[formName].validate((valid) => {
					if (valid) {
						this.$axios.post(this.$url + 'partner_order', {
							// 参数
							partner_id: this.form.partner_id,
							device_id: this.form.device_id,
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
