<template>
	<div class="partner_device">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>合作广告屏</span></el-col>
					<el-col :span="6">
						<!-- 查询 s -->
						<el-form :inline="true" :model="formInline" size="mini" class="demo-form-inline">
							<el-form-item label="">
								<el-input placeholder="设备品牌、型号" v-model="formInline.keywords" clearable>
									<el-button slot="append" icon="el-icon-search" @click="getUserDeviceList()">查询</el-button>
								</el-input>
							</el-form-item>
						</el-form>
						<!-- 查询 e -->
					</el-col>
					<!-- <el-col :span="12"> -->
						<!-- 新增 s -->
						<!-- <router-link to="user_add"><el-button size="mini" icon="el-icon-plus">新增用户</el-button></router-link> -->
						<!-- 新增 e -->
					<!-- </el-col> -->
					<el-col :span="3" :offset="9">
						<el-button size="mini" icon="el-icon-back" title="返回" @click="back()">返回</el-button>
					</el-col>
				</el-row>
				<!-- 用户信息 s -->
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="24"><el-tag type="" effect="plain">广告屏合作商：{{user_name}}</el-tag></el-col>
				</el-row>
				<!-- 用户信息 e -->
			</div>
			<div class="">
				<!-- 用户（广告屏合作商）设备列表 s -->
				<el-table :data="userDeviceList" border style="width: 100%">
					<el-table-column prop="device_id" label="序号" fixed width="90"></el-table-column>
					<el-table-column prop="brand" label="品牌" min-width="120"></el-table-column>
					<el-table-column prop="model" label="型号" width="120"></el-table-column>
					<el-table-column prop="size" label="尺寸" width="120"></el-table-column>
					<el-table-column prop="sale_price" label="出售价格/元" width="120"></el-table-column>
					<el-table-column prop="share" label="用户份额" width="120">
						<template slot-scope="scope">
							{{scope.row.share * 100 + '%'}}
						</template>
					</el-table-column>
					<el-table-column prop="company_name" label="设备分公司" width="120"></el-table-column>
					<el-table-column label="设备区域" header-align="center">
						<el-table-column prop="province" label="省份" width="120"></el-table-column>
						<el-table-column prop="city" label="城市" width="120"></el-table-column>
						<el-table-column prop="county" label="区县" width="120"></el-table-column>
						<el-table-column prop="street" label="街道" width="120"></el-table-column>
					</el-table-column>
					<el-table-column prop="shopname" label="店铺" width="120"></el-table-column>
					<el-table-column label="广告收益/元" header-align="center">
						<el-table-column prop="today_income" label="今日收益" width="120"></el-table-column>
						<el-table-column prop="total_income" label="累计收益" width="120"></el-table-column>
					</el-table-column>
					<el-table-column prop="status" label="状态" width="90" :filters="[{ text: '禁用', value: 0 }, { text: '正常', value: 1 }]" :filter-method="filterStatus" filter-placement="bottom-end">
						<template slot-scope="scope">
							<span :class="scope.row.status === 1 ? 'text-success' : 'text-info'" size="mini">{{scope.row.status_msg}}</span>
						</template>
					</el-table-column>
					<el-table-column label="操作" fixed="right" min-width="130">
						<template slot-scope="scope">
							<el-button type="primary" size="mini" plain @click="toUserDeviceEdit(scope.row)">编辑用户份额</el-button>
							<!-- <el-button type="danger" size="mini" plain @click="deleteUser(scope)">删除</el-button> -->
						</template>
					</el-table-column>
				</el-table>
				<!-- 用户（广告屏合作商）设备列表 e -->

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
					keywords: '' // 查询关键词
				},
				userDeviceList: [], // 用户（广告屏合作商）设备列表
				listPagination: {} ,// 列表分页参数
				
				user_id: '',
				user_name: ''
			}
		},
		mounted() {
			this.getParams();
			this.getUserDeviceList(); // 获取用户（广告屏合作商）设备列表
		},
		methods: {
			/**
			 * 获取路由带过来的参数
			 */
			getParams() {
				this.user_id = this.$route.query.user_id;
				this.user_name = this.$route.query.user_name;
			},
			
			/**
			 * 获取用户（广告屏合作商）设备列表
			 */
			getUserDeviceList() {
				let self = this;
				this.$axios.get(this.$url + 'partner_device', {
					params: {
						user_id: this.user_id,
						keywords: this.formInline.keywords,
						page: this.listPagination.current_page,
						size: this.listPagination.per_page
					}/* ,
					headers: {
						'admin-user-id': JSON.parse(localStorage.getItem('admin_user')).id,
						'admin-user-token': JSON.parse(localStorage.getItem('admin_user')).token
					} */
				})
				.then(function(res) {
					if (res.data.status == 1) {
						// 用户（广告屏合作商）设备列表分页参数
						self.listPagination = res.data.data;

						// 当数据为空时
						if (self.listPagination.total == 0) {
							self.$message({
								message: '数据不存在',
								type: 'warning'
							});
							return;
						}

						// 用户（广告屏合作商）设备列表
						self.userDeviceList = self.listPagination.data;
					} else {
						self.$message({
							message: '网络忙，请重试',
							type: 'warning'
						});
					}
				})
				.catch(function(error) {
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
				this.getUserDeviceList();
			},

			/**
			 * 分页 currentPage 改变时会触发
			 * @param {Object} current_page
			 */
			handleCurrentChange(current_page) {
				this.listPagination.current_page = current_page; // 当前页数
				this.getUserDeviceList();
			},

			/**
			 * 筛选用户状态
			 * @param {Object} value
			 * @param {Object} row
			 */
			filterStatus(value, row) {
				return row.status === value;
			},

			/**
			 * 跳转用户设备编辑页
			 * @param {Object} row
			 */
			toUserDeviceEdit(row) {
				this.$router.push({
					path: "partner_device_edit",
					query: {
						partner_device_id: row.partner_device_id,
						user_id: this.user_id,
						user_name: this.user_name,
						device_id: row.device_id,
						share: row.share
					}
				});
			},

			/**
			 * 删除用户
			 * @param {Object} scope
			 */
			deleteUser(scope) {
				this.$confirm('此操作将永久删除该用户, 是否继续?', '删除', {
					confirmButtonText: '确定',
					cancelButtonText: '取消',
					type: 'warning'
				}).then(() => {
					// 调用删除接口
					let self = this;
					this.$axios.delete(this.$url + 'user_partner/' + scope.row.user_id)
						.then(function(res) {
							// 移除元素
							self.userDeviceList.splice(scope.$index, 1);

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
						message: '已取消删除'
					});
				});
			},
			
			/**
			 * 返回上一页
			 */
			back(){
				this.$router.go(-1);
			},
		}
	}
</script>

<style>
</style>
