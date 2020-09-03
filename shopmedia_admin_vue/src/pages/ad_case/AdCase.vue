<template>
	<div class="ad_case">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>广告案例列表</span></el-col>
					<el-col :span="6">
						<!-- 查询 s -->
						<el-form :inline="true" :model="formInline" size="mini" class="demo-form-inline">
							<el-form-item label="">
								<el-input placeholder="广告名称" v-model="formInline.ad_name" clearable>
									<el-button slot="append" icon="el-icon-search" @click="getAdCaseList()">查询</el-button>
								</el-input>
							</el-form-item>
						</el-form>
						<!-- 查询 e -->
					</el-col>
					<el-col :span="12">
						<!-- 新增 s -->
						<router-link to="ad_case_create"><el-button size="mini" type="primary" icon="el-icon-plus">新增广告案例</el-button></router-link>
						<!-- 新增 e -->
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- 广告案例列表 s -->
				<el-table :data="adCaseList" :empty-text="listPagination.total == 0 ? '' : '数据加载中…'" max-height="500" border style="width: 100%">
					<el-table-column prop="ad_case_id" label="序号" fixed width="50"></el-table-column>
					<el-table-column prop="ad_name" label="广告名称" fixed width="120"></el-table-column>
					<el-table-column prop="advertiser_name" label="广告主名称" min-width="120"></el-table-column>
					<el-table-column prop="advertiser_phone" label="广告主电话" min-width="120"></el-table-column>
					<el-table-column prop="ad_cover" label="广告案例封面图" width="130">
						<template slot-scope="scope">
							<el-image style="width: 100px; height: 100px" :src="scope.row.ad_cover" fit="cover
"></el-image>
						</template>
					</el-table-column>
					<el-table-column prop="status_msg" label="状态" width="70"></el-table-column>
					<el-table-column prop="create_time" label="创建时间" width="180"></el-table-column>
					<el-table-column label="操作" fixed="right" min-width="90">
						<template slot-scope="scope">
							<el-button type="primary" size="mini" plain @click="toAdCaseEdit(scope.row)">编辑</el-button>
						</template>
					</el-table-column>
				</el-table>
				<!-- 广告案例列表 e -->
				
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
					ad_name: '' // 广告名称
				},
				adCaseList: [], // 广告案例列表
				listPagination: {}, // 列表分页参数
			}
		},
		mounted() {
			this.getAdCaseList();
		},
		methods: {
			/**
			 * 获取广告案例列表
			 */
			getAdCaseList() {
				let self = this;
				this.$axios.get(this.$url + 'ad_case', {
					params: {
						ad_name: this.formInline.ad_name,
						page: this.listPagination.current_page,
						size: this.listPagination.per_page
					}
				})
				.then(function(res) {
					// console.log(123, res);
					if (res.data.status == 1) {
						// 广告案例列表分页参数
						self.listPagination = res.data.data;
						
						// 当数据为空时
						if (self.listPagination.total == 0) {
							self.$message({
								message: '数据不存在',
								type: 'warning'
							});
							return;
						}
						
						// 广告案例列表
						self.adCaseList = self.listPagination.data;
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
				this.getAdCaseList();
			},
			
			/**
			 * 分页 currentPage 改变时会触发
			 * @param {Object} current_page
			 */
			handleCurrentChange(current_page) {
				this.listPagination.current_page = current_page; // 当前页数
				this.getAdCaseList();
			},
			
			/**
			 * 跳转广告编辑页
			 * @param {Object} row
			 */
			toAdCaseEdit(row) {
				this.$router.push({path: "ad_case_edit", query: {ad_case_id: row.ad_case_id}});
			}
		}
	}
</script>

<style>
</style>
