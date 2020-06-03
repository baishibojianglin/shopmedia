<template>
	<div class="shop">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>店铺列表</span></el-col>
					<el-col :span="15">
						<!-- 查询 s -->
						<el-form :inline="true" :model="formInline" size="mini" class="demo-form-inline">
							<el-form-item label="">
								<el-select v-model="formInline.status" clearable placeholder="状态">
									<el-option v-for="(item, index) in {0: '禁用', 1: '启用', 2: '待审核', 3: '驳回'}" :key="index" :label="item" :value="Number(index)"></el-option>
								</el-select>
							</el-form-item>
							<el-form-item label="">
								<el-select v-model="formInline.is_commission" clearable placeholder="业务提成">
									<el-option v-for="(item, index) in {0: '未提成', 1: '已提成'}" :key="index" :label="item" :value="Number(index)"></el-option>
								</el-select>
							</el-form-item>
							<el-form-item label="">
								<el-input placeholder="店铺名称" v-model="formInline.shop_name" clearable>
									<el-button slot="append" icon="el-icon-search" @click="getShopList()">查询</el-button>
								</el-input>
							</el-form-item>
						</el-form>
						<!-- 查询 e -->
					</el-col>
					<el-col :span="3">
						<el-button size="mini" icon="el-icon-back" title="返回" @click="back()">返回</el-button>
					</el-col>
				</el-row>
				<!-- 用户信息 s -->
				<el-row v-if="formInline.role_id" :gutter="20" type="flex" justify="space-between">
					<el-col :span="24">
						<el-tag type="" effect="plain"><span v-if="formInline.role_id == 3">店家</span><span v-if="formInline.role_id == 6">店铺业务员</span>：{{user_name}}</el-tag>
						<el-tag v-if="formInline.role_id == 6" type="danger" effect="plain" style="margin-left: 5px;">店铺状态合计：启用 {{count.status_enable_count}}，禁用 {{count.status_disable_count}}，待审核 {{count.status_pending_count}}，驳回 {{count.status_reject_count}}</el-tag>
						<el-tag v-if="formInline.role_id == 6" type="danger" effect="plain" style="margin-left: 5px;">店铺业务员提成状态合计：未提成 {{count.not_commission_count}}，已提成 {{count.is_commission_count}}</el-tag>
					</el-col>
				</el-row>
				<!-- 用户信息 e -->
			</div>
			<div class="">
				<!-- 店铺列表 s -->
				<el-table :data="shopList" :empty-text="listPagination.total == 0 ? '' : '数据加载中…'" max-height="500" border style="width: 100%">
					<el-table-column prop="shop_id" label="序号" fixed width="50"></el-table-column>
					<el-table-column prop="shop_name" label="店铺名称" fixed width="120"></el-table-column>
					<el-table-column prop="address" label="详细地址" width="180" show-overflow-tooltip></el-table-column>
					<el-table-column label="店铺区域" header-align="center">
						<el-table-column prop="province" label="省份" width="120"></el-table-column>
						<el-table-column prop="city" label="城市" width="120"></el-table-column>
						<el-table-column prop="county" label="区县" width="120"></el-table-column>
						<el-table-column prop="town" label="乡镇街道" width="120"></el-table-column>
					</el-table-column>
					<el-table-column prop="status" label="状态" width="90" :filters="[{ text: '禁用', value: 0 }, { text: '启用', value: 1 }, { text: '待审核', value: 2 }, { text: '驳回', value: 3 }]" :filter-method="filterStatus" filter-placement="bottom-end">
						<template slot-scope="scope">
							<span v-for="(item, index) in {0: 'text-info', 1: 'text-success', 2: 'text-warning', 3: 'text-danger'}" :key="index" v-if="scope.row.status == index" :class="item">{{scope.row.status_msg}}</span>
						</template>
					</el-table-column>
					<el-table-column prop="is_commission" label="店铺业务员提成状态" :fixed="formInline.role_id == 6 ? 'right' : false" width="120">
						<template slot-scope="scope">
							<span v-for="(item, index) in {0: 'text-warning', 1: 'text-success'}" :key="index" v-if="scope.row.is_commission == index" :class="item">{{scope.row.is_commission_msg}}</span>
						</template>
					</el-table-column>
					<el-table-column prop="create_time" label="创建时间" width="180"></el-table-column>
					<el-table-column label="操作" fixed="right" width="90">
						<template slot-scope="scope">
							<el-button type="primary" size="mini" plain @click="toShopEdit(scope.row)">编辑</el-button>
						</template>
					</el-table-column>
				</el-table>
				<!-- 店铺列表 e -->
				
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
					role_id: '', // 用户角色ID
					shopkeeper_id: '', // 店家ID
					salesman_id: '', // 店铺业务员ID
					shop_name: '', // 店铺名称
					status: '', // 店铺状态
					is_commission: '' // 店铺业务员提成状态
				},
				shopList: [], // 店铺列表
				listPagination: {}, // 列表分页参数
				count: {}, // 统计数据
				
				user_name: '' // 用户名称
			}
		},
		mounted() {
			this.getParams();
			this.getShopList(); // 获取店铺列表
			this.getShopCount(); // 统计店铺数据
		},
		methods: {
			/**
			 * 获取路由带过来的参数
			 */
			getParams() {
				// 用户角色ID
				this.formInline.role_id = this.$route.query.role_id;
				// 店家
				this.formInline.shopkeeper_id = this.formInline.role_id == 3 ? this.$route.query.shopkeeper_id : '';
				// 店铺业务员
				this.formInline.salesman_id = this.formInline.role_id == 6 ? this.$route.query.salesman_id : '';
				this.user_name = this.$route.query.user_name;
			},
			
			/**
			 * 获取店铺列表
			 */
			getShopList() {
				let self = this;
				this.$axios.get(this.$url + 'shop', {
					params: {
						role_id: this.formInline.role_id,
						shopkeeper_id: this.formInline.shopkeeper_id,
						salesman_id: this.formInline.salesman_id,
						shop_name: this.formInline.shop_name,
						status: this.formInline.status,
						is_commission: this.formInline.is_commission,
						page: this.listPagination.current_page,
						size: this.listPagination.per_page
					}
				})
				.then(function(res) {
					if (res.data.status == 1) {
						// 店铺列表分页参数
						self.listPagination = res.data.data;
						
						// 当数据为空时
						if (self.listPagination.total == 0) {
							self.$message({
								message: '数据不存在',
								type: 'warning'
							});
							return;
						}
						
						// 店铺列表
						self.shopList = self.listPagination.data;
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
				this.getShopList();
			},
			
			/**
			 * 分页 currentPage 改变时会触发
			 * @param {Object} current_page
			 */
			handleCurrentChange(current_page) {
				this.listPagination.current_page = current_page; // 当前页数
				this.getShopList();
			},
			
			/**
			 * 筛选状态
			 * @param {Object} value
			 * @param {Object} row
			 */
			filterStatus(value, row) {
				return row.status === value;
			},
			
			/**
			 * 统计店铺数据
			 */
			getShopCount() {
				let self = this;
				this.$axios.get(this.$url + 'shop_count', {
					params: {
						role_id: this.formInline.role_id,
						// shopkeeper_id: this.formInline.shopkeeper_id,
						salesman_id: this.formInline.salesman_id,
						// status: this.formInline.status,
						// is_commission: this.formInline.is_commission
					}
				})
				.then(function(res) {
					if (res.data.status == 1) {
						// 统计数据
						self.count = res.data.data;
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
			 * 跳转店铺编辑页
			 * @param {Object} row
			 */
			toShopEdit(row) {
				this.$router.push({path: "shop_edit", query: {shop_id: row.shop_id}});
			},
			
			/**
			 * 返回上一页
			 */
			back(){
				this.$router.go(-1);
			}
		}
	}
</script>

<style>
</style>
