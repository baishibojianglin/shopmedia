<template>
	<div class="goods_brand_edit">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>编辑商品品牌</span></el-col>
					<el-col :span="3">
						<el-button size="mini" icon="el-icon-back" title="返回" @click="back()">返回</el-button>
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- Form 表单 s -->
				<el-form ref="ruleForm" :model="form" :rules="rules" label-width="200px" size="small" class="demo-form-inline">
					<el-form-item prop="brand_name" label="商品品牌">
						<el-input v-model="form.brand_name" placeholder="输入商品品牌名称" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="logo" label="logo">
						<el-input v-model="form.logo" v-show="false" style="width:350px;"></el-input>
						<el-upload :action="this.$url+'upload'" name="logo" :on-success="handleUploadSuccess" :limit="1">
						<el-button size="medium" type="primary" plain icon="el-icon-upload">上传logo</el-button>
						</el-upload>
						<img :src="form.logo" :alt="form.brand_name" :title="form.brand_name" width="50" height="50" />
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
					brand_id: '', // 商品品牌ID
					brand_name: '', // 商品品牌名称
					logo: '', // 品牌logo
				},
				rules: { // 验证规则
					brand_name: [
						{ required: true, message: '请输入商品品牌名称', trigger: 'blur' },
						{ min: 1, max: 20, message: '长度在 1 到 20 个字符', trigger: 'blur' }
					],
					/* logo: [
						{ required: true, message: '请上传商品品牌logo', trigger: 'change' }
					] */
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
				this.form.brand_id = this.$route.query.brand_id;
				this.form.brand_name = this.$route.query.brand_name;
				this.form.logo = this.$route.query.logo;
			},
			
			/**
			 * 编辑商品品牌提交表单
			 * @param {Object} formName
			 */
			submitForm(formName) {
				let self = this;
				this.$refs[formName].validate((valid) => {
					if (valid) {
						this.$axios.put(this.$url + 'goods_brand/' + this.form.brand_id, {
							// 参数
							brand_name: this.form.brand_name,
							logo: this.form.logo
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
