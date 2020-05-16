<template>
	<div class="feedback">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>投诉建议列表</span></el-col>
					<el-col :span="18">
						<!-- 查询 s -->
						<el-form :inline="true" :model="formInline" size="mini" class="demo-form-inline">
							<el-form-item label="">
								<el-select v-model="formInline.status" placeholder="状态">
									<el-option v-for="(item, index) in {0: '待处理', 1: '已处理', 2: '处理中'}" :key="index" :label="item" :value="Number(index)"></el-option>
								</el-select>
							</el-form-item>
							<el-form-item label="">
								<el-input placeholder="用户名称、电话或反馈内容关键字" v-model="formInline.keywords" clearable>
									<el-button slot="append" icon="el-icon-search" @click="getFeedbackList()">查询</el-button>
								</el-input>
							</el-form-item>
						</el-form>
						<!-- 查询 e -->
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- 用户反馈列表 s -->
				<el-table :data="feedbackList" :empty-text="listPagination.total == 0 ? '' : '数据加载中…'" max-height="500" border style="width: 100%">
					<el-table-column prop="id" label="序号" fixed width="50"></el-table-column>
					<el-table-column prop="user_name" label="用户名称" width="120"></el-table-column>
					<el-table-column prop="phone" label="用户电话" width="120"></el-table-column>
					<el-table-column prop="status" label="状态" fixed width="90" :filters="[{ text: '待处理', value: 0 }, { text: '已处理', value: 1 }, { text: '处理中', value: 2 }]" :filter-method="filterStatus" filter-placement="bottom-end">
						<template slot-scope="scope">
							<span v-for="(item, index) in {0: 'text-danger', 1: 'text-success', 2: 'text-warning'}" :key="index" v-if="scope.row.status == index" :class="item">{{scope.row.status_msg}}</span>
						</template>
					</el-table-column>
					<el-table-column prop="content" label="反馈内容" min-width="180" show-overflow-tooltip></el-table-column>
					<el-table-column prop="create_time" label="反馈创建时间" width="180"></el-table-column>
					<el-table-column label="操作" fixed="right" width="130">
						<template slot-scope="scope">
							<el-button type="primary" size="mini" plain @click="toFeedbackHandle(scope.row)">更新反馈状态</el-button>
						</template>
					</el-table-column>
				</el-table>
				<!-- 用户反馈列表 e -->
				
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
					keywords: '' // 关键字
					/* user_name: '', // 用户名称
					phone: '', // 用户电话
					content: '' // 反馈内容 */
				},
				feedbackList: [], // 用户反馈列表
				listPagination: {}, // 列表分页参数
			}
		},
		mounted() {
			this.getFeedbackList(); // 获取用户反馈列表
		},
		methods: {
			/**
			 * 获取用户反馈列表
			 */
			getFeedbackList() {
				let self = this;
				this.$axios.get(this.$url + 'feedback', {
					params: {
						keywords: this.formInline.keywords,
						status: this.formInline.status,
						/* user_name: this.formInline.user_name,
						phone: this.formInline.phone,
						content: this.formInline.content, */
						page: this.listPagination.current_page,
						size: this.listPagination.per_page
					}
				})
				.then(function(res) {
					if (res.data.status == 1) {
						// 用户反馈列表分页参数
						self.listPagination = res.data.data;
						
						// 当数据为空时
						if (self.listPagination.total == 0) {
							self.$message({
								message: '数据不存在',
								type: 'warning'
							});
							return;
						}
						
						// 用户反馈列表
						self.feedbackList = self.listPagination.data;
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
				this.getFeedbackList();
			},
			
			/**
			 * 分页 currentPage 改变时会触发
			 * @param {Object} current_page
			 */
			handleCurrentChange(current_page) {
				this.listPagination.current_page = current_page; // 当前页数
				this.getFeedbackList();
			},
			
			/**
			 * 筛选反馈处理状态
			 * @param {Object} value
			 * @param {Object} row
			 */
			filterStatus(value, row) {
				return row.status === value;
			},
			
			/**
			 * 跳转用户反馈处理状态更新页
			 * @param {Object} row
			 */
			toFeedbackHandle(row) {
				this.$router.push({path: "feedback_handle", query: {id: row.id}});
			}
		}
	}
</script>

<style>
</style>
