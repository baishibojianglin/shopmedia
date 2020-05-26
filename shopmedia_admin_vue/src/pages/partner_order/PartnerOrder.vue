<template>
	<div class="partner_order">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>合作商订单列表</span></el-col>
					<el-col :span="6">
						<!-- 查询 s -->
						<el-form :inline="true" :model="formInline" size="mini" class="demo-form-inline">
							<el-form-item label="">
								<el-input placeholder="订单编号" v-model="formInline.order_sn" clearable>
									<el-button slot="append" icon="el-icon-search" @click="getOrderList()">查询</el-button>
								</el-input>
							</el-form-item>
						</el-form>
						<!-- 查询 e -->
					</el-col>
					<el-col :span="12">
						<!-- 新增 s -->
						<router-link to="partner_order_create"><el-button size="mini" type="primary" icon="el-icon-plus">新增订单</el-button></router-link>
						<!-- 新增 e -->
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- 订单列表 s -->
				<el-table :data="orderList" empty-text="数据加载中…" border style="width: 100%">
					<el-table-column prop="order_id" label="序号" fixed width="70"></el-table-column>
					<el-table-column prop="order_sn" label="订单编号" fixed width="180"></el-table-column>
					<el-table-column label="广告屏合作商" header-align="center">
						<el-table-column prop="user_name" label="合作商名称" min-width="120"></el-table-column>
						<el-table-column prop="phone" label="合作商电话" width="120"></el-table-column>
					</el-table-column>
					<el-table-column label="广告屏" header-align="center">
						<el-table-column prop="device_id" label="广告屏序号" width="120"></el-table-column>
						<el-table-column prop="shopname" label="店铺名称" width="120"></el-table-column>
						<el-table-column prop="device_price" label="广告屏总价/元" width="120"></el-table-column>
					</el-table-column>
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
					<el-table-column label="操作" fixed="right" min-width="160">
						<template slot-scope="scope">
							<el-button type="primary" size="mini" plain @click="toOrderEdit(scope.row)">编辑</el-button>
							<el-button type="danger" size="mini" plain @click="deleteOrder(scope)">删除</el-button>
						</template>
					</el-table-column>
				</el-table>
				<!-- 订单列表 e -->
				
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
					order_sn: '' // 订单编号
				},
				orderList: [], // 订单列表
				listPagination: {}, // 列表分页参数
			}
		},
		mounted() {
			this.getOrderList(); // 获取订单列表
		},
		methods: {
			/**
			 * 获取订单列表
			 */
			getOrderList() {
				let self = this;
				this.$axios.get(this.$url + 'partner_order', {
					params: {
						order_sn: this.formInline.order_sn,
						page: this.listPagination.current_page,
						size: this.listPagination.per_page
					}
				})
				.then(function(res) {
					if (res.data.status == 1) {
						// 订单列表分页参数
						self.listPagination = res.data.data;
						
						// 当数据为空时
						if (self.listPagination.total == 0) {
							self.$message({
								message: '数据不存在',
								type: 'warning'
							});
							return;
						}
						
						// 订单列表
						self.orderList = self.listPagination.data;
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
				this.getOrderList();
			},
			
			/**
			 * 分页 currentPage 改变时会触发
			 * @param {Object} current_page
			 */
			handleCurrentChange(current_page) {
				this.listPagination.current_page = current_page; // 当前页数
				this.getOrderList();
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
			 * 跳转订单编辑页
			 * @param {Object} row
			 */
			toOrderEdit(row) {
				this.$router.push({path: "partner_order_edit", query: {order_id: row.order_id}});
			},
			/**
			 * 删除订单
			 * @param {Object} scope
			 */
			deleteOrder(scope) {
				let message = scope.row.is_delete == 1 ? '此操作将永久删除该订单，是否继续？' : '移除该订单，放入回收站？';
				this.$confirm(message, '删除', {
					confirmButtonText: '确定',
					cancelButtonText: '取消',
					type: 'warning'
				}).then(() => {
					// 调用删除接口
					let self = this;
					this.$axios.delete(this.$url + 'partner_order/' + scope.row.order_id)
					.then(function(res) {
						// 移除元素
						self.orderList.splice(scope.$index, 1);
						
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
					this.$message({
						type: 'info',
						message: '已取消删除'
					});
				});
			}
		}
	}
</script>

<style>
</style>
