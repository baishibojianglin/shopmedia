<template>
	<div class="auth_group">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>角色管理</span></el-col>
					<el-col :span="12">
						<!-- 查询 s -->
						<el-form ref="formInline" :inline="true" :model="formInline" size="mini" class="demo-form-inline">
							<el-form-item label="">
								<el-input placeholder="角色名称" v-model="formInline.title" clearable>
									<el-button slot="append" icon="el-icon-search" @click="getAuthGroupList()">查询</el-button>
									<el-button slot="append" icon="el-icon-refresh-left" @click="resetForm('formInline')">重置</el-button>
									<el-button icon="el-icon-refresh-left" @click="resetForm('formInline')">重置</el-button>
								</el-input>
								
							</el-form-item>
						</el-form>
						<!-- 查询 e -->
					</el-col>
					<el-col :span="6">
						<!-- 新增 s -->
						<router-link to="auth_group_create"><el-button size="mini" type="primary" icon="el-icon-plus">新增角色</el-button></router-link>
						<!-- 新增 e -->
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- 角色列表 s -->
				<el-table :data="authGroupList" max-height="500" border empty-text="数据加载中…" style="width: 100%">
					<el-table-column prop="id" type="" label="序号" fixed width="90"></el-table-column>
					<el-table-column prop="title" label="角色名称" fixed min-width="180"></el-table-column>
					<el-table-column prop="parent_id" type="" label="上级序号" width="90">
						<template slot-scope="scope">
							{{scope.row.parent_id == 0 ? '-' : scope.row.parent_id}}
						</template>
					</el-table-column>
					<el-table-column prop="parent_title" label="上级角色" width="180">
						<template slot-scope="scope">
							{{scope.row.parent_id == 0 ? '-' : scope.row.parent_title}}
						</template>
					</el-table-column>
					<el-table-column prop="company_name" label="所属分公司" width="180">
						<template slot-scope="scope">
							{{scope.row.company_id == 0 ? '-' : scope.row.company_name}}
						</template>
					</el-table-column>
					<el-table-column prop="status" label="状态" width="90" :filters="[{ text: '禁用', value: 0 }, { text: '启用', value: 1 }]" :filter-method="filterStatus" filter-placement="bottom-end">
						<template slot-scope="scope">
							<!-- <el-tag :type="scope.row.status === 1 ? 'success' : 'info'" size="mini">{{scope.row.status_msg}}</el-tag> -->
							<span :class="scope.row.status === 1 ? 'text-success' : 'text-info'">{{scope.row.status_msg}}</span>
						</template>
					</el-table-column>
					<el-table-column prop="auth_rules" label="授权配置下级权限" width="150">
						<template slot-scope="scope">
							<span :class="scope.row.auth_rules === 1 ? 'text-success' : 'text-info'">{{scope.row.auth_rules == 1 ? "允许" : "禁止"}}</span>
						</template>
					</el-table-column>
					<el-table-column label="操作" fixed="right" min-width="240">
						<template slot-scope="scope">
							<el-button type="primary" size="mini" plain @click="toAuthGroupRule(scope.row)">配置权限</el-button>
							<el-button type="primary" size="mini" plain @click="toAuthGroupEdit(scope.row)">编辑</el-button>
							<el-button type="danger" size="mini" plain @click="deleteAuthGroup(scope)">删除</el-button>
						</template>
					</el-table-column>
				</el-table>
				<!-- 角色列表 e -->
				
				<!-- 分页 s -->
				<div>
					<el-pagination
						background
						:page-sizes="[5, 10, 15, 20]"
						:page-size="listPagination.per_page"
						:total="listPagination.total"
						:current-page="listPagination.current_page"
						layout="total, sizes, prev, pager, next, jumper"
						@size-change="handleSizeChange"
						@current-change="handleCurrentChange">
					</el-pagination>
				</div>
				<!-- 分页 e -->
			</div>
		</el-card>
	</div>
</template>

<script>
	export default {
		data() {
			return {
				formInline: {
					title: '' // 角色名称
				},
				authGroupList: [], // 角色列表
				listPagination: {} // 列表分页参数
			}
		},
		mounted() {
			this.getAuthGroupList(); // 获取角色列表
		},
		methods: {
			/**
			 * 获取角色列表
			 */
			getAuthGroupList() {
				let self = this;
				this.$axios.get(this.$url + 'auth_group', {
					params: {
						title: this.formInline.title,
						page: this.listPagination.current_page,
						size: this.listPagination.per_page
					}/* ,
					headers: {
						'admin-user-id': JSON.parse(localStorage.getItem('admin_user')).id,
						'admin-user-token': JSON.parse(localStorage.getItem('admin_user')).token
					} */
				})
				.then(function(res) {
					if (res.data.status == 1) {
						// 角色列表分页参数
						self.listPagination = res.data.data;
						
						// 当数据为空时
						if (self.listPagination.total == 0) {
							self.$message({
								message: '数据不存在',
								type: 'warning'
							});
							return;
						}
						
						// 角色列表
						self.authGroupList = self.listPagination.data;
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
			 * 分页 pageSize 改变时会触发
			 * @param {Object} page_size
			 */
			handleSizeChange(page_size) {
				this.listPagination.per_page = page_size; // 每页条数
				this.getAuthGroupList();
			},
			
			/**
			 * 分页 currentPage 改变时会触发
			 * @param {Object} current_page
			 */
			handleCurrentChange(current_page) {
				this.listPagination.current_page = current_page; // 当前页数
				this.getAuthGroupList();
			},
			
			/**
			 * 筛选角色状态
			 * @param {Object} value
			 * @param {Object} row
			 */
			filterStatus(value, row) {
				return row.status === value;
			},
			
			/**
			 * 重置表单
			 * @param {Object} formName
			 */
			resetForm(formName) {
				// this.$refs[formName].resetFields();
				this.formInline = {};
				this.getAuthGroupList();
			},
			
			/**
			 * 跳转角色权限配置页
			 * @param {Object} row
			 */
			toAuthGroupRule(row) {
				this.$router.push({path: "auth_group_rule", query: {id: row.id}});/* , title: row.title, status: row.status, parent_id: row.parent_id */
			},
			
			/**
			 * 跳转角色编辑页
			 * @param {Object} row
			 */
			toAuthGroupEdit(row) {
				this.$router.push({path: "auth_group_edit", query: {id: row.id}});/* , title: row.title, status: row.status, parent_id: row.parent_id */
			},
			
			/**
			 * 删除角色
			 * @param {Object} scope
			 */
			deleteAuthGroup(scope) {
				this.$confirm('此操作将永久删除该角色, 是否继续?', '删除', {
					confirmButtonText: '确定',
					cancelButtonText: '取消',
					type: 'warning'
				}).then(() => {
					// 调用删除接口
					let self = this;
					this.$axios.delete(this.$url + 'auth_group/' + scope.row.id)
					.then(function(res) {
						// 移除元素
						self.authGroupList.splice(scope.$index, 1);
						
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
				}).catch(() => {
					this.$message({
						type: 'info',
						message: '已取消删除'
					});
				});
			}
		}
	}
</script>

<style>
</style>
