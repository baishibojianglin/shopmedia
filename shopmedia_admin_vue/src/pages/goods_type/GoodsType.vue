<template>
	<div class="goods_cate">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>商品分类列表</span></el-col>
					<el-col :span="6">
						<!-- 查询 s -->
						<!-- <el-form :inline="true" :model="formInline" size="mini" class="demo-form-inline">
							<el-form-item label="">
								<el-input placeholder="查询商品分类" v-model="formInline.type_name" clearable>
									<el-button slot="append" icon="el-icon-search" @click="getGoodsTypeList()"></el-button>
								</el-input>
							</el-form-item>
						</el-form> -->
						<!-- 查询 e -->
					</el-col>
					<el-col :span="6">
						<!-- 新增 s -->
						<router-link to="goods_type_create">
							<el-button size="mini" icon="el-icon-plus">新增分类</el-button>
						</router-link>
						<!-- 新增 e -->
					</el-col>
					<el-col :span="6">
						<el-button size="mini" icon="el-icon-back" title="返回顶级分类" @click="getTopGoodsCateList()" v-if="isBack">顶级分类</el-button>
						<el-button size="mini" icon="el-icon-back" title="返回上级分类" @click="getParentGoodsCateList(grandparentId)" v-if="isBack">上级分类</el-button>
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- 商品分类列表 s -->
				<el-table :data="goodsTypeList" border style="width: 100%">
					<el-table-column prop="id" label="序号" fixed width="90"></el-table-column>
					<el-table-column prop="type_name" label="分类名称" fixed min-width="180"></el-table-column>
					<el-table-column prop="parent_id" label="上级序号" width="90">
						<template slot-scope="scope">
							{{scope.row.parent_id == 0 ? '（无）' : scope.row.parent_id}}
						</template>
					</el-table-column>
					<el-table-column prop="parent_name" label="上级分类" width="180"></el-table-column>
					<el-table-column prop="status" label="状态" width="90" :filters="[{ text: '禁用', value: 0 }, { text: '启用', value: 1 }]"
					 :filter-method="filterStatus" filter-placement="bottom-end">
						<template slot-scope="scope">
							<el-tag :type="scope.row.status === 0 ? 'info' : (scope.row.status === 1 ? 'success' : 'danger')" size="mini">{{scope.row.status_msg}}</el-tag>
						</template>
					</el-table-column>
					<el-table-column label="操作" fixed="right" min-width="350">
						<template slot-scope="scope">
							<el-button type="primary" size="mini" icon="el-icon-edit" circle @click="toGoodsTypeEdit(scope.row)"></el-button>
							<el-button type="danger" size="mini" icon="el-icon-close" circle @click="disableGoodsType(scope.row)" v-if="scope.row.status == 1"></el-button>
							<el-button type="success" size="mini" icon="el-icon-check" circle @click="disableGoodsType(scope.row)" v-if="scope.row.status == 0"></el-button>
						</template>
					</el-table-column>
				</el-table>
				<!-- 商品分类列表 e -->

				<!-- 分页 s -->
				<div>
					<el-pagination background :page-sizes="[5, 10, 15, 20]" :page-size="listPagination.per_page" :total="listPagination.total"
					 :current-page="listPagination.current_page" layout="total, sizes, prev, pager, next, jumper" @size-change="handleSizeChange"
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
					type_name: '', // 商品分类名称
					status: 1	//分类状态
				},
				goodsTypeList: [], // 商品分类列表，如 [{id: 1, type_name: '油盐酱醋茶', parent_id: 0, audit_status: 0, audit_status_msg: '待审核'}, {…}, …]
				listPagination: {}, // 列表分页参数
				grandparentId: '', // 上上级ID
				parentId: 0, // 上级ID，默认为 0 查看一级分类
				isBack: false, // 是否显示返回按钮

			}
		},
		mounted() {
			this.getGoodsTypeList(); // 获取商品分类列表
		},
		methods: {
			/**
			 * 获取商品分类列表
			 */
			getGoodsTypeList() {
				let self = this;

				this.$axios.get(this.$url + 'goods_type', {
						params: {
							type_name: this.formInline.type_name,
							page: this.listPagination.current_page,
							size: this.listPagination.per_page
						},
						headers: {
							'admin-user-id': JSON.parse(localStorage.getItem('admin_user')).user_id,
							'admin-user-token': JSON.parse(localStorage.getItem('admin_user')).token
						}
					})
					.then(function(res) {
						if (res.data.status == 1) {
							// 商品分类列表分页参数
							self.listPagination = res.data.data;

							// 当数据为空时
							if (self.listPagination.total == 0) {
								self.$message({
									message: '数据不存在',
									type: 'warning'
								});
								return;
							}

							// 商品分类列表
							let goodsTypeList = self.listPagination.data;
							goodsTypeList.forEach((item, index) => {
								if (index == 0) { // 0表示第1条数据，因每一条数据的上上级ID都相同
									self.grandparentId = item.grandparent_id; // 上上级ID是否存在时赋值
									return;
								}
							});
							self.goodsTypeList = goodsTypeList;
						} else {
							self.$message({
								message: '网络忙，请重试',
								type: 'warning'
							});
						}
					})
					.catch(function(error) {
						// 错误处理
						if (error.response) {
							console.log(error.response.data);
							console.log(error.response.status);
							console.log(error.response.headers);
						} else if (error.request) {
							console.log('error.request', error.request)
						} else {
							console.log('error.message', error.message)
						}
						console.log('error.config', error.config)

						self.$message({
							message: error.response.data.message,
							type: 'warning'
						});
					});
			},

			/**
			 * 获取下级商品分类列表
			 * @param {Object} row
			 */
			getSonGoodsCateList(row) {
				this.parentId = row.id;
				if (this.parentId) {
					this.formInline.type_name = '';
					this.listPagination.current_page = 1;
					this.getGoodsTypeList();
				}
			},

			/**
			 * 返回顶级商品分类列表
			 */
			getTopGoodsCateList() {
				this.formInline.type_name = '';
				this.parentId = 0;
				this.getGoodsTypeList();
			},

			/**
			 * 返回上级商品分类列表
			 * @param {Object} grandparentId
			 */
			getParentGoodsCateList(grandparentId) {
				this.parentId = grandparentId;
				this.getGoodsTypeList();
			},
			/**
			 * 分页 pageSize 改变时会触发
			 * @param {Object} page_size
			 */
			handleSizeChange(page_size) {
				this.listPagination.per_page = page_size; // 每页条数
				this.getGoodsTypeList();
			},

			/**
			 * 分页 currentPage 改变时会触发
			 * @param {Object} current_page
			 */
			handleCurrentChange(current_page) {
				this.listPagination.current_page = current_page; // 当前页数
				this.getGoodsTypeList();
			},
			/**
			 * 筛选活动状态
			 * @param {Object} value
			 * @param {Object} row
			 */
			filterStatus(value, row) {
				return row.status === value;
			},

			/**
			 * 跳转商品分类编辑页
			 * @param {Object} row
			 */
			toGoodsTypeEdit(row) {
				this.$router.push({
					path: "goods_type_edit",
					query: {
						id: row.id,
						type_name:row.type_name,
						parent_id:row.parent_id
					}
				});
			},

			/**
			 * 禁用商品分类
			 * @param {Object} scope
			 */
			disableGoodsType(row) {
				let message = row.status == 1 ? '是否禁用该分类?' : '是否启用该分类？';
				this.$confirm(message, '禁用', {
					confirmButtonText: '确定',
					cancelButtonText: '取消',
					type: 'warning'
				}).then(() => {
					// 调用禁用接口
					let self = this;
					this.$axios.delete(this.$url + 'goods_type/' + row.id)
						.then(function(res) {
							
							if (res.data.status == 1) {
								if(res.data.data.status == 1){ // 修改后的分类状态为启用
									row.status = 1;
									row.status_msg = '启用';
								} else { // 禁用
									row.status = 0;
									row.status_msg = '禁用';
								}
							}
							
							let type = res.data.status == 1 ? 'success' : 'warning';
							self.$message({
								message: res.data.message,
								type: type
							});
						})
						.catch(function(error) {
							self.$message({
								message: error.response.data.message,
								type: 'warning'
							});
						});
				}).catch(() => {
					this.$message({
						type: 'info',
						message: '已取消禁用'
					});
				});
			},


		}
	}
</script>

<style>
</style>
