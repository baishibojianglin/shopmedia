<template>
	<div class="activity_create">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>编辑活动</span></el-col>
					<el-col :span="3">
						<el-button size="mini" icon="el-icon-back" title="返回" @click="back()">返回</el-button>
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- Form 表单 s -->
				<el-form ref="ruleForm" :model="form" :rules="rules" label-width="200px" size="small" class="demo-form-inline">
					<el-form-item prop="act_name" label="活动名称">
						<el-input v-model="form.act_name" placeholder="输入活动名称" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="act_datetime" label="活动时间">
						<el-date-picker v-model="form.act_datetime" type="datetimerange" range-separator="至" start-placeholder="开始日期" end-placeholder="结束日期"></el-date-picker>
					</el-form-item>
					<el-form-item prop="times" label="每人每天抽奖限制次数">
						<el-input-number v-model="form.times" :min="0" :step="1" :precision="0" controls-position="right"></el-input-number>
					</el-form-item>
					<el-form-item prop="description" label="活动描述">
						<quill-editor ref="myTextEditor" v-model="form.description" :options="editorOption" style="height:600px;margin-bottom: 6.25rem;"></quill-editor>
					</el-form-item>
					<el-form-item prop="status" label="状态">
						<el-radio-group v-model="form.status">
							<el-radio v-for="(item, index) in {0: '禁用', 1: '启用'}" :key="index" :label="Number(index)">{{item}}</el-radio>
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
	import 'quill/dist/quill.core.css'
	import 'quill/dist/quill.snow.css'
	import 'quill/dist/quill.bubble.css'
	
	import { quillEditor } from 'vue-quill-editor'
	
	export default {
		components: {
			quillEditor
		},
		data() {
			return {
				form: {
					act_id: '', // 活动ID
					act_name: '', // 活动名称
					act_datetime: '', // 活动时间
					times: '', // 每人每天抽奖限制次数
					description: '', // 活动描述
					status: '' // 活动状态
				},
				rules: { // 验证规则
					act_name: [
						{ required: true, message: '请输入活动名称', trigger: 'blur' },
						{ min: 1, max: 150, message: '长度在 1 到 150 个字符', trigger: 'blur' }
					],
					act_datetime: [{ /* type: 'date', */ required: true, message: '请选择活动时间', trigger: 'change'}],
					times: [{required: true, message: '每人每天抽奖限制次数', trigger: 'blur'}],
					description: [
						{ required: true, message: '请输入活动描述', trigger: 'blur' }
					]
				},
				
				editorOption: {
					placeholder: '输入活动描述'
				},
			}
		},
		created() {
			this.getParams();
			this.getActivity(); // 获取指定的活动信息
		},
		methods: {
			/**
			 * 获取路由带过来的参数
			 */
			getParams() {
				this.form.act_id = this.$route.query.act_id;
			},
			
			/**
			 * 获取指定的活动信息
			 */
			getActivity() {
				let self = this;
				this.$axios.get(this.$url + 'activity/' + this.form.act_id)
				.then(function(res) {
					if (res.data.status == 1) {
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
			 * 编辑活动提交表单
			 * @param {Object} formName
			 */
			submitForm(formName) {
				let self = this;
				this.$refs[formName].validate((valid) => {
					if (valid) {
						this.$axios.put(this.$url + 'activity/' + this.form.activity_id, {
							// 参数
							act_name: this.form.act_name,
							act_datetime: this.form.act_datetime,
							times: this.form.times,
							description: this.form.description,
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
				this.getActivity()();
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
