<template>
	<div class="ad_combo_order">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="24"><span>广告套餐订单列表</span></el-col>
				</el-row>
			</div>
			<div class="">
				<!-- 广告套餐订单列表 s -->
				<el-table :data="adComboOrderList" :empty-text="listPagination.total == 0 ? '' : '数据加载中…'" max-height="500" border style="width: 100%">
					<el-table-column prop="order_id" label="序号" fixed width="50"></el-table-column>
					<el-table-column prop="order_sn" label="订单号" min-width="180"></el-table-column>
					<el-table-column prop="phone" label="广告主电话" fixed min-width="120"></el-table-column>
					<el-table-column prop="advertiser_address" label="广告主地址" min-width="120" show-overflow-tooltip></el-table-column>
					<el-table-column prop="salesman_phone" label="广告主业务员电话" min-width="120"></el-table-column>
					<el-table-column prop="ad_name" label="广告名称" min-width="120"></el-table-column>
					<el-table-column prop="combo_id" label="套餐序号" width="90"></el-table-column>
					<el-table-column prop="combo_price" label="套餐价格/元" min-width="120"></el-table-column>
					<el-table-column prop="device_quantity" label="设备数量/台" min-width="120"></el-table-column>
					<el-table-column prop="ad_days" label="广告天数" min-width="90"></el-table-column>
					<el-table-column prop="order_price" label="订单价格/元" width="120"></el-table-column>
					<el-table-column prop="order_status" label="订单状态" width="90" :filters="[{ text: '未付款', value: 0 }, { text: '已完成', value: 1 }, { text: '已取消', value: 2 }]" :filter-method="filterStatus" filter-placement="bottom-end">
						<template slot-scope="scope">
							<span :class="scope.row.order_status === 0 ? 'text-info' : (scope.row.order_status === 1 ? 'text-success' : 'text-danger')">{{scope.row.order_status_msg}}</span>
						</template>
					</el-table-column>
					<el-table-column prop="pay_status" label="支付状态" width="90" :filters="[{ text: '未支付', value: 0 }, { text: '已支付', value: 1 }]" :filter-method="filterPayStatus" filter-placement="bottom-end">
						<template slot-scope="scope">
							<span :class="scope.row.pay_status === 0 ? 'text-info' : 'text-success'">{{scope.row.pay_status_msg}}</span>
						</template>
					</el-table-column>
					<el-table-column prop="order_time" label="下单时间" width="180"></el-table-column>
					<el-table-column prop="pay_time" label="付款时间" width="180"></el-table-column>
					<el-table-column v-if="false" label="操作" fixed="right" min-width="90">
						<template slot-scope="scope">
							<el-button type="primary" size="mini" plain @click="toAdComboEdit(scope.row)">编辑</el-button>
						</template>
					</el-table-column>
				</el-table>
				<!-- 广告套餐订单列表 e -->
				
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
				adComboOrderList: [], // 广告套餐订单列表
				listPagination: {}, // 列表分页参数
			}
		},
		mounted() {
			this.getAdComboOrderList();
		},
		methods: {
			/**
			 * 获取广告套餐订单列表
			 */
			getAdComboOrderList() {
				let self = this;
				this.$axios.get(this.$url + 'ad_combo_order', {
					params: {
						page: this.listPagination.current_page,
						size: this.listPagination.per_page
					}
				})
				.then(function(res) {
					if (res.data.status == 1) {
						// 广告套餐订单列表分页参数
						self.listPagination = res.data.data;
						
						// 当数据为空时
						if (self.listPagination.total == 0) {
							self.$message({
								message: '数据不存在',
								type: 'warning'
							});
							return;
						}
						
						// 广告套餐订单列表
						self.adComboOrderList = self.listPagination.data;
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
				this.getAdComboOrderList();
			},
			
			/**
			 * 分页 currentPage 改变时会触发
			 * @param {Object} current_page
			 */
			handleCurrentChange(current_page) {
				this.listPagination.current_page = current_page; // 当前页数
				this.getAdComboOrderList();
			},
			
			/**
			 * 筛选订单状态
			 * @param {Object} value
			 * @param {Object} row
			 */
			filterStatus(value, row) {
				return row.order_status === value;
			},
			/**
			 * 筛选支付状态
			 * @param {Object} value
			 * @param {Object} row
			 */
			filterPayStatus(value, row) {
				return row.pay_status === value;
			},
			
			/**
			 * 跳转广告套餐订单编辑页
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
