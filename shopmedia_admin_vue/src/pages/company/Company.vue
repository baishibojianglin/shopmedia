<template>
	<div class="company">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>分公司列表{{formInline.is_delete == 1 ? '（回收站）' : ''}}</span></el-col>
					<el-col :span="6">
						<!-- 查询 s -->
						<el-form :inline="true" :model="formInline" size="mini" class="demo-form-inline">
							<el-form-item label="">
								<el-input placeholder="分公司名称" v-model="formInline.company_name" clearable>
									<el-button slot="append" icon="el-icon-search" @click="getCompanyList()">查询</el-button>
								</el-input>
							</el-form-item>
						</el-form>
						<!-- 查询 e -->
					</el-col>
					<el-col :span="6">
						<!-- 新增 s -->
						<router-link to="companycreate"><el-button size="mini" type="primary" icon="el-icon-plus">新增分公司</el-button></router-link>
						<!-- 新增 e -->
					</el-col>
					<el-col :span="6" style="text-align: right;">
						<!-- <el-button size="mini" icon="el-icon-delete" @click="getCompanyList(1)" v-if="formInline.is_delete != 1">回收站</el-button>
						<el-button size="mini" icon="el-icon-back" title="返回" @click="getCompanyList()" v-if="formInline.is_delete == 1">返回</el-button> -->
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- 分公司列表 s -->
				<el-table :data="companyList" empty-text="数据加载中…" border style="width: 100%">
					<el-table-column prop="company_id" label="序号" fixed width="90"></el-table-column>
					<el-table-column prop="company_name" label="分公司名称" fixed min-width="180"></el-table-column>
					<el-table-column prop="province" label="省份" min-width="120"></el-table-column>
					<el-table-column prop="city" label="城市" min-width="120"></el-table-column>
					<el-table-column prop="person_name" label="负责人" width="120"></el-table-column>
					<el-table-column prop="phone" label="负责人电话" width="120"></el-table-column>
					<el-table-column prop="status" label="状态" width="90" :filters="[{ text: '禁用', value: 0 }, { text: '启用', value: 1 }]" :filter-method="filterStatus" filter-placement="bottom-end">
						<template slot-scope="scope">
							<span :class="scope.row.status === 0 ? 'text-info' : (scope.row.status === 1 ? 'text-success' : 'text-danger')">{{scope.row.status_msg}}</span>
						</template>
					</el-table-column>
					<el-table-column prop="create_time" label="创建时间" width="180" sortable></el-table-column>
					<el-table-column label="操作" fixed="right" min-width="160">
						<template slot-scope="scope">
							<el-button style="margin:0 5px 5px 0;" type="primary" size="mini" plain @click="toCompanyEdit(scope.row)" v-if="formInline.is_delete != 1">编辑</el-button>
							<el-button style="margin:0 5px 5px 0;" type="primary" size="mini" plain @click="recover(scope.row)" v-if="formInline.is_delete == 1">还原</el-button>
							<!-- <el-button style="margin:0 5px 5px 0;" type="danger" size="mini" plain @click="deleteCompany(scope)">删除</el-button> -->
						</template>
					</el-table-column>
				</el-table>
				<!-- 分公司列表 e -->
				
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
					company_name: '', // 分公司名称
					is_delete: '' // 是否删除
				},
				companyList: [], // 分公司列表
				listPagination: {}, // 列表分页参数
			}
		},
		mounted() {
			this.getCompanyList(); // 获取分公司列表
		},
		methods: {
			/**
			 * 获取分公司列表
			 * @param {Object} is_delete
			 */
			getCompanyList(is_delete) {
				let self = this;
				this.$axios.get(this.$url + 'company', {
					params: {
						company_name: this.formInline.company_name,
						is_delete: is_delete,
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
						// 分公司列表分页参数
						self.listPagination = res.data.data;
						
						// 当数据为空时
						if (self.listPagination.total == 0) {
							self.$message({
								message: '数据不存在',
								type: 'warning'
							});
							return;
						}
						
						// 分公司列表
						self.companyList = self.listPagination.data;
						self.formInline.is_delete = is_delete == 1 ? is_delete : '';
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
				this.getCompanyList();
			},
			
			/**
			 * 分页 currentPage 改变时会触发
			 * @param {Object} current_page
			 */
			handleCurrentChange(current_page) {
				this.listPagination.current_page = current_page; // 当前页数
				this.getCompanyList();
			},
			
			/**
			 * 筛选分公司状态
			 * @param {Object} value
			 * @param {Object} row
			 */
			filterStatus(value, row) {
				return row.status === value;
			},
			
			/**
			 * 跳转分公司编辑页
			 * @param {Object} row
			 */
			toCompanyEdit(row) {
				this.$router.push({path: "companyedit", query: {company_id: row.company_id, province_id: row.province_id}});
			},
			
			/**
			 * 删除分公司
			 * @param {Object} scope
			 */
			deleteCompany(scope) {
				let message = scope.row.is_delete == 1 ? '此操作将永久删除该分公司，是否继续？' : '移除该分公司，放入回收站？';
				this.$confirm(message, '删除', {
					confirmButtonText: '确定',
					cancelButtonText: '取消',
					type: 'warning'
				}).then(() => {
					// 调用删除接口
					let self = this;
					this.$axios.delete(this.$url + 'company/' + scope.row.company_id)
					.then(function(res) {
						// 移除元素
						self.companyList.splice(scope.$index, 1);
						
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
					/* this.$message({
						type: 'info',
						message: '已取消删除'
					}); */
				});
			},
			
			/**
			 * 还原回收站数据
			 * @param {Object} row
			 */
			recover(row) {
				let self = this;
				this.$axios.put(this.$url + 'company/' + row.company_id, {
					// 参数
					is_delete: row.is_delete
				}/* , {
					// 请求头配置
					headers: {
						'admin-user-id': JSON.parse(localStorage.getItem('admin_user')).id,
						'admin-user-token': JSON.parse(localStorage.getItem('admin_user')).token
					}
				} */)
				.then(function(res) {
					let type = res.data.status == 1 ? 'success' : 'warning';
					self.$message({
						message: '还原成功',
						type: type
					});
					self.getCompanyList();
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
