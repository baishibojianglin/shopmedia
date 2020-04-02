<template>
	<div class="region">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20">
					<el-col :span="6"><span>{{title}}</span></el-col>
					<el-col :span="6">
						<!-- 查询 s -->
						<el-form :inline="true" :model="formInline" size="mini" class="demo-form-inline">
							<el-form-item label="">
								<el-input :placeholder="'查询' + title" v-model="formInline.region_name" clearable>
									<el-button slot="append" icon="el-icon-search" @click="getRegionList"></el-button>
								</el-input>
							</el-form-item>
						</el-form>
						<!-- 查询 e -->
					</el-col>
					<el-col :span="6">
						<!-- 新增 s -->
						<el-form :inline="true" :model="formAddRegion" :rules="rules" size="mini" ref="ruleForm" class="demo-form-inline">
							<el-form-item label="" prop="region_name">
								<el-input :placeholder="'新增'+ title" v-model="formAddRegion.region_name" clearable>
									<el-button slot="append" icon="el-icon-plus" @click="addRegion('ruleForm')"></el-button>
								</el-input>
							</el-form-item>
						</el-form>
						<!-- 新增 e -->
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- 区域列表 s -->
				<el-row :gutter="10">
					<el-col :xs="12" :sm="8" :md="6" :lg="4" :xl="3" v-for="(item, index) in regionList" :key="index" style="margin-bottom: 1rem;">
						<el-card>
							<span>{{item.region_name}}</span>
							<div style="margin-top: 1rem;">
								<el-button type="primary" size="mini" plain icon="el-icon-edit" @click="toDetail(item.region_id, item.region_name)">管理</el-button>
								<el-popconfirm iconColor="red" title="确定删除该区域？" style="margin-left: 0.5rem;" @onConfirm="deleteRegion(item.region_id, index)">
									<el-button type="danger" size="mini" plain icon="el-icon-delete" slot="reference" title="删除"><!-- 删除 --></el-button>
								</el-popconfirm>
							</div>
						</el-card>
					</el-col>
				</el-row>
				<!-- 区域列表 e -->
			</div>
		</el-card>
	</div>
</template>

<script>
	export default {
		data() {
			return {
				title: '省级区域',
				formInline: {
					region_name: '' // 区域名称
				},
				regionList: [], // 区域列表，如 [{region_id: 1, region_name: '北京市', level: 1, parent_id: 0}, {…}, …]
				formAddRegion: {
					region_name: '',
					level: 1, // 区域级别
					parent_id: 0 // 上级ID
				},
				rules: { // 验证规则
					region_name: [
						{ required: true, message: '请输入区域名称', trigger: 'blur' }
					],
				}
			}
		},
		mounted() { // 实例被挂载后调用
			this.getRegionList(); // 获取区域列表
		},
		methods: {
			/**
			 * 获取区域列表
			 */
			getRegionList() {
				let self = this;
				this.$axios.get(this.$url + 'region', {
					params: {
						region_name: this.formInline.region_name
					}/* ,
					headers: {
						'admin-user-id': JSON.parse(localStorage.getItem('admin_user')).id,
						'admin-user-token': JSON.parse(localStorage.getItem('admin_user')).token
					} */
				})
				.then(function(res) {
					if (res.data.status == 1) {
						self.regionList = res.data.data;
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
			 * 新增区域
			 */
			addRegion(formName) {
				let self = this;
				this.$refs[formName].validate((valid) => {
					if (valid) {
						this.$axios.post(this.$url + 'region', {
							region_name: this.formAddRegion.region_name,
							level: this.formAddRegion.level,
							parent_id: this.formAddRegion.parent_id
						})
						.then(function(res) {
							// 新增元素
							self.regionList.push({
								region_id: res.data.data.region_id,
								region_name: self.formAddRegion.region_name,
								level: self.formAddRegion.level,
								parent_id: self.formAddRegion.parent_id
							});
							
							let type = res.data.status == 1 ? 'success' : 'warning';
							self.$message({
								message: res.data.message,
								type: type
							});
						})
						.catch(function (error) {
							self.$message({
								message: error.response.data.message,
								type: 'warning'
							});
						});
					} else {
						console.log('error submit!!');
						return false;
					}
					// this.formAddRegion.region_name = ''; // 初始化新增输入框的区域名称
				});
			},
			
			/**
			 * 跳转区域管理
			 * @param {Object} parent_id
			 * @param {Object} parent_name
			 */
			toDetail(parent_id, parent_name) {
				this.$router.push({path: "regioncity", query: {parent_id: parent_id, parent_name: parent_name}});
			},
			
			/**
			 * 删除区域
			 * @param {Object} region_id
			 */
			deleteRegion(region_id, index) {
				let self = this;
				this.$axios.delete(this.$url + 'region/' + region_id)
				.then(function(res) {
					// 移除元素
					self.regionList.splice(index, 1);
					
					let type = res.data.status == 1 ? 'success' : 'warning';
					self.$message({
						message: res.data.message,
						type: type
					});
				})
				.catch(function (error) {
					self.$message({
						message: error.response.data.message,
						type: 'warning'
					});
				});
			}
		}
	}
</script>

<style>
</style>
