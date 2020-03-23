<template>
	<div class="user">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>设备合作者拥有设备</span></el-col>
					<el-col :span="6">
						<!-- 查询 s -->
						<el-form :inline="true" :model="formInline" size="mini" class="demo-form-inline">
							<el-form-item label="">
								<el-input placeholder="查询用户" v-model="formInline.user_name" clearable>
									<el-button slot="append" icon="el-icon-search" @click="getUserList()"></el-button>
								</el-input>
							</el-form-item>
						</el-form>
						<!-- 查询 e -->
					</el-col>
					<!-- <el-col :span="12"> -->
						<!-- 新增 s -->
						<!-- <router-link to="user_add"><el-button size="mini" icon="el-icon-plus">新增用户</el-button></router-link> -->
						<!-- 新增 e -->
					<!-- </el-col> -->
					<el-col :span="3" :offset="9">
						<el-button size="mini" icon="el-icon-back" title="返回" @click="back()">返回</el-button>
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- 用户列表 s -->
				<el-table :data="userList" border style="width: 100%">
					<el-table-column prop="user_id" label="序号" fixed width="90"></el-table-column>
					<el-table-column prop="user_name" label="用户名称" fixed min-width="180"></el-table-column>
					<el-table-column prop="avatar" label="头像" width="180">
						<template slot-scope="scope">
							<img :src="scope.row.avatar" :alt="scope.row.avatar" :title="scope.row.user_name" width="50" height="50" />
						</template>
					</el-table-column>
					<el-table-column prop="phone" label="电话号码" width="180">
						<template slot-scope="scope">
							{{scope.row.phone}}{{scope.row.phone_verified == 1 ? '(已验证)' : '(未验证)'}}
						</template>
					</el-table-column>
					<el-table-column prop="money" label="余额/元" min-width="120"></el-table-column>
					<el-table-column prop="income" label="收益/元" min-width="120"></el-table-column>
					<el-table-column prop="cash" label="提现/元" min-width="120"></el-table-column>
					<el-table-column prop="status" label="状态" width="90" :filters="[{ text: '禁用', value: 0 }, { text: '正常', value: 1 }]" :filter-method="filterStatus" filter-placement="bottom-end">
						<template slot-scope="scope">
							<el-tag :type="scope.row.status === 0 ? 'info' : (scope.row.status === 1 ? 'success' : 'danger')" size="mini">{{scope.row.status_msg}}</el-tag>
						</template>
					</el-table-column>
					<el-table-column prop="login_time" label="登录时间" width="180"></el-table-column>
					<el-table-column prop="login_ip" label="登录IP" width="180"></el-table-column>
					<el-table-column label="操作" fixed="right" min-width="160">
						<template slot-scope="scope">
							<el-button type="primary" size="mini" plain @click="toUserEdit(scope.row)">编辑</el-button>
							<el-button type="danger" size="mini" plain @click="deleteUser(scope)">删除</el-button>
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
					user_name: '' // 用户名称
				},
				userList: [], // 用户列表
				listPagination: {} // 列表分页参数
			}
		},
		mounted() {
			this.getUserList(); // 获取用户列表
		},
		methods: {
			/**
			 * 获取用户列表
			 */
			getUserList() {
				let self = this;
				this.$axios.get(this.$url + 'user_partner', {
						params: {
							user_name: this.formInline.user_name,
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
							// 用户列表分页参数
							self.listPagination = res.data.data;

							// 当数据为空时
							if (self.listPagination.total == 0) {
								self.$message({
									message: '数据不存在',
									type: 'warning'
								});
								return;
							}

							// 用户列表
							self.userList = self.listPagination.data;
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
				this.getUserList();
			},

			/**
			 * 分页 currentPage 改变时会触发
			 * @param {Object} current_page
			 */
			handleCurrentChange(current_page) {
				this.listPagination.current_page = current_page; // 当前页数
				this.getUserList();
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
			 * 跳转用户编辑页
			 * @param {Object} row
			 */
			toUserEdit(row) {
				this.$router.push({
					path: "user_partner_edit",
					query: {
						user_id: row.user_id
					}
				});
			},

			/**
			 * 删除用户
			 * @param {Object} scope
			 */
			deleteUser(scope) {
				this.$confirm('此操作将永久删除该用户, 是否继续?', '删除', {
					confirmButtonText: '确定',
					cancelButtonText: '取消',
					type: 'warning'
				}).then(() => {
					// 调用删除接口
					let self = this;
					this.$axios.delete(this.$url + 'user_partner/' + scope.row.user_id)
						.then(function(res) {
							// 移除元素
							self.userList.splice(scope.$index, 1);

							let type = res.data.status == 1 ? 'success' : 'warning';
							self.$message({
								message: res.data.message,
								type: type
							});
						})
						.catch(function(error) {
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
			},
			
			/**
			 * 返回上一页
			 */
			back(){
				this.$router.go(-1);
			},
		}
	}
</script>

<style>
</style>
