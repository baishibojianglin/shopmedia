<template>
	<div class="create">
		<el-card class="box-card">

			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="24"><span class="el-icon-edit color-blue"></span> 编辑分公司信息</el-col>
				</el-row>
			</div>

			<el-form v-loading="loading" ref="ruleForm" :model="ruleForm" :rules="rules" label-width="150px">

				<el-form-item label="分公司名称" prop="company_name">
					<el-input style="width:217px;" v-model="ruleForm.company_name"></el-input>
				</el-form-item>

				<el-form-item label="所在地区" prop="city_id">
					<el-select @change="zone" v-model="ruleForm.province_id" placeholder="请选择">
						<el-option v-for="item in provincelist" :key="item.value" :label="item.label" :value="item.value">
						</el-option>
					</el-select>
					<el-select style="margin-left: 5px;" v-model="ruleForm.city_id" placeholder="请选择">
						<el-option v-for="item in citylist" :key="item.value" :label="item.label" :value="item.value">
						</el-option>
					</el-select>
				</el-form-item>

				<el-form-item label="分公司状态" prop="status">
					<el-select v-model="ruleForm.status" placeholder="请选择">
						<el-option v-for="item in status_options" :key="item.value" :label="item.label" :value="item.value">
						</el-option>
					</el-select>
				</el-form-item>

				<el-form-item label="负责人姓名" prop="person_name">
					<el-input style="width:217px;" v-model="ruleForm.person_name"></el-input>
				</el-form-item>

				<el-form-item label="联系电话" prop="phone">
					<el-input style="width:217px;" v-model="ruleForm.phone"></el-input>
				</el-form-item>

				<el-form-item>
					<el-button type="primary" @click="submitForm('ruleForm')">提交</el-button>
					<el-button @click="resetForm('ruleForm')">返回</el-button>
				</el-form-item>

			</el-form>
		</el-card>
	</div>
</template>

<script>
	export default {
		data() {
			return {
				status_options: [{
						value: 1,
						label: '正常'
					},
					{
						value: 0,
						label: '禁用'
					}
				],
				ruleForm: {
					company_name: '', //供应商名字
					province_id: '', //省级
					city_id: '', //市级
					person_name: '', //负责人姓名
					phone: '', //联系电话
					status: '' //状态
				},
				rules: {
					company_name: [{
						required: true,
						message: '请输入分公司名称',
						trigger: 'blur'
					}],
					city_id: [{
						required: true,
						message: '请选择地区',
						trigger: 'blur'
					}],
					person_name: [{
						required: true,
						message: '请输入负责人姓名',
						trigger: 'blur'
					}],
					phone: [{
						required: true,
						message: '请输入联系电话',
						trigger: 'blur'
					}],
					status: [{
						required: true,
						message: '请选择状态',
						trigger: 'blur'
					}]
				},
				company_id: '', //分公司id
				provincelist: [],
				citylist: [],
				loading: true
			}
		},
		mounted() {
			//初始化省级地址列表
			this.zone();
			//初始化加载市级地址列表
			this.zone(this.$route.query.province_id);
			//如果是编辑，赋值原来的数据
			this.getdata();
		},
		methods: {
			/**
			 * 回显数据
			 */
			getdata() {
				let self = this;
				this.$axios.get(this.$url + 'company/' + this.$route.query.company_id)
				.then(function(res) {
					if (res.data.status == 1) {
						self.company_id = res.data.data.company_id;
						delete res.data.data.company_id;
						self.ruleForm = res.data.data;
						self.loading = false;
					}
				})
			},

			/**
			 * 获取地区列表
			 */
			zone(t_parent_id) {
				let self = this;
				let parent_id = 0;
				if (t_parent_id) {
					parent_id = t_parent_id;
				}
				self.ruleForm.city_id = ''; //清楚上次选择的市级数据
				this.$axios.get(this.$url + 'get_region_list', {
					params: {
						parent_id: parent_id
					}
				}).then(function(res) {
					if (res.data.status == 1) {
						if (parent_id == 0) { //省级
							self.provincelist.splice(0, self.provincelist.length);
							res.data.data.forEach((value, index) => {
								self.$set(self.provincelist, index, {
									value: value.region_id,
									label: value.region_name
								});
							})
						} else { //市级
							self.citylist.splice(0, self.citylist.length);
							res.data.data.forEach((value, index) => {
								self.$set(self.citylist, index, {
									value: value.region_id,
									label: value.region_name
								});
							})
						}

					} else {
						self.$message({
							message: '网络繁忙，请重试',
							type: 'warning'
						});
					}
				})
			},

			/**
			 * 提交表单
			 * @param {Object} formName
			 */
			submitForm(formName) {
				let self = this;
				this.$refs[formName].validate((valid) => {
					if (valid) {
						this.$axios.put(this.$url + 'company/' + this.company_id, {
							data: this.ruleForm
							//company_id: this.company_id
						}).then(function(res) {
							if (res.data.status == 1) {
								self.$message({
									message: '编辑成功',
									type: 'success'
								});
								self.$router.push({
									path: "company",
									query: {
										companyid: res.data.companyid
									}
								});
							}
						})
						.catch(function (error) {
							self.$message({
								message: error.response.data.message,
								type: 'warning'
							});
						});
					} else {
						return false;
					}
				});
			},

			/**
			 * 重置表单
			 * @param {Object} formName
			 */
			resetForm(formName) {
				//this.$refs[formName].resetFields();
				//this.ruleForm.province_id='';//重置省
				this.$router.go(-1); //返回上一级
			},
		}
	}
</script>

<style>
	.create {
		padding: 20px 0 50px 0;
	}
</style>
