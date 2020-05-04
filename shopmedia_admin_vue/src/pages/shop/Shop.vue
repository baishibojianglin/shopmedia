<template>
	<div class="shop">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>店家店铺列表</span></el-col>
					<el-col :span="15">
						<!-- 查询 s -->
						<el-form :inline="true" :model="formInline" size="mini" class="demo-form-inline">
							<el-form-item label="">
								<el-select v-model="formInline.status" placeholder="状态">
									<el-option v-for="(item, index) in {0: '禁用', 1: '启用'}" :key="index" :label="item" :value="Number(index)"></el-option>
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
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="24"><el-tag type="" effect="plain">店家：{{user_name}}</el-tag></el-col>
				</el-row>
				<!-- 用户信息 e -->
			</div>
			<div class="">
				<!-- 店铺列表 s -->
				<el-table :data="shopList" empty-text="数据加载中…" max-height="500" border style="width: 100%">
					<el-table-column prop="shop_id" label="序号" fixed width="50"></el-table-column>
					<el-table-column prop="shop_name" label="店铺名称" fixed width="120"></el-table-column>
					<el-table-column prop="address" label="详细地址" width="180" show-overflow-tooltip></el-table-column>
					<el-table-column label="店铺区域" header-align="center">
						<el-table-column prop="province" label="省份" width="120"></el-table-column>
						<el-table-column prop="city" label="城市" width="120"></el-table-column>
						<el-table-column prop="county" label="区县" width="120"></el-table-column>
						<el-table-column prop="town" label="乡镇街道" width="120"></el-table-column>
					</el-table-column>
					<el-table-column prop="status" label="状态" width="90" :filters="[{ text: '禁用', value: 0 }, { text: '启用', value: 1 }]" :filter-method="filterStatus" filter-placement="bottom-end">
						<template slot-scope="scope">
							<span v-for="(item, index) in {0: 'text-info', 1: 'text-success'}" :key="index" v-if="scope.row.status == index" :class="item">{{scope.row.status_msg}}</span>
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
					shopkeeper_id: '', // 店家ID
					shop_name: '', // 店铺名称
					status: '' // 店铺状态
				},
				shopList: [], // 店铺列表
				listPagination: {}, // 列表分页参数
				
				user_name: '' // 用户名称
			}
		},
		mounted() {
			this.getParams();
			this.getShopList(); // 获取店铺列表
		},
		methods: {
			/**
			 * 获取路由带过来的参数
			 */
			getParams() {
				this.formInline.shopkeeper_id = this.$route.query.shopkeeper_id;
				this.user_name = this.$route.query.user_name;
			},
			
			/**
			 * 获取店铺列表
			 */
			getShopList() {
				let self = this;
				this.$axios.get(this.$url + 'shop', {
					params: {
						shopkeeper_id: this.formInline.shopkeeper_id,
						shop_name: this.formInline.shop_name,
						status: this.formInline.status,
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
