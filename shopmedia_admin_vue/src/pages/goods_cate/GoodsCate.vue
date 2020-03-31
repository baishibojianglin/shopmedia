<template>
	<div class="goods_cate">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>商品类别列表</span></el-col>
					<el-col :span="6">
						<!-- 查询 s -->
						<el-form :inline="true" :model="formInline" size="mini" class="demo-form-inline">
							<el-form-item label="">
								<el-input placeholder="查询商品类别" v-model="formInline.cate_name" clearable>
									<el-button slot="append" icon="el-icon-search" @click="getGoodsCateList()"></el-button>
								</el-input>
							</el-form-item>
						</el-form>
						<!-- 查询 e -->
					</el-col>
					<el-col :span="6">
						<!-- 新增 s -->
						<router-link to="goodscateadd"><el-button size="mini" icon="el-icon-plus">新增类别</el-button></router-link>
						<!-- 新增 e -->
					</el-col>
					<el-col :span="6">
						<el-button size="mini" icon="el-icon-back" title="返回顶级类别" @click="getTopGoodsCateList()" v-if="isBack">顶级类别</el-button>
						<el-button size="mini" icon="el-icon-back" title="返回上级类别" @click="getParentGoodsCateList(grandparentId)" v-if="isBack">上级类别</el-button>
						<!-- <el-button size="mini" icon="el-icon-refresh" title="刷新" @click="getGoodsCateList(parentId)">刷新</el-button> -->
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- 商品类别列表 s -->
				<el-table :data="goodsCateList" border style="width: 100%">
					<el-table-column prop="cate_id" label="序号" fixed width="90"></el-table-column>
					<el-table-column prop="cate_name" label="类别名称" fixed min-width="180"></el-table-column>
					<el-table-column prop="parent_id" label="上级序号" width="90">
						<template slot-scope="scope">
							{{scope.row.parent_id == 0 ? '（无）' : scope.row.parent_id}}
						</template>
					</el-table-column>
					<el-table-column prop="parent_name" label="上级类别" width="180"></el-table-column>
					<!-- <el-table-column prop="grandparent_id" label="上上级ID" width="100"></el-table-column> -->
					<el-table-column prop="is_on_sale_msg" label="是否上架" width="90" :filters="[{ text: '下架', value: 0 }, { text: '上架', value: 1 }]" :filter-method="filterIsOnSale" filter-placement="bottom-end"></el-table-column>
					<el-table-column prop="audit_status" label="审核状态" width="90" :filters="[{ text: '待审核', value: 0 }, { text: '正常', value: 1 }, { text: '驳回', value: 2 }]" :filter-method="filterAuditStatus" filter-placement="bottom-end">
						<template slot-scope="scope">
							<el-tag :type="scope.row.audit_status === 0 ? 'info' : (scope.row.audit_status === 1 ? 'success' : 'danger')" size="mini">{{scope.row.audit_status_msg}}</el-tag>
						</template>
					</el-table-column>
					<el-table-column prop="audit_time" label="审核时间" width="180"></el-table-column>
					<el-table-column label="操作" fixed="right" min-width="350">
						<template slot-scope="scope">
							<el-button type="warning" size="mini" plain @click="isOnSale(scope)">{{scope.row.is_on_sale ? '下架' : '上架'}}</el-button>
							<el-button type="primary" size="mini" plain @click="dialogFormVisible = true; form.cate_id = scope.row.cate_id; tableRowIndex = scope.$index">审核</el-button>
							<el-button size="mini" plain @click="getSonGoodsCateList(scope.row)">下级</el-button>
							<el-button type="primary" size="mini" plain @click="toGoodsCateEdit(scope.row)">编辑</el-button>
							<el-button type="danger" size="mini" plain @click="deleteGoodsCate(scope)">删除</el-button>
						</template>
					</el-table-column>
				</el-table>
				<!-- 商品类别列表 e -->
				
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
				
				<!-- 审核商品类别 Dialog 对话框 s，放在“审核”按钮后边交互效果差 -->
				<el-dialog title="审核" :visible.sync="dialogFormVisible" width="30%" :destroy-on-close="true">
					<el-form ref="ruleForm" :model="form" :rules="rules" size="small">
						<el-form-item label="审核状态" prop="audit_status" label-width="120px">
							<el-select v-model="form.audit_status" placeholder="请选择…">
								<el-option :key="1" label="通过" :value="1"></el-option>
								<el-option :key="2" label="驳回" :value="2"></el-option>
							</el-select>
						</el-form-item>
					</el-form>
					<div slot="footer" class="dialog-footer">
						<el-button size="small" plain @click="dialogFormVisible = false">取 消</el-button>
						<el-button type="primary" size="small" plain @click="auditGoodsCate('ruleForm')">确 定</el-button>
					</div>
				</el-dialog>
				<!-- 审核商品类别 Dialog 对话框 e -->
			</div>
		</el-card>
	</div>
</template>

<script>
	export default {
		data() {
			return {
				formInline: {
					cate_name: '' // 商品类别名称
				},
				goodsCateList: [], // 商品类别列表，如 [{cate_id: 1, cate_name: '油盐酱醋茶', parent_id: 0, audit_status: 0, audit_status_msg: '待审核'}, {…}, …]
				listPagination: {}, // 列表分页参数
				grandparentId: '', // 上上级ID
				parentId: 0, // 上级ID，默认为 0 查看一级类别
				isBack: false, // 是否显示返回按钮
				
				/* Dialog 对话框 s */
				dialogFormVisible: false, // 是否显示 Dialog
				form: { // Dialog 嵌套审核商品类别操作的表单数据对象
					cate_id: '',
					audit_status: ''
				},
				rules: { // 验证规则
					audit_status: [
						{ required: true, message: '请选择审核状态', trigger: 'change' }
					]
				},
				tableRowIndex: '' // 表格行序号
				/* Dialog 对话框 e */
			}
		},
		mounted() {
			this.getGoodsCateList(); // 获取商品类别列表
		},
		methods: {
			/**
			 * 获取商品类别列表
			 */
			getGoodsCateList() {
				let self = this;
				this.parentId = this.formInline.cate_name ? '' : this.parentId; // 查询时不区分商品类别等级
				this.isBack = this.parentId ? true : false; // 非一级商品类别时显示返回按钮
				
				this.$axios.get(this.$url + 'goods_cate', {
					params: {
						cate_name: this.formInline.cate_name,
						parent_id: this.parentId,
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
						// 商品类别列表分页参数
						self.listPagination = res.data.data;
						
						// 当数据为空时
						if (self.listPagination.total == 0) {
							self.$message({
								message: '数据不存在',
								type: 'warning'
							});
							return;
						}
						
						// 商品类别列表
						let goodsCateList = self.listPagination.data;
						goodsCateList.forEach((item, index) => {
							if (index == 0) { // 0表示第1条数据，因每一条数据的上上级ID都相同
								self.grandparentId = item.grandparent_id; // 上上级ID是否存在时赋值
								return;
							}
						});
						self.goodsCateList = goodsCateList;
					} else {
						self.$message({
							message: '网络忙，请重试',
							type: 'warning'
						});
					}
				})
				.catch(function (error) {
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
			 * 获取下级商品类别列表
			 * @param {Object} row
			 */
			getSonGoodsCateList(row) {
				this.parentId = row.cate_id;
				if (this.parentId) {
					this.formInline.cate_name = '';
					this.listPagination.current_page = 1;
					this.getGoodsCateList();
				}
			},
			
			/**
			 * 返回顶级商品类别列表
			 */
			getTopGoodsCateList() {
				this.formInline.cate_name = '';
				this.parentId = 0;
				this.getGoodsCateList();
			},
			
			/**
			 * 返回上级商品类别列表
			 * @param {Object} grandparentId
			 */
			getParentGoodsCateList(grandparentId) {
				this.parentId = grandparentId;
				this.getGoodsCateList();
			},
			
			/**
			 * 分页 pageSize 改变时会触发
			 * @param {Object} page_size
			 */
			handleSizeChange(page_size) {
				this.listPagination.per_page = page_size; // 每页条数
				this.getGoodsCateList();
			},
			
			/**
			 * 分页 currentPage 改变时会触发
			 * @param {Object} current_page
			 */
			handleCurrentChange(current_page) {
				this.listPagination.current_page = current_page; // 当前页数
				this.getGoodsCateList();
			},
			
			/**
			 * 筛选商品类别审核状态
			 * @param {Object} value
			 * @param {Object} row
			 */
			filterAuditStatus(value, row) {
				return row.audit_status === value;
			},
			
			/**
			 * 审核商品类别
			 * @param {Object} formName
			 */
			auditGoodsCate(formName) {
				let self = this;
				this.$refs[formName].validate((valid) => {
					if (valid) {
						this.$axios.put(this.$url + 'goods_cate/' + this.form.cate_id, {
							// 参数
							audit_status: this.form.audit_status
						}, {
							// 请求头配置
							headers: {
									'admin-user-id': JSON.parse(localStorage.getItem('admin_user')).user_id,
									'admin-user-token': JSON.parse(localStorage.getItem('admin_user')).token
							}
						})
						.then(function(res) {
							let type = res.data.status == 1 ? 'success' : 'warning';
							self.$message({
								message: res.data.message,
								type: type
							});
							self.goodsCateList[self.tableRowIndex].audit_status = self.form.audit_status; // 静态改变审核状态
							self.goodsCateList[self.tableRowIndex].audit_status_msg = (self.form.audit_status === 0 ? '待审核' : (self.form.audit_status === 1 ? '正常' : '驳回')); // 静态改变审核状态信息
							self.dialogFormVisible = false; // 隐藏 Dialog 对话框
						})
						.catch(function (error) {
							self.$message({
								message: error.response.data.message,
								type: 'warning'
							});
						});
					} else {
						self.$message({
							message: 'error submit!!',
							type: 'warning',
						});
						return false;
					}
				});
			},
			
			/**
			 * 跳转商品类别编辑页
			 * @param {Object} row
			 */
			toGoodsCateEdit(row) {
				this.$router.push({path: "goodscateedit", query: {cate_id: row.cate_id, cate_name: row.cate_name, parent_id: row.parent_id}});
			},
			
			/**
			 * 删除商品类别
			 * @param {Object} scope
			 */
			deleteGoodsCate(scope) {
				this.$confirm('此操作将永久删除该商品类别, 是否继续?', '删除', {
					confirmButtonText: '确定',
					cancelButtonText: '取消',
					type: 'warning'
				}).then(() => {
					// 调用删除接口
					let self = this;
					this.$axios.delete(this.$url + 'goods_cate/' + scope.row.cate_id)
					.then(function(res) {
						// 移除元素
						self.goodsCateList.splice(scope.$index, 1);
						
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
			},
			
			/**
			 * 筛选上下架状态
			 * @param {Object} value
			 * @param {Object} row
			 */
			filterIsOnSale(value, row) {
				return row.is_on_sale === value;
			},
			
			/**
			 * 上下架
			 */
			isOnSale(scop) {
				let self = this;
				let is_on_sale = scop.row.is_on_sale;
				this.$axios.put(this.$url + 'goods_cate/' + scop.row.cate_id, {
					// 参数
					is_on_sale: is_on_sale
				}, {
					// 请求头配置
					headers: {
						'admin-user-id': JSON.parse(localStorage.getItem('admin_user')).user_id,
						'admin-user-token': JSON.parse(localStorage.getItem('admin_user')).token
					}
				})
				.then(function(res) {
					let type = res.data.status == 1 ? 'success' : 'warning';
					self.$message({
						message: res.data.status == 1 ? (is_on_sale === 1 ? '已下架' : '已上架') : res.data.message,
						type: type
					});
					scop.row.is_on_sale = is_on_sale === 1 ? 0 : 1; // 静态改变上下架状态
					scop.row.is_on_sale_msg = is_on_sale === 1 ? '下架' : '上架'; // 静态改变上下架状态信息
				})
				.catch(function (error) {
					self.$message({
						message: error.response.data.message,
						type: 'warning'
					});
				});
			}
		}
	}
</script>

<style>
</style>
