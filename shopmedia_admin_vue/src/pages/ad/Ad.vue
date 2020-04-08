<template>
	<div class="ad">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>广告列表</span></el-col>
					<el-col :span="6">
						<!-- 查询 s -->
						<el-form :inline="true" :model="formInline" size="mini" class="demo-form-inline">
							<el-form-item label="">
								<el-input placeholder="广告名称" v-model="formInline.ad_name" clearable>
									<el-button slot="append" icon="el-icon-search" @click="getAdList()">查询</el-button>
								</el-input>
							</el-form-item>
						</el-form>
						<!-- 查询 e -->
					</el-col>
					<el-col :span="12">
						<!-- 新增 s -->
						<router-link to="ad_create"><el-button size="mini" type="primary" icon="el-icon-plus">新增广告</el-button></router-link>
						<!-- 新增 e -->
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- 广告列表 s -->
				<el-table :data="adList" empty-text="数据加载中…" border style="width: 100%">
					<el-table-column prop="ad_id" label="序号" fixed width="50"></el-table-column>
					<el-table-column prop="ad_name" label="广告名称" fixed min-width="120"></el-table-column>
					<el-table-column prop="ad_cate_name" label="广告类别" width="120"></el-table-column>
					<el-table-column prop="ad_price" label="广告价格(元)" width="120"></el-table-column>
					<el-table-column label="投放时间">
						<el-table-column prop="start_datetime" label="投放开始时间" width="180"></el-table-column>
						<el-table-column prop="end_datetime" label="投放结束时间" width="180"></el-table-column>
					</el-table-column>
					<el-table-column label="每日播放时间段">
						<el-table-column prop="start_time" label="播放开始时间" width="120"></el-table-column>
						<el-table-column prop="end_time" label="播放结束时间" width="120"></el-table-column>
					</el-table-column>
					<el-table-column prop="play_times" label="每日播放次数" width="120"></el-table-column>
					<el-table-column label="广告主">
						<el-table-column prop="advertisers" label="广告主名称" width="120"></el-table-column>
						<el-table-column prop="phone" label="广告主电话" width="120"></el-table-column>
					</el-table-column>
					<el-table-column prop="shop_cate_name" label="投放店铺类别" width="120"></el-table-column>
					<el-table-column label="投放区域">
						<el-table-column prop="province" label="省份" width="120"></el-table-column>
						<el-table-column prop="city" label="城市" width="120"></el-table-column>
						<el-table-column prop="county" label="区县" width="120"></el-table-column>
						<el-table-column prop="town" label="乡镇街道" width="120"></el-table-column>
					</el-table-column>
					<el-table-column prop="audit_status" label="审核状态" width="90" :filters="[{ text: '待审核', value: 0 }, { text: '正常', value: 1 }, { text: '驳回', value: 2 }]" :filter-method="filterAuditStatus" filter-placement="bottom-end">
						<template slot-scope="scope">
							<span :class="scope.row.audit_status === 0 ? 'text-info' : (scope.row.audit_status === 1 ? 'text-success' : 'text-danger')">{{scope.row.audit_status_msg}}</span>
						</template>
					</el-table-column>
					<el-table-column label="操作" fixed="right" min-width="160">
						<template slot-scope="scope">
							<el-button style="margin:0 5px 5px 0;" type="primary" size="mini" plain @click="toAdEdit(scope.row)">编辑</el-button>
							<el-button style="margin:0 5px 5px 0;" type="danger" size="mini" plain @click="deleteAd(scope)">删除</el-button>
						</template>
					</el-table-column>
				</el-table>
				<!-- 广告列表 e -->
				
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
					ad_name: '' // 分公司名称
				},
				adList: [], // 广告列表
				listPagination: {}, // 列表分页参数
			}
		},
		mounted() {
			this.getAdList(); // 获取广告列表
		},
		methods: {
			/**
			 * 获取广告列表
			 */
			getAdList() {
				let self = this;
				this.$axios.get(this.$url + 'ad', {
					params: {
						ad_name: this.formInline.ad_name,
						page: this.listPagination.current_page,
						size: this.listPagination.per_page
					}/* ,
					headers: {
						'admin-user-id': JSON.parse(localStorage.getItem('admin_user')).user_id,
						'admin-user-token': JSON.parse(localStorage.getItem('admin_user')).token
					} */
				})
				.then(function(res) {
					if (res.data.status == 1) {
						// 广告列表分页参数
						self.listPagination = res.data.data;
						
						// 当数据为空时
						if (self.listPagination.total == 0) {
							self.$message({
								message: '数据不存在',
								type: 'warning'
							});
							return;
						}
						
						// 广告列表
						self.adList = self.listPagination.data;
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
				this.getAdList();
			},
			
			/**
			 * 分页 currentPage 改变时会触发
			 * @param {Object} current_page
			 */
			handleCurrentChange(current_page) {
				this.listPagination.current_page = current_page; // 当前页数
				this.getAdList();
			},
			
			/**
			 * 筛选广告审核状态
			 * @param {Object} value
			 * @param {Object} row
			 */
			filterAuditStatus(value, row) {
				return row.audit_status === value;
			},
			
			/**
			 * 跳转广告编辑页
			 * @param {Object} row
			 */
			toAdEdit(row) {
				this.$router.push({path: "ad_edit", query: {ad_id: row.ad_id}});
			},
			
			/**
			 * 删除广告
			 * @param {Object} scope
			 */
			deleteAd(scope) {
				this.$confirm('此操作将永久删除该广告, 是否继续?', '删除', {
					confirmButtonText: '确定',
					cancelButtonText: '取消',
					type: 'warning'
				}).then(() => {
					// 调用删除接口
					let self = this;
					this.$axios.delete(this.$url + 'ad/' + scope.row.ad_id)
					.then(function(res) {
						// 移除元素
						self.adList.splice(scope.$index, 1);
						
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
			}
		}
	}
</script>

<style>
</style>
