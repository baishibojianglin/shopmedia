<template>
	<div class="admin">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>管理员</span></el-col>
					<el-col :span="6">
						<!-- 查询 s -->
						<el-form :inline="true" :model="formInline" size="mini" class="demo-form-inline">
							<el-form-item label="">
								<el-input placeholder="管理员账号" v-model="formInline.account" clearable>
									<el-button slot="append" icon="el-icon-search" @click="getAdminList()">查询</el-button>
								</el-input>
							</el-form-item>
						</el-form>
						<!-- 查询 e -->
					</el-col>
					<el-col :span="12">
						<!-- 新增 s -->
						<router-link to="admin_create"><el-button size="mini" type="primary" icon="el-icon-plus">新增管理员</el-button></router-link>
						<!-- 新增 e -->
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- 管理员列表 s -->
				<el-table :data="adminList" max-height="500" border empty-text="数据加载中…" style="width: 100%">
					<el-table-column prop="id" label="序号" fixed width="90"></el-table-column>
					<el-table-column prop="account" label="管理员账号" fixed min-width="120"></el-table-column>
					<el-table-column prop="parent_id" label="上级序号" width="90">
						<template slot-scope="scope">
							{{scope.row.parent_id == 0 ? '（无）' : scope.row.parent_id}}
						</template>
					</el-table-column>
					<el-table-column prop="parent_account" label="上级账号" width="120">
						<template slot-scope="scope">
							{{scope.row.parent_id == 0 ? '（无）' : scope.row.parent_account}}
						</template>
					</el-table-column>
					<el-table-column prop="company_name" label="分公司名称" width="120"></el-table-column>
					<el-table-column prop="auth_group_title" label="角色" width="120"></el-table-column>
					<el-table-column prop="status" label="状态" width="90" :filters="[{ text: '禁用', value: 0 }, { text: '启用', value: 1 }]" :filter-method="filterStatus" filter-placement="bottom-end">
						<template slot-scope="scope">
							<span :class="scope.row.status === 1 ? 'text-success' : 'text-info'">{{scope.row.status_msg}}</span>
						</template>
					</el-table-column>
					<el-table-column prop="login_time" label="登录时间" width="180"></el-table-column>
					<el-table-column prop="login_ip" label="登录IP" width="130"></el-table-column>
					<el-table-column label="操作" fixed="right" min-width="160">
						<template slot-scope="scope">
							<el-button type="primary" size="mini" plain @click="toAdminEdit(scope.row)">编辑</el-button>
							<el-button type="danger" size="mini" plain @click="deleteAdmin(scope)">删除</el-button>
						</template>
					</el-table-column>
				</el-table>
				<!-- 管理员列表 e -->
				
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
					account: '' // 管理员账号
				},
				adminList: [], // 管理员列表
				listPagination: {} // 列表分页参数
			}
		},
		mounted() {
			this.getAdminList(); // 获取管理员列表
		},
		methods: {
			/**
			 * 获取管理员列表
			 */
			getAdminList() {
				let self = this;
				this.$axios.get(this.$url + 'admin', {
					params: {
						account: this.formInline.account,
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
						// 管理员列表分页参数
						self.listPagination = res.data.data;
						
						// 当数据为空时
						if (self.listPagination.total == 0) {
							self.$message({
								message: '数据不存在',
								type: 'warning'
							});
							return;
						}
						
						// 管理员列表
						self.adminList = self.listPagination.data;
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
				this.getAdminList();
			},
			
			/**
			 * 分页 currentPage 改变时会触发
			 * @param {Object} current_page
			 */
			handleCurrentChange(current_page) {
				this.listPagination.current_page = current_page; // 当前页数
				this.getAdminList();
			},
			
			/**
			 * 筛选管理员状态
			 * @param {Object} value
			 * @param {Object} row
			 */
			filterStatus(value, row) {
				return row.status === value;
			},
			
			/**
			 * 跳转管理员编辑页
			 * @param {Object} row
			 */
			toAdminEdit(row) {
				this.$router.push({path: "admin_edit", query: {id: row.id}});
			},
			
			/**
			 * 删除管理员
			 * @param {Object} scope
			 */
			deleteAdmin(scope) {
				this.$confirm('此操作将永久删除该管理员, 是否继续?', '删除', {
					confirmButtonText: '确定',
					cancelButtonText: '取消',
					type: 'warning'
				}).then(() => {
					// 调用删除接口
					let self = this;
					this.$axios.delete(this.$url + 'admin/' + scope.row.id)
					.then(function(res) {
						// 移除元素
						self.adminList.splice(scope.$index, 1);
						
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
