<template>
	<div class="ad_combo">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>广告套餐列表</span></el-col>
					<el-col :span="18">
						<!-- 新增 s -->
						<!-- <router-link to="ad_combo_create"><el-button size="mini" type="primary" icon="el-icon-plus">新增广告套餐</el-button></router-link> -->
						<!-- 新增 e -->
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- 广告套餐列表 s -->
				<el-table :data="adComboList" :empty-text="listPagination.total == 0 ? '' : '数据加载中…'" max-height="500" border style="width: 100%">
					<el-table-column prop="combo_id" label="序号" fixed width="50"></el-table-column>
					<el-table-column prop="combo_price" label="套餐价格/元" fixed min-width="120"></el-table-column>
					<el-table-column prop="device_quantity" label="设备数量/台" min-width="120"></el-table-column>
					<el-table-column prop="ad_seconds" label="广告时长/秒" min-width="120"></el-table-column>
					<el-table-column prop="ad_days" label="广告天数" min-width="90"></el-table-column>
					<el-table-column prop="ad_type_name" label="广告类型" min-width="90"></el-table-column>
					<el-table-column prop="status_msg" label="状态" width="70"></el-table-column>
					<el-table-column v-if="false" label="操作" fixed="right" min-width="90">
						<template slot-scope="scope">
							<el-button type="primary" size="mini" plain @click="toAdComboEdit(scope.row)">编辑</el-button>
						</template>
					</el-table-column>
				</el-table>
				<!-- 广告套餐列表 e -->
				
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
				adComboList: [], // 广告套餐列表
				listPagination: {}, // 列表分页参数
			}
		},
		mounted() {
			this.getAdComboList();
		},
		methods: {
			/**
			 * 获取广告套餐列表
			 */
			getAdComboList() {
				let self = this;
				this.$axios.get(this.$url + 'ad_combo', {
					params: {
						page: this.listPagination.current_page,
						size: this.listPagination.per_page
					}
				})
				.then(function(res) {
					if (res.data.status == 1) {
						// 广告套餐列表分页参数
						self.listPagination = res.data.data;
						
						// 当数据为空时
						if (self.listPagination.total == 0) {
							self.$message({
								message: '数据不存在',
								type: 'warning'
							});
							return;
						}
						
						// 广告套餐列表
						self.adComboList = self.listPagination.data;
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
				this.getAdComboList();
			},
			
			/**
			 * 分页 currentPage 改变时会触发
			 * @param {Object} current_page
			 */
			handleCurrentChange(current_page) {
				this.listPagination.current_page = current_page; // 当前页数
				this.getAdComboList();
			},
			
			/**
			 * 跳转广告套餐编辑页
			 * @param {Object} row
			 */
			toAdComboEdit(row) {
				this.$router.push({path: "ad_combo_edit", query: {combo_id: row.combo_id}});
			}
		}
	}
</script>

<style>
</style>
