<template>
	<div class="ad_create">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>新增广告</span></el-col>
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
					<el-form-item prop="ad_cate_id" label="广告类别">
						<ad-cate-select :value="form.ad_cate_id"></ad-cate-select>
					</el-form-item>
					<el-form-item prop="ad_price" label="广告价格(元)">
						<el-input-number v-model="form.ad_price" :min="0" :step="1" :precision="2" controls-position="right"></el-input-number>
					</el-form-item>
					<el-form-item prop="ad_datetime" label="投放时间">
						<el-date-picker
							v-model="form.ad_datetime"
							type="datetimerange"
							range-separator="至"
							start-placeholder="开始日期"
							end-placeholder="结束日期">
						</el-date-picker>
					</el-form-item>
					<el-form-item prop="ad_time" label="每日播放时间段">
						<el-time-picker
							is-range
							v-model="form.ad_time"
							range-separator="至"
							start-placeholder="开始时间"
							end-placeholder="结束时间"
							placeholder="选择时间范围">
						</el-time-picker>
					</el-form-item>
					<el-form-item prop="play_times" label="每日播放次数">
						<el-input-number v-model="form.play_times" :min="0" :step="1" :precision="0" controls-position="right"></el-input-number>
					</el-form-item>
					<el-form-item prop="advertisers" label="广告主名称">
						<el-input v-model="form.advertisers" placeholder="输入广告主名称" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="phone" label="广告主电话">
						<el-input v-model="form.phone" placeholder="输入广告主联系电话" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="shop_cate_id" label="投放店铺类别">
						<shop-cate-select :value="form.shop_cate_id"></shop-cate-select>
					</el-form-item>
					<el-form-item prop="region" label="投放区域">
						<!-- TODO -->
					</el-form-item>
					<el-form-item prop="logo" label="logo">
						<el-input v-model="form.logo" v-show="false" style="width:350px;"></el-input>
						<el-upload :action="this.$url+'upload'" name="logo" :on-success="handleUploadSuccess" :limit="1">
						<el-button size="medium" type="primary" plain icon="el-icon-upload">上传logo</el-button>
						</el-upload>
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
	import adCateSelect from '@/pages/ad_cate/ad-cate-select.vue';
	import shopCateSelect from '@/pages/shop_cate/shop-cate-select.vue';
	
	export default {
		components: {
			adCateSelect,
			shopCateSelect
		},
		data() {
			return {
				form: {
					ad_name: '', // 广告名称
					ad_cate_id: '', // 广告类别ID
					ad_price: '',
					logo: '', // 品牌logo
				},
				rules: { // 验证规则
					ad_name: [
						{ required: true, message: '请输入广告名称', trigger: 'blur' },
						{ min: 1, max: 20, message: '长度在 1 到 20 个字符', trigger: 'blur' }
					],
					/* logo: [
						{ required: true, message: '请上传广告logo', trigger: 'blur' }
					] */
				}
			}
		},
		methods: {
			/**
			 * 新增广告类别提交表单
			 * @param {Object} formName
			 */
			submitForm(formName) {
				let self = this;
				this.$refs[formName].validate((valid) => {
					if (valid) {
						this.$axios.post(this.$url + 'ad', {
							// 参数
							ad_name: this.form.ad_name,
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
