<template>
	<div class="device">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>广告屏列表</span></el-col>
				</el-row>
				<el-row :gutter="20" type="flex" justify="space-between" style="margin-top: 1rem;">
					<el-col :span="18">
						<!-- 查询 s -->
						<el-form :inline="true" :model="formInline" size="mini" class="demo-form-inline">
							<el-form-item label="">
								<el-select v-model="formInline.status" clearable placeholder="状态">
									<el-option v-for="(item, index) in {0: '下线', 1: '正常', 2: '故障'}" :label="item" :value="Number(index)"></el-option>
								</el-select>
							</el-form-item>
							<el-form-item label="">
								<el-input placeholder="设备编号" v-model="formInline.device_sn" clearable></el-input>
							</el-form-item>
							<el-form-item label="">
								<el-input placeholder="店铺名称" v-model="formInline.shop_name" clearable>
									<el-button slot="append" icon="el-icon-search" @click="getDeviceList()">查询</el-button>
								</el-input>
							</el-form-item>
						</el-form>
						<!-- 查询 e -->
					</el-col>
					<el-col :span="6">
						<!-- 新增 s -->
						<router-link to="adddevice"><el-button size="mini" type="primary" icon="el-icon-plus">新增广告屏</el-button></router-link>
						<!-- 新增 e -->
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- 广告屏列表 s -->
				<el-table :data="deviceList" empty-text="数据加载中…" border style="width: 100%">
					<el-table-column prop="device_id" label="序号" fixed width="70"></el-table-column>
					<el-table-column prop="device_sn" label="设备编号" width="120"></el-table-column>
					<el-table-column prop="shop_name" label="店铺名称" width="120"></el-table-column>
					<el-table-column prop="shop_id" label="店铺编号" width="90"></el-table-column>
					<el-table-column prop="brand_msg" label="品牌" min-width="100"></el-table-column>
					<el-table-column prop="model_msg" label="型号" width="100"></el-table-column>
					<el-table-column prop="size_msg" label="尺寸/寸" width="80"></el-table-column>
					<el-table-column prop="sale_price" label="价格/元" width="100"></el-table-column>
					<el-table-column prop="saled_part" label="已售份额" width="90"></el-table-column>
					<el-table-column prop="company_name" label="所属分公司" width="120"></el-table-column>
					<el-table-column prop="level" label="广告位等级" width="100"></el-table-column>
					<el-table-column prop="status" label="状态" width="80" :filters="[{ text: '下线', value: 0 }, { text: '正常', value: 1 },{ text: '故障', value: 2 }]" :filter-method="filterStatus" filter-placement="bottom-end">
						<template slot-scope="scope">
							<span :class="scope.row.status === 0 ? 'text-info' : (scope.row.status === 1 ? 'text-success' : 'text-danger')">{{scope.row.status_msg}}</span>
						</template>
					</el-table-column>
					<el-table-column label="操作" fixed="right" min-width="160">
						<template slot-scope="scope">
							<el-button style="margin:0 5px 5px 0;" type="primary" size="mini" plain @click="toDeviceEdit(scope.row)">编辑</el-button>
<!-- 							<el-button style="margin:0 5px 5px 0;" type="danger" size="mini" plain @click="deleteDevice(scope)">删除</el-button>
 -->						</template>
					</el-table-column>
				</el-table>
				<!-- 广告屏列表 e -->
				
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
					status: '', // 状态
					device_sn: '', // 设备编号
					shop_name: '', // 店铺名称
				},
				deviceList: [], // 广告屏列表
				listPagination: {}, // 列表分页参数
			}
		},
		mounted() {
			this.getDeviceList(); // 获取广告屏列表
		},
		methods: {
			/**
			 * 获取广告屏列表
			 */
			getDeviceList() {
				let self = this;
				this.$axios.get(this.$url + 'device', {
					params: {
						status: this.formInline.status,
						device_sn: this.formInline.device_sn,
						shop_name: this.formInline.shop_name,
						page: this.listPagination.current_page,
						size: this.listPagination.per_page
					}
				})
				.then(function(res) {
					if (res.data.status == 1) {
						// 广告屏列表分页参数
						self.listPagination = res.data.data;
						// 当数据为空时
						if (self.listPagination.total == 0) {
							self.$message({
								message: '数据不存在',
								type: 'warning'
							});
							return;
						}
						
						// 广告屏列表
						self.deviceList = self.listPagination.data;
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
				this.getDeviceList();
			},
			
			/**
			 * 分页 currentPage 改变时会触发
			 * @param {Object} current_page
			 */
			handleCurrentChange(current_page) {
				this.listPagination.current_page = current_page; // 当前页数
				this.getDeviceList();
			},
			
			/**
			 * 筛选广告屏状态
			 * @param {Object} value
			 * @param {Object} row
			 */
			filterStatus(value, row) {
				return row.status === value;
			},
			
			/**
			 * 跳转广告屏编辑页
			 * @param {Object} row
			 */
			toDeviceEdit(row) {
				this.$router.push({path: "editdevice", query: {device_id: row.device_id,city_id:row.city_id,area_id:row.area_id}});
			},
			/**
			 * 删除广告屏
			 * @param {Object} scope
			 */
			deleteDevice(scope) {
				this.$confirm('此操作将永久删除该广告屏, 是否继续?', '删除', {
					confirmButtonText: '确定',
					cancelButtonText: '取消',
					type: 'warning'
				}).then(() => {
					// 调用删除接口
					let self = this;
					this.$axios.delete(this.$url + 'device/' + scope.row.device_id)
					.then(function(res) {
						// 移除元素
						self.deviceList.splice(scope.$index, 1);
						
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
