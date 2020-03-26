<template>
	<div class="user_type">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>用户类型</span></el-col>
					<el-col :span="6">
						<!-- 查询 s -->
						<el-form :inline="true" :model="formInline" size="mini" class="demo-form-inline">
							<el-form-item label="">
								<el-input placeholder="用户类型名称" v-model="formInline.type_name" clearable>
									<el-button slot="append" icon="el-icon-search" @click="getUserTypeList()">查询</el-button>
								</el-input>
							</el-form-item>
						</el-form>
						<!-- 查询 e -->
					</el-col>
					<el-col :span="6" :offset="6">
						<!-- 新增 s -->
						<!-- <router-link to="user_add"><el-button size="mini" icon="el-icon-plus">新增用户</el-button></router-link> -->
						<!-- 新增 e -->
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- 用户列表 s -->
				<el-table :data="userTypeList" border style="width: 100%">
					<el-table-column prop="type_id" label="序号" fixed width="90"></el-table-column>
					<el-table-column prop="type_name" label="类型名称" fixed min-width="180"></el-table-column>
					<el-table-column prop="parent_id" label="上级类型序号" width="120"></el-table-column>
					<el-table-column prop="parent_comm_ratio" label="上级用户统一提成比例" width="180"></el-table-column>
					<el-table-column prop="status" label="状态" width="90" :filters="[{ text: '禁用', value: 0 }, { text: '启用', value: 1 }]" :filter-method="filterStatus" filter-placement="bottom-end">
						<template slot-scope="scope">
							<span :class="scope.row.status === 1 ? 'text-success' : 'text-info'" size="mini">{{scope.row.status_msg}}</span>
						</template>
					</el-table-column>
					<el-table-column label="操作" fixed="right" width="90">
						<template slot-scope="scope">
							<el-button type="primary" size="mini" plain @click="toUserTypeEdit(scope.row)">编辑</el-button>
						</template>
					</el-table-column>
				</el-table>
				<!-- 用户列表 e -->

				<!-- 分页 s -->
				<div>
					<el-pagination background :page-sizes="[5, 10, 15, 20]" :page-size="listPagination.per_page" :total="listPagination.total"
					 :current-page="listPagination.current_page" layout="total, sizes, prev, pager, next, jumper" @size-change="handleSizeChange"
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
					type_name: '' // 用户类型名称
				},
				userTypeList: [], // 用户类型列表
				listPagination: {} // 列表分页参数
			}
		},
		mounted() {
			this.getUserTypeList(); // 获取用户列表
		},
		methods: {
			/**
			 * 获取用户列表
			 */
			getUserTypeList() {
				let self = this;
				this.$axios.get(this.$url + 'user_type', {
						params: {
							type_name: this.formInline.type_name,
							page: this.listPagination.current_page,
							size: this.listPagination.per_page
						}/* ,
						headers: {
							'admin-user-id': JSON.parse(localStorage.getItem('company')).user_id,
							'admin-user-token': JSON.parse(localStorage.getItem('company')).token
						} */
					})
					.then(function(res) {
						if (res.data.status == 1) {
							// 用户类型列表分页参数
							self.listPagination = res.data.data;

							// 当数据为空时
							if (self.listPagination.total == 0) {
								self.$message({
									message: '数据不存在',
									type: 'warning'
								});
								return;
							}

							// 用户类型列表
							self.userTypeList = self.listPagination.data;
						} else {
							self.$message({
								message: '网络忙，请重试',
								type: 'warning'
							});
						}
					})
					.catch(function(error) {
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
				this.getUserTypeList();
			},

			/**
			 * 分页 currentPage 改变时会触发
			 * @param {Object} current_page
			 */
			handleCurrentChange(current_page) {
				this.listPagination.current_page = current_page; // 当前页数
				this.getUserTypeList();
			},

			/**
			 * 筛选用户状态
			 * @param {Object} value
			 * @param {Object} row
			 */
			filterStatus(value, row) {
				return row.status === value;
			},

			/**
			 * 跳转用户类型编辑页
			 * @param {Object} row
			 */
			toUserTypeEdit(row) {
				this.$router.push({
					path: "user_type_edit",
					query: {
						type_id: row.type_id
					}
				});
			}
		}
	}
</script>

<style>
</style>
