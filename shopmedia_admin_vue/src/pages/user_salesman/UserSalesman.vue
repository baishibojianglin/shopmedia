<template>
	<div class="user">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="3"><span>业务员</span></el-col>
				</el-row>
				<el-row :gutter="20" type="flex" justify="space-between" style="margin-top: 1rem;">
					<!-- <el-col :span="3"><span>业务员</span></el-col> -->
					<el-col :span="18">
						<!-- 查询 s -->
						<el-form ref="formInline" :inline="true" :model="formInline" size="mini" class="demo-form-inline">
							<el-form-item label="">
								<el-select v-model="formInline.role_id" multiple placeholder="选择角色" filterable style="margin-right: 25px;">
									<el-option
										v-for="item in userRoleList"
										:key="item.id"
										:label="item.title"
										:value="item.id">
									</el-option>
								</el-select>
							</el-form-item>
							<el-form-item label="">
								<el-select v-model="formInline.status" clearable placeholder="状态">
									<el-option v-for="(item, index) in {0: '禁用', 1: '启用', 2: '待审核', 3: '驳回'}" :key="index" :label="item" :value="Number(index)"></el-option>
								</el-select>
							</el-form-item>
							<el-form-item label="">
								<el-input placeholder="上级名称" v-model="formInline.parent_name" clearable>
								</el-input>
							</el-form-item>
							<el-form-item label="">
								<el-input placeholder="用户名称" v-model="formInline.user_name" clearable>
									<!-- <el-button slot="append" icon="el-icon-search" @click="getUserList()">查询</el-button> -->
									<!-- <el-button slot="append" icon="el-icon-refresh-left" @click="resetForm('formInline')">重置</el-button> -->
								</el-input>
							</el-form-item>
							<el-form-item>
								<el-button type="primary" icon="el-icon-search" @click="getUserList()">查询</el-button>
								<el-button icon="el-icon-refresh-left" @click="resetForm('formInline')">重置</el-button>
							</el-form-item>
						</el-form>
						<!-- 查询 e -->
					</el-col>
					<el-col :span="6">
						<!-- 新增 s -->
						<router-link to="user_salesman_create"><el-button size="mini" type="primary" icon="el-icon-plus">新增业务员</el-button></router-link>
						<!-- 新增 e -->
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- 用户列表 s -->
				<el-table :data="userList" border empty-text="数据加载中…" max-height="600" :span-method="objectSpanMethod" style="width: 100%">
					<!-- <el-table-column prop="id" label="序号" fixed width="90"></el-table-column> -->
					<el-table-column prop="user_id" label="用户序号" fixed width="90"></el-table-column>
					<el-table-column prop="user_name" label="用户名称" fixed min-width="180"></el-table-column>
					<el-table-column prop="title" label="业务员角色" fixed min-width="120"></el-table-column>
					<!-- <el-table-column prop="avatar" label="头像" width="90">
						<template slot-scope="scope">
							<img :src="scope.row.avatar" :alt="scope.row.avatar" :title="scope.row.user_name" width="50" height="50" />
						</template>
					</el-table-column> -->
					<el-table-column prop="phone" label="电话号码" width="120">
						<template slot-scope="scope">
							{{scope.row.phone}}<!-- {{scope.row.phone ? (scope.row.phone_verified == 1 ? '' : '(未验证)') : ''}} -->
						</template>
					</el-table-column>
					<el-table-column prop="company_name" label="分公司" min-width="180"></el-table-column>
					<!-- <el-table-column prop="parent_id" label="上级序号" min-width="90"></el-table-column> -->
					<el-table-column prop="parent_name" label="上级名称" min-width="180"></el-table-column>
					<el-table-column prop="comm_ratio" label="业务员提成比例" min-width="120"></el-table-column>
					<el-table-column prop="parent_comm_ratio" label="向上级提成比例" min-width="120"></el-table-column>
					<el-table-column prop="auth_son_ratio_msg" label="授权配置下级提成比例" min-width="120">
						<template slot-scope="scope">
							<span :class="scope.row.auth_son_ratio === 1 ? 'text-success' : 'text-info'">{{scope.row.auth_son_ratio_msg}}</span>
						</template>
					</el-table-column>
					<el-table-column prop="auth_open_user_msg" label="授权开通目标客户" min-width="120">
						<template slot-scope="scope">
							<span :class="scope.row.auth_open_user === 1 ? 'text-success' : 'text-info'">{{scope.row.auth_open_user_msg}}</span>
						</template>
					</el-table-column>
					<el-table-column prop="invitation_code" label="目标客户邀请码" min-width="90"></el-table-column>
					<el-table-column prop="son_invitation_code" label="下级业务员邀请码" min-width="90"></el-table-column>
					<el-table-column prop="money" label="余额/元" min-width="120"></el-table-column>
					<el-table-column prop="income" label="收益/元" min-width="120"></el-table-column>
					<el-table-column prop="cash" label="提现/元" min-width="120"></el-table-column>
					<el-table-column prop="status" label="状态" width="90">
					<!-- <el-table-column prop="status" label="状态" width="90" :filters="[{ text: '禁用', value: 0 }, { text: '启用', value: 1 }, { text: '待审核', value: 2 }, { text: '驳回', value: 3 }]" :filter-method="filterStatus" filter-placement="bottom-end"> --><!-- 替换上面 prop="status" 可用于筛选状态 -->
						<template slot-scope="scope">
							<span v-for="(item, index) in {0: 'text-info', 1: 'text-success', 2: 'text-warning', 3: 'text-danger'}" :key="index" v-if="scope.row.status == index" :class="item">{{scope.row.status_msg}}</span>
						</template>
					</el-table-column>
					<el-table-column prop="login_time" label="登录时间" width="180"></el-table-column>
					<el-table-column prop="login_ip" label="登录IP" width="130"></el-table-column>
					<el-table-column label="操作" fixed="right" min-width="210">
						<template slot-scope="scope">
							<el-button v-if="scope.row.role_id == 6" type="infor" size="mini" plain @click="toShopList(scope.row)">店铺</el-button>
							<el-button type="infor" size="mini" plain @click="toSonUser(scope.row)">下级</el-button>
							<el-button type="primary" size="mini" plain @click="toUserEdit(scope.row)">编辑</el-button>
							<!-- <el-button type="danger" size="mini" plain @click="deleteUser(scope)">删除</el-button> -->
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
					user_name: '', // 用户名称
					parent_name: '', // 上级用户名称
					role_id: '', // 角色ID
					status: '' // 角色状态
				},
				userList: [], // 用户列表
				listPagination: {}, // 列表分页参数
				
				// 合并行的相关参数
				spanArr: [], // 用于存放每一行记录的合并数
				position: 0, // spanArr的索引
				
				userRoleList: [] // 用户角色列表
			}
		},
		mounted() {
			this.getUserList(); // 获取用户列表
			this.getUserRoleList();
		},
		methods: {
			/**
			 * 获取用户列表
			 */
			getUserList() {
				let self = this;
				this.$axios.get(this.$url + 'user_salesman', {
					params: {
						user_name: this.formInline.user_name,
						parent_name: this.formInline.parent_name,
						role_id: this.formInline.role_id,
						status: this.formInline.status,
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
						// 获取每一行合并的行数
						self.getSpanArr(self.userList);
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
			 * 获取每一行合并的行数
			 * @param {Object} data
			 */
			getSpanArr(data) {
				this.spanArr = []; // 避免表格错乱！
				data.forEach((item, index) => {
					if (index === 0) {
						this.spanArr.push(1);
						this.position = 0;
					} else {
						// 判断当前元素与上一个元素是否相同
						if (data[index].user_id === data[index - 1].user_id) { // 这里是要合并行
							this.spanArr[this.position] += 1;
							this.spanArr.push(0); // ０即表示该行不显示
						} else {
							this.spanArr.push(1);
							this.position = index;
						}
					}
				})
			},
			
			/**
			 * 合并行
			 */
			objectSpanMethod({ row, column, rowIndex, columnIndex }) {
				let columnIndexArray = [0, 1, 3, 4, 16, 17]; // 列号组成的数组
				if (columnIndexArray.includes(columnIndex) === true) { // 即 columnIndex === 0 || columnIndex === 1 …
					const _row = this.spanArr[rowIndex];
					const _col = _row > 0 ? 1 : 0;
					return {
						rowspan: _row,
						colspan: _col
					}
				}
			},
			
			/**
			 * 重置表单
			 * @param {Object} formName
			 */
			resetForm(formName) {
				// this.$refs[formName].resetFields();
				this.formInline = {};
				this.getUserList();
			},

			/**
			 * 跳转用户编辑页
			 * @param {Object} row
			 */
			toUserEdit(row) {
				this.$router.push({
					path: "user_salesman_edit",
					query: {
						id: row.id
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
					this.$axios.delete(this.$url + 'user_salesman/' + scope.row.id)
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
			 * 跳转下级用户（业务员）列表页
			 * @param {Object} row
			 */
			toSonUser(row) {
				this.$router.push({
					path: "son_salesman",
					query: {
						id: row.id,
						role_id: row.role_id
					}
				});
			},
			
			/**
			 * 获取用户角色（业务员）列表
			 */
			getUserRoleList() {
				let self = this;
				this.$axios.get(this.$url + 'user_role_list', {
					params: {
						parent_id: 1
					}/* ,
					headers: {
						'admin-user-id': JSON.parse(localStorage.getItem('admin_user')).id,
						'admin-user-token': JSON.parse(localStorage.getItem('admin_user')).token
					} */
				})
				.then(function(res) {
					if (res.data.status == 1) {
						// 用户角色列表
						self.userRoleList = res.data.data;
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
			 * 跳转店铺列表页
			 * @param {Object} row
			 */
			toShopList(row) {
				this.$router.push({
					path: "shop",
					query: {
						role_id: row.role_id,
						salesman_id: row.id,
						user_name: row.user_name
					}
				});
			}
		}
	}
</script>

<style>
	.el-select .el-input {
		width: 100px;
	}
</style>
