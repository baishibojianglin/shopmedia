<template>
	<div class="auth_rule">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>权限规则</span></el-col>
					<el-col :span="6">
						<!-- 查询 s -->
						<el-form :inline="true" :model="formInline" size="mini" class="demo-form-inline">
							<el-form-item label="">
								<el-input placeholder="规则名称" v-model="formInline.title" clearable>
									<el-button slot="append" icon="el-icon-search" @click="getAuthRuleList()">查询</el-button>
								</el-input>
							</el-form-item>
						</el-form>
						<!-- 查询 e -->
					</el-col>
					<el-col :span="12">
						<!-- 新增 s -->
						<router-link to="auth_rule_create"><el-button size="mini" type="primary" icon="el-icon-plus">新增权限规则</el-button></router-link>
						<!-- 新增 e -->
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- 权限规则列表 s -->
				<el-table :data="authRuleList" max-height="500" border empty-text="数据加载中…" style="width: 100%">
					<el-table-column prop="id" type="" label="序号" fixed width="90"></el-table-column>
					<el-table-column prop="title" label="权限规则名称" fixed min-width="180"></el-table-column>
					<el-table-column prop="name" label="权限规则" min-width="180"></el-table-column>
					<el-table-column prop="type" label="权限规则类型" min-width="120"></el-table-column>
					<el-table-column prop="pid" label="上级序号" width="90"></el-table-column>
					<el-table-column prop="level" label="级别" width="90"></el-table-column>
					<el-table-column prop="icon" label="图标" width="70">
						<template slot-scope="scope"><i :class="scope.row.icon" :title="scope.row.icon"></i></template>
					</el-table-column>
					<el-table-column prop="status" label="状态" width="90" :filters="[{ text: '禁用', value: 0 }, { text: '正常', value: 1 }]" :filter-method="filterStatus" filter-placement="bottom-end">
						<template slot-scope="scope">
							<!-- <el-tag :type="scope.row.status === 1 ? 'success' : 'info'" size="mini">{{scope.row.status_msg}}</el-tag> -->
							<span :class="scope.row.status === 1 ? 'text-success' : 'text-info'">{{scope.row.status_msg}}</span>
						</template>
					</el-table-column>
					<el-table-column prop="sort" label="同级排序" width="90"></el-table-column>
					<el-table-column label="操作" fixed="right" min-width="160">
						<template slot-scope="scope">
							<el-button type="primary" size="mini" plain @click="toAuthRuleEdit(scope.row)">编辑</el-button>
							<el-button type="danger" size="mini" plain @click="deleteAuthRule(scope)">删除</el-button>
						</template>
					</el-table-column>
				</el-table>
				<!-- 权限规则列表 e -->
				
				<!-- 分页 s -->
				<div hidden="hidden">
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
					title: '' // 权限规则名称
				},
				authRuleList: [], // 权限规则列表
				listPagination: {} // 列表分页参数
			}
		},
		mounted() {
			this.getAuthRuleList(); // 获取权限规则列表
		},
		methods: {
			/**
			 * 获取权限规则列表
			 */
			getAuthRuleList() {
				let self = this;
				this.$axios.get(this.$url + 'auth_rule', {
					params: {
						title: this.formInline.title,
						// page: this.listPagination.current_page,
						// size: this.listPagination.per_page
					}/* ,
					headers: {
						'admin-user-id': JSON.parse(localStorage.getItem('admin_user')).id,
						'admin-user-token': JSON.parse(localStorage.getItem('admin_user')).token
					} */
				})
				.then(function(res) {
					if (res.data.status == 1) {
						// 权限规则列表分页参数
						// self.listPagination = res.data.data;
						
						// 当数据为空时
						/* if (self.listPagination.total == 0) {
							self.$message({
								message: '数据不存在',
								type: 'warning'
							});
							return;
						} */
						
						// 权限规则列表
						self.authRuleList = res.data.data;
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
				this.getAuthRuleList();
			},
			
			/**
			 * 分页 currentPage 改变时会触发
			 * @param {Object} current_page
			 */
			handleCurrentChange(current_page) {
				this.listPagination.current_page = current_page; // 当前页数
				this.getAuthRuleList();
			},
			
			/**
			 * 筛选权限规则状态
			 * @param {Object} value
			 * @param {Object} row
			 */
			filterStatus(value, row) {
				return row.status === value;
			},
			
			/**
			 * 跳转权限规则编辑页
			 * @param {Object} row
			 */
			toAuthRuleEdit(row) {
				this.$router.push({path: "auth_rule_edit", query: {id: row.id}});/* , title: row.title, status: row.status, pid: row.pid */
			},
			
			/**
			 * 删除权限规则
			 * @param {Object} scope
			 */
			deleteAuthRule(scope) {
				this.$confirm('此操作将永久删除该权限规则, 是否继续?', '删除', {
					confirmButtonText: '确定',
					cancelButtonText: '取消',
					type: 'warning'
				}).then(() => {
					// 调用删除接口
					let self = this;
					this.$axios.delete(this.$url + 'auth_rule/' + scope.row.id)
					.then(function(res) {
						// 移除元素
						self.authRuleList.splice(scope.$index, 1);
						
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
