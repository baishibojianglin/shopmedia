<template>
	<div class="shop_edit">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>编辑店家店铺</span></el-col>
					<el-col :span="3">
						<el-button size="mini" icon="el-icon-back" title="返回" @click="back()">返回</el-button>
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- Form 表单 s -->
				<el-form ref="ruleForm" :model="form" :rules="rules" label-width="200px" size="small" class="demo-form-inline">
					<el-form-item prop="user_name" label="店家名称">
						<el-input v-model="form.user_name" disabled style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="shop_name" label="店铺名称">
						<el-input v-model="form.shop_name" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="cate_id" label="店铺类别">
						<el-select v-model="form.cate" placeholder="请选择…" clearable filterable>
							<el-option v-for="item in shopCateList" :key="item.cate_id" :label="item.cate_name" :value="item.cate_id">
							</el-option>
						</el-select>
					</el-form-item>
					<el-form-item prop="region" label="店铺所在区域">
						<el-input v-model="form.province + '，' + form.city + '，' + form.county + '，' + form.town" disabled style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="address" label="详细地址">
						<el-input v-model="form.address" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="longitude" label="经度">
						<el-input-number v-model="form.longitude" :step="1" :precision="6" controls-position="right" style="width: 200px;"></el-input-number> <span class="text-info">{{form.longitude > 0 ? form.longitude + '°E' : -form.longitude + '°W'}}</span>
					</el-form-item>
					<el-form-item prop="latitude" label="纬度">
						<el-input-number v-model="form.latitude" :step="1" :precision="6" controls-position="right" style="width: 200px;"></el-input-number> <span class="text-info">{{form.latitude > 0 ? form.latitude + '°N' : -form.latitude + '°S'}}</span>
					</el-form-item>
					<el-form-item prop="shop_area" label="店铺面积/㎡">
						<el-input-number v-model="form.shop_area" :step="1" :precision="2" controls-position="right" style="width: 200px;"></el-input-number>
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
	export default {
		data() {
			return {
				form: {
					status: '' // 店铺状态
				},
				rules: { // 验证规则
					shop_name: [
						{ required: true, message: '请输入店铺名称', trigger: 'blur' }
					],
					address: [
						{ required: true, message: '请输入店铺详细地址', trigger: 'blur' }
					],
					longitude: [
						{ required: true, message: '请输入店铺经度位置', trigger: 'blur' }
					],
					latitude: [
						{ required: true, message: '请输入店铺纬度位置', trigger: 'blur' }
					]
				},
				
				shopCateList: [] // 店铺类别列表
			}
		},
		created() {
			this.getParams();
			this.getShopCateList(); // 获取店铺类别列表
			this.getShop(); // 获取指定的店铺信息
		},
		methods: {
			/**
			 * 获取路由带过来的参数
			 */
			getParams() {
				this.form.shop_id = this.$route.query.shop_id;
			},
			
			/**
			 * 获取店铺类别列表
			 */
			getShopCateList() {
				let self = this;
				this.$axios.get(this.$url + 'shop_cate_list')
				.then(function(res) {
					if (res.data.status == 1) {
						// 店铺类别列表
						self.shopCateList = res.data.data;
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
			 * 获取指定的店铺信息
			 */
			getShop() {
				let self = this;
				this.$axios.get(this.$url + 'shop/' + this.form.shop_id)
				.then(function(res) {
					if (res.data.status == 1) {
						// 店铺信息
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
			 * 编辑店铺信息提交表单
			 * @param {Object} formName
			 */
			submitForm(formName) {
				let self = this;
				this.$refs[formName].validate((valid) => {
					if (valid) {
						this.$axios.put(this.$url + 'shop/' + this.form.shop_id, {
							// 参数
							shop_name: this.form.shop_name,
							cate_id: this.form.cate,
							address: this.form.address,
							longitude: this.form.longitude,
							latitude: this.form.latitude,
							shop_area: this.form.shop_area,
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
				this.getShop();
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
