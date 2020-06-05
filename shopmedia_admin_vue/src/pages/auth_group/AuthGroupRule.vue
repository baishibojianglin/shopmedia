<template>
	<div class="auth_group_rule">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>配置角色权限规则</span></el-col>
					<el-col :span="3">
						<el-button size="mini" icon="el-icon-back" title="返回" @click="back()">返回</el-button>
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- Form 表单 s -->
				<el-form ref="ruleForm" :model="form" :rules="formRules" label-width="200px" size="small" class="demo-form-inline">
					<el-form-item prop="title" label="角色名称">
						<el-input v-model="form.title" clearable style="width:350px;" readonly></el-input>
					</el-form-item>
					<el-form-item prop="company_name" label="所属分公司">
						<el-input v-model="form.company_name" clearable style="width:350px;" readonly></el-input>
					</el-form-item>
					<el-form-item prop="rules" label="选择权限规则">
						<!-- Tree 树形控件（可选择层级） s -->
						<el-tree node-key="id" :props="props" :load="loadNode" default-expand-all :default-checked-keys="form.rules" lazy show-checkbox check-strictly @check="handleCheck" ref="tree"></el-tree>
						<!-- Tree 树形控件 e -->
					</el-form-item>
					<el-form-item>
						<el-button type="primary" plain @click="submitForm('ruleForm')">提交</el-button>
						<!-- <el-button plain @click="resetForm('ruleForm')">重置</el-button> -->
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
				props: {
					label: 'title_type',
					isLeaf: 'leaf' // 指定节点是否为叶子节点
				},

				parent_id: '', // 父级ID
				level: '', // 层级

				form: {
					id: '', // 角色ID
					title: '', // 角色名称
					rules: [] // 权限规则ID集合（数组）
				},
				formRules: { // 表单验证规则
					// rules: [{required: true, message: '请选择权限规则', trigger: 'change'}]
				}
			};
		},
		created() {
			this.getParams();
			this.getAuthGroup(); // 获取指定的角色信息
		},
		methods: {
			/**
			 * 获取路由带过来的参数
			 */
			getParams() {
				this.form.id = this.$route.query.id;
			},
			
			/**
			 * 获取指定的角色信息
			 */
			getAuthGroup() {
				let self = this;
				this.$axios.get(this.$url + 'auth_group/' + this.form.id, {
					headers: {
						'admin-user-id': JSON.parse(localStorage.getItem('admin_user')).user_id,
						'admin-user-token': JSON.parse(localStorage.getItem('admin_user')).token
					}
				})
				.then(function(res) {
					if (res.data.status == 1) {
						// 角色信息
						self.form = res.data.data;
						if (self.form.rules) {
							self.form.rules = self.form.rules.split(",");
							self.form.rules.forEach((item, index) =>{
								self.form.rules[index] = parseInt(self.form.rules[index]); // 将字符串数组转换成数字数组
							})
						} else {
							self.form.rules = [];
						}
						
						// console.log('auth_group：', self.form)
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
			 * 懒加载（权限规则） Tree 树形数据
			 * @param {Object} node
			 * @param {Object} resolve
			 */
			loadNode(node, resolve) {
				let self = this;
				if (node.data) {
					// console.log(123, node.data)
					this.parent_id = node.data.id;
					this.level = node.data.level + 1;
				} else {
					this.parent_id = 0;
					this.level = 1;
				}

				// 懒加载Auth权限规则树形列表
				this.$axios.get(this.$url + 'lazy_load_auth_rule_tree', {
					params: {
						parent_id: this.parent_id,
						level: this.level,
						group_id: this.form.id
					},
					headers: {
						'admin-user-id': JSON.parse(localStorage.getItem('admin_user')).user_id,
						'admin-user-token': JSON.parse(localStorage.getItem('admin_user')).token
					}
				}).then(function(res) {
					if (res.data.status == 1) {
						const data = res.data.data;
						data.forEach((value, index) => {
							// 当不存在子级时，指定节点为叶子节点
							if (value.children_count == 0) {
								value.leaf = true;
							}
						})

						/* if (node.level === 0) {
							return resolve(res.data.data);
						}
						if (node.level > 1) return resolve([]); */

						setTimeout(() => {
							resolve(data);
						}, 500);
					} else {
						self.$message({
							message: '网络忙，请重试',
							type: 'warning'
						});
					}
				})
				/* .catch(function(error) {
					self.$message({
						message: error.response.data.message,
						type: 'warning'
					});
				}); */
			},
			
			/**
			 * 当（权限规则）复选框被点击的时候触发
			 * @param {Object} data
			 * @param {Object} checkedObj
			 */
			handleCheck(data, checkedObj) {
				// 获取权限规则ID集合（含全选与半选）
				let checkedRules = this.$refs.tree.getCheckedKeys(); // 被选中的节点的 key 所组成的数组
				this.form.rules = checkedRules.length != 0 ? checkedRules : []; // 判断全选是否为空 checkedRules.length ?= 0，用于验证 Tree 树形在表单中的选中状态
			},

			/**
			 * 编辑角色提交表单
			 * @param {Object} formName
			 */
			submitForm(formName) {
				let self = this;
				this.$refs[formName].validate((valid) => {
					if (valid) {
						this.$axios.put(this.$url + 'config_auth_group_rule/' + this.form.id, {
								// 参数
								rules: this.form.rules
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
							.catch(function(error) {
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
			back() {
				this.$router.go(-1);
			}
		}
	};
</script>

<style>
</style>
