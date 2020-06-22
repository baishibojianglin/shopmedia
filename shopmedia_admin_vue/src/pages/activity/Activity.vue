<template>
	<div class="activity">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>活动列表</span></el-col>
					<el-col :span="6">
						<!-- 查询 s -->
						<el-form :inline="true" :model="formInline" size="mini" class="demo-form-inline">
							<el-form-item label="">
								<el-input placeholder="活动名称" v-model="formInline.act_name" clearable>
									<el-button slot="append" icon="el-icon-search" @click="getActivityList()">查询</el-button>
								</el-input>
							</el-form-item>
						</el-form>
						<!-- 查询 e -->
					</el-col>
					<el-col :span="6">
						<!-- 新增 s -->
						<router-link to="activity_create"><el-button size="mini" type="primary" icon="el-icon-plus">新增活动</el-button></router-link>
						<!-- 新增 e -->
					</el-col>
					<el-col :span="6" style="text-align: right;">
						<!-- <el-button size="mini" icon="el-icon-delete" @click="getActivityList(1)" v-if="formInline.is_delete != 1">回收站</el-button>
						<el-button size="mini" icon="el-icon-back" title="返回" @click="getActivityList()" v-if="formInline.is_delete == 1">返回</el-button> -->
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- 活动列表 s -->
				<el-table :data="activityList" :empty-text="listPagination.total == 0 ? '' : '数据加载中…'" max-height="500" border style="width: 100%">
					<el-table-column prop="act_id" label="序号" fixed width="50"></el-table-column>
					<el-table-column prop="act_name" label="活动名称" fixed min-width="180"></el-table-column>
					<el-table-column prop="start_time" label="开始时间" width="180"></el-table-column>
					<el-table-column prop="end_time" label="结束时间" width="180"></el-table-column>
					<el-table-column prop="times" label="每人每天抽奖限制次数" width="180"></el-table-column>
					<el-table-column prop="create_time" label="创建时间" width="180"></el-table-column>
					<el-table-column prop="status" label="状态" width="90" :filters="[{ text: '禁用', value: 0 }, { text: '启用', value: 1 }]" :filter-method="filterStatus" filter-placement="bottom-end">
						<template slot-scope="scope">
							<span v-for="(item, index) in {0: 'text-info', 1: 'text-success'}" :key="index" v-if="scope.row.status == index" :class="item">{{scope.row.status_msg}}</span>
						</template>
					</el-table-column>
					<el-table-column label="操作" fixed="right" min-width="160">
						<template slot-scope="scope">
							<el-button type="primary" size="mini" plain @click="toActivityEdit(scope.row)" v-if="formInline.is_delete != 1">编辑</el-button>
							<el-button type="primary" size="mini" plain @click="recover(scope.row)" v-if="formInline.is_delete == 1">还原</el-button>
							<!-- <el-button type="danger" size="mini" plain @click="deleteActivity(scope)">删除</el-button> -->
						</template>
					</el-table-column>
				</el-table>
				<!-- 活动列表 e -->
				
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
					act_name: '', // 活动名称
					is_delete: '' // 是否删除
				},
				activityList: [], // 活动列表
				listPagination: {}, // 列表分页参数
			}
		},
		mounted() {
			this.getActivityList(); // 获取活动列表
		},
		methods: {
			/**
			 * 获取活动列表
			 * @param {Object} is_delete
			 */
			getActivityList(is_delete) {
				let self = this;
				this.$axios.get(this.$url + 'activity', {
					params: {
						act_name: this.formInline.act_name,
						is_delete: is_delete,
						page: this.listPagination.current_page,
						size: this.listPagination.per_page
					}
				})
				.then(function(res) {
					if (res.data.status == 1) {
						// 活动列表分页参数
						self.listPagination = res.data.data;
						
						// 当数据为空时
						if (self.listPagination.total == 0) {
							self.$message({
								message: '数据不存在',
								type: 'warning'
							});
							return;
						}
						
						// 活动列表
						self.activityList = self.listPagination.data;
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
				this.getActivityList();
			},
			
			/**
			 * 分页 currentPage 改变时会触发
			 * @param {Object} current_page
			 */
			handleCurrentChange(current_page) {
				this.listPagination.current_page = current_page; // 当前页数
				this.getActivityList();
			},
			
			/**
			 * 筛选活动状态
			 * @param {Object} value
			 * @param {Object} row
			 */
			filterStatus(value, row) {
				return row.status === value;
			},
			
			/**
			 * 跳转活动编辑页
			 * @param {Object} row
			 */
			toActivityEdit(row) {
				this.$router.push({path: "activity_edit", query: {act_id: row.act_id}});
			},
			
			/**
			 * 删除活动
			 * @param {Object} scope
			 */
			deleteActivity(scope) {
				let message = scope.row.is_delete == 1 ? '此操作将永久删除该活动，是否继续？' : '移除该活动，放入回收站？';
				this.$confirm(message, '删除', {
					confirmButtonText: '确定',
					cancelButtonText: '取消',
					type: 'warning'
				}).then(() => {
					// 调用删除接口
					let self = this;
					this.$axios.delete(this.$url + 'activity/' + scope.row.act_id)
					.then(function(res) {
						// 移除元素
						self.activityList.splice(scope.$index, 1);
						
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
				this.$axios.put(this.$url + 'activity/' + row.act_id, {
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
					self.getActivityList();
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
