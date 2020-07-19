<template>
	<div class="ad_case_edit">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>编辑广告案例</span></el-col>
					<el-col :span="3">
						<el-button size="mini" icon="el-icon-back" title="返回" @click="back()">返回</el-button>
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- Form 表单 s -->
				<el-form ref="ruleForm" :model="form" :rules="rules" label-width="200px" size="small" class="demo-form-inline">
					<el-form-item prop="ad_name" label="广告名称">
						<el-input v-model="form.ad_name" placeholder="输入广告名称" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="advertiser_name" label="广告主名称">
						<el-input v-model="form.advertiser_name" placeholder="输入广告主名称" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="advertiser_phone" label="广告主电话">
						<el-input v-model="form.advertiser_phone" placeholder="输入广告主电话" clearable style="width:350px;"></el-input>
					</el-form-item>
					
					<el-form-item prop="ad_cover" label="广告案例封面图">
						<el-input v-model="form.ad_cover" placeholder="输入广告案例封面图地址" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="ad_video" label="广告案例视频">
						<el-input v-model="form.ad_video" placeholder="输入广告案例视频地址" clearable style="width:350px;"></el-input>
					</el-form-item>
					
					<el-form-item prop="status" label="是否显示">
						<el-radio-group v-model="form.status">
							<el-radio v-for="(item, index) in {0: '隐藏', 1: '显示'}" :key="index" :label="Number(index)">{{item}}</el-radio>
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
					ad_case_id: '', // 广告案例ID
					ad_name: '', // 广告名称
					advertiser_name: '', // 广告主名称
					advertiser_phone: '', // 广告主电话
					ad_cover: '', // 广告案例封面图
					ad_video: '', // 广告案例视频
					status: '' // 显示状态
				},
				rules: { // 验证规则
					ad_name: [
						{ required: true, message: '请输入奖品名称', trigger: 'blur' },
						{ min: 1, max: 60, message: '长度在 1 到 60 个字符', trigger: 'blur' }
					],
					advertiser_name: [{required: true, message: '请输入广告主名称', trigger: 'blur'}],
					advertiser_phone: [{required: true, message: '请输入广告主电话', trigger: 'blur'}],
					ad_cover: [{required: true, message: '请输入广告封面图地址', trigger: 'blur'}],
					ad_video: [{required: true, message: '请输入广告案例视频地址', trigger: 'blur'}]
				}
			}
		},
		created() {
			this.getParams();
			this.getAdCase();
		},
		methods: {
			/**
			 * 获取路由带过来的参数
			 */
			getParams() {
				this.form.ad_case_id = this.$route.query.ad_case_id;
			},
			
			/**
			 * 获取指定的广告案例信息
			 */
			getAdCase() {
				let self = this;
				this.$axios.get(this.$url + 'ad_case/' + this.form.ad_case_id)
				.then(function(res) {
					if (res.data.status == 1) {
						// 广告案例信息
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
			 * 新增活动奖品提交表单
			 * @param {Object} formName
			 */
			submitForm(formName) {
				let self = this;
				this.$refs[formName].validate((valid) => {
					if (valid) {
						this.$axios.put(this.$url + 'ad_case/' + this.form.ad_case_id, {
							// 参数
							ad_name: this.form.ad_name,
							advertiser_name: this.form.advertiser_name,
							advertiser_phone: this.form.advertiser_phone,
							ad_cover: this.form.ad_cover,
							ad_video: this.form.ad_video,
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
				this.getAdCase();
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
