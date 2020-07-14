<template>
	<div class="act_raffle">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>中奖纪录列表</span></el-col>
					<el-col :span="18">
						<!-- 查询 s -->
						<el-form :inline="true" :model="formInline" size="mini" class="demo-form-inline">
							<el-form-item label="">
								<el-input placeholder="中奖者电话" v-model="formInline.phone" clearable>
									<el-button slot="append" icon="el-icon-search" @click="getActRaffleList()">查询</el-button>
								</el-input>
							</el-form-item>
						</el-form>
						<!-- 查询 e -->
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- 中奖纪录列表 s -->
				<el-table :data="actRaffleList" :empty-text="listPagination.total == 0 ? '' : '数据加载中…'" max-height="500" border style="width: 100%">
					<el-table-column prop="raffle_id" label="序号" fixed width="50"></el-table-column>
					<el-table-column prop="phone" label="中奖者电话" fixed width="120"></el-table-column>
					<el-table-column prop="prize_name" label="奖品名称" min-width="120"></el-table-column>
					<el-table-column prop="act_name" label="所属活动" min-width="120"></el-table-column>
					<el-table-column prop="sponsor" label="奖品赞助商" width="120"></el-table-column>
					<el-table-column prop="sponsor_phone" label="赞助商电话" width="120"></el-table-column>
					<el-table-column prop="sponsor_address" label="赞助商地址" width="120" show-overflow-tooltip></el-table-column>
					<el-table-column prop="shop_name" label="抽奖店铺名称" width="120"></el-table-column>
					<el-table-column prop="shop_address" label="店铺地址" width="120" show-overflow-tooltip></el-table-column>
					<el-table-column prop="raffle_time" label="抽奖时间" width="180"></el-table-column>
					<!-- <el-table-column label="操作" fixed="right" min-width="160">
						
					</el-table-column> -->
				</el-table>
				<!-- 中奖纪录列表 e -->
				
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
					phone: '' // 中奖者电话
				},
				actRaffleList: [], // 中奖纪录列表
				listPagination: {}, // 列表分页参数
			}
		},
		mounted() {
			this.getActRaffleList();
		},
		methods: {
			/**
			 * 获取中奖纪录列表
			 * @param {Object} is_delete
			 */
			getActRaffleList(is_delete) {
				let self = this;
				this.$axios.get(this.$url + 'act_raffle', {
					params: {
						phone: this.formInline.phone,
						page: this.listPagination.current_page,
						size: this.listPagination.per_page
					}
				})
				.then(function(res) {
					if (res.data.status == 1) {
						// 中奖纪录列表分页参数
						self.listPagination = res.data.data;
						
						// 当数据为空时
						if (self.listPagination.total == 0) {
							self.$message({
								message: '数据不存在',
								type: 'warning'
							});
							return;
						}
						
						// 中奖纪录列表
						self.actRaffleList = self.listPagination.data;
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
				this.getActRaffleList();
			},
			
			/**
			 * 分页 currentPage 改变时会触发
			 * @param {Object} current_page
			 */
			handleCurrentChange(current_page) {
				this.listPagination.current_page = current_page; // 当前页数
				this.getActRaffleList();
			}
		}
	}
</script>

<style>
</style>
