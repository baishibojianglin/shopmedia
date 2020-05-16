<template>
	<div class="user">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="3"><span>下级业务员</span></el-col>
					<el-col :span="3">
						<el-button size="mini" icon="el-icon-back" title="返回" @click="back()">返回</el-button>
					</el-col>
				</el-row>
				<el-row :gutter="20" type="flex" justify="space-between" style="margin-top: 1rem;">
					<!-- <el-col :span="3"><span>业务员</span></el-col> -->
					<el-col :span="18">
						<!-- 查询 s -->
						<el-form ref="formInline" :inline="true" :model="formInline" size="mini" class="demo-form-inline">
							<el-form-item label="">
								<el-select v-model="formInline.status" clearable placeholder="状态">
									<el-option v-for="(item, index) in {0: '禁用', 1: '启用', 2: '待审核', 3: '驳回'}" :key="index" :label="item" :value="Number(index)"></el-option>
								</el-select>
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
				</el-row>
			</div>
			<div class="">
				<!-- 用户列表 s -->
				<el-table :data="userList" border empty-text="数据加载中…" max-height="500" style="width: 100%">
					<!-- <el-table-column prop="id" label="序号" fixed width="90"></el-table-column> -->
					<el-table-column prop="user_id" label="用户序号" fixed width="90"></el-table-column>
					<el-table-column prop="user_name" label="用户名称" fixed min-width="180"></el-table-column>
					<el-table-column prop="sales_amount" label="销售金额/元" fixed min-width="120"></el-table-column>
					<!-- <el-table-column prop="title" label="业务员角色" min-width="120"></el-table-column> -->
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
					<!-- <el-table-column prop="company_name" label="分公司" min-width="180"></el-table-column> -->
					<!-- <el-table-column prop="parent_id" label="上级序号" min-width="90"></el-table-column> -->
					<!-- <el-table-column prop="parent_name" label="上级名称" min-width="180"></el-table-column> -->
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
							<span v-for="(item, index) in {0: 'text-info', 1: 'text-success', 2: 'text-warning', 3: 'text-danger'}" v-if="scope.row.status == index" :class="item">{{scope.row.status_msg}}</span>
						</template>
					</el-table-column>
					<!-- <el-table-column label="操作" fixed="right" min-width="210">
						<template slot-scope="scope">
							
						</template>
					</el-table-column> -->
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
					status: '' // 角色状态
				},
				userList: [], // 用户列表
				listPagination: {}, // 列表分页参数
				
				// 合并行的相关参数
				spanArr: [], // 用于存放每一行记录的合并数
				position: 0 // spanArr的索引
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
				this.$axios.get(this.$url + 'son_salesman', {
					params: {
						user_name: this.formInline.user_name,
						parent_id: this.$route.query.id, // 获取路由带过来的参数
						role_id: this.$route.query.role_id, // 获取路由带过来的参数
						status: this.formInline.status,
						page: this.listPagination.current_page,
						size: this.listPagination.per_page
					}
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
			 * 重置表单
			 * @param {Object} formName
			 */
			resetForm(formName) {
				// this.$refs[formName].resetFields();
				this.formInline = {};
				this.getUserList();
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
