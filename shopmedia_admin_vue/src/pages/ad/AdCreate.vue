<template>
	<div class="ad_create">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>新增广告</span></el-col>
					<el-col :span="3">
						<el-button size="mini" icon="el-icon-back" title="返回" @click="back()">返回</el-button>
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- Form 表单 s -->
				<el-form ref="ruleForm" :model="form" :rules="rules" label-width="200px" size="small" class="demo-form-inline">
					<el-form-item prop="is_ad_combo" label="是否广告套餐">
						<el-radio-group v-model="form.is_ad_combo">
							<el-radio :label="1">广告套餐</el-radio>
							<el-radio :label="0">普通投放</el-radio>
						</el-radio-group>
					</el-form-item>
					<el-form-item prop="ad_name" label="广告名称">
						<el-input v-model="form.ad_name" placeholder="输入广告名称" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="ad_cate_id" label="广告类别">
						<!-- TODO：封装公共 ad-cate-select 组件 -->
						<!-- <ad-cate-select :value="form.ad_cate_id"></ad-cate-select> -->
						<el-select v-model="form.ad_cate_id" placeholder="请选择…" clearable filterable @change="selectAdCateChange">
							<el-option v-for="item in adCateList" :key="item.cate_id" :label="item.cate_name" :value="item.cate_id"></el-option>
						</el-select>
					</el-form-item>
					<el-form-item prop="ad_datetime" label="投放时间">
						<el-date-picker v-model="form.ad_datetime" type="datetimerange" range-separator="至" start-placeholder="开始日期" end-placeholder="结束日期"></el-date-picker>
					</el-form-item>
					<el-form-item prop="ad_time" label="每日播放时间段">
						<el-time-picker is-range v-model="form.ad_time" range-separator="至" start-placeholder="开始时间" end-placeholder="结束时间"
						 placeholder="选择时间范围">
						</el-time-picker>
					</el-form-item>
					<el-form-item prop="play_times" label="每日播放次数">
						<el-input-number v-model="form.play_times" :min="0" :step="1" :precision="0" controls-position="right"></el-input-number>
					</el-form-item>
					<el-form-item prop="advertiser" label="广告主名称">
						<el-input v-model="form.advertiser" placeholder="输入广告主名称" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="phone" label="广告主电话">
						<el-input v-model="form.phone" placeholder="输入广告主联系电话" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="phone" label="广告主业务员">
						<el-select v-model="form.salesman_id" placeholder="请选择…" clearable filterable>
							<el-option v-for="item in salesmanList" :key="item.id" :label="item.user_name + ' ' +item.phone" :value="item.id"></el-option>
						</el-select>
					</el-form-item>
					<el-form-item prop="region_ids" label="投放区域">
						<!-- Tree 树形控件（可选择层级） s -->
						<el-tree ref="tree" empty-text="数据加载中…" node-key="region_id" :props="props" :load="loadNode" lazy show-checkbox @check="handleCheck"></el-tree>
						<!-- Tree 树形控件 e -->
					</el-form-item>
					
					<el-form-item v-if="false" prop="shop_cate_ids" label="投放店铺类别">
						<!-- TODO：封装公共 shop-cate-select 组件 -->
						<!-- <shop-cate-select :value="form.shop_cate_ids"></shop-cate-select> -->
						<el-select v-model="form.shop_cate_ids" multiple placeholder="请选择…" clearable filterable @change="selectShopCateChange">
							<el-option v-for="item in shopCateList" :key="item.cate_id" :label="item.cate_name" :value="item.cate_id"></el-option>
						</el-select>
					</el-form-item>
					
					<el-form-item prop="device_ids" label="投放广告屏">
						<el-checkbox v-model="checkAll" :indeterminate="isIndeterminate" @change="handleCheckAllChange" v-if="deviceList.length != 0 ? true : false">全选</el-checkbox>
						<el-checkbox-group v-model="form.device_ids" @change="handleCheckedDeviceChange">
							<el-checkbox v-for="item in deviceList" :label="item.device_id" :key="item.device_id" border>{{'广告屏：' + item.device_id + '，' + '【' + item.ad_cate_name + '】' + '店铺：' + item.shop_name + '，' + '地址：' + item.address}}<!-- {{'品牌：' + item.brand + '，型号：' + item.model + '，尺寸：' + item.model}} --></el-checkbox>
						</el-checkbox-group>
					</el-form-item>
					<el-form-item prop="ad_price" label="广告价格/元">
						<el-input-number v-model="form.ad_price" :min="0" :step="1" :precision="2" controls-position="right"></el-input-number>
					</el-form-item>
					<el-form-item prop="discount_ratio" label="广告折扣率">
						<el-input-number v-model="form.discount_ratio" :min="0" :max="1" :step="0.1" :precision="2" controls-position="right"></el-input-number>（设置为1时表示无折扣）
					</el-form-item>
					<el-form-item prop="is_show" label="是否显示">
						<el-radio-group v-model="form.is_show">
							<el-radio :label="1">显示</el-radio>
							<el-radio :label="0">不显示</el-radio>
						</el-radio-group>
					</el-form-item>
					<el-form-item prop="sort" label="排序">
						<el-input-number v-model="form.sort" :min="0" :step="1" :precision="0" controls-position="right"></el-input-number>
					</el-form-item>
					<el-form-item>
						<el-button type="primary" plain @click="submitForm('ruleForm')">提交</el-button>
						<el-button plain @click="resetForm('ruleForm')">重置</el-button>
					</el-form-item>
				</el-form>
				<!-- Form 表单 e -->
			</div>
		</el-card>
	</div>
</template>

<script>
	// import adCateSelect from '@/pages/ad_cate/ad-cate-select.vue';
	// import shopCateSelect from '@/pages/shop_cate/shop-cate-select.vue';

	export default {
		components: {
			// adCateSelect,
			// shopCateSelect
		},
		data() {
			return {
				form: {
					is_ad_combo: '', // 是否广告套餐
					ad_name: '', // 广告名称
					ad_cate_id: '', // 广告类别ID
					ad_price: '', // 广告价格
					discount_ratio: 1, // 广告折扣率
					region_ids: [], // 区域ID集合（数组）
					shop_cate_ids: [], // 投放店铺类别ID集合
					device_ids: [], // 投放广告设备ID集合
					advertiser: '', // 广告主名称
					phone: '', // 广告主电话
					salesman_id: '' // 广告主业务员
					// …
				},
				rules: { // 验证规则
					is_ad_combo: [{required: true, message: '请选择是否广告套餐', trigger: 'change'}],
					ad_name: [
						{required: true, message: '请输入广告名称', trigger: 'blur'},
						{min: 1, max: 20, message: '长度在 1 到 20 个字符', trigger: 'blur'}
					],
					ad_cate_id: [{required: true, message: '请选择广告类别', trigger: 'change'}],
					ad_price: [{required: true, message: '请输入广告价格', trigger: 'blur'}],
					ad_datetime: [{ /* type: 'date', */ required: true, message: '请选择投放时间', trigger: 'change'}],
					ad_time: [{ /* type: 'date', */ required: true, message: '每日播放时间段', trigger: 'change'}],
					play_times: [{required: true, message: '请输入每日播放次数', trigger: 'blur'}],
					advertiser: [
						{required: true, message: '请输入广告主名称', trigger: 'blur'},
						{min: 2, max: 20, message: '长度在 2 到 20 个字符', trigger: 'blur'}
					],
					phone: [{required: true, pattern: /^1[34578]\d{9}$/, message: '目前只支持中国大陆的手机号码', trigger: 'blur'}],
					salesman_id: [{required: true, message: '请选择广告主业务员', trigger: 'change'}],
					region_ids: [{required: true, message: '请选择投放区域', trigger: 'change'}],
					// shop_cate_ids: [{required: true, message: '请选择投放店铺类别', trigger: 'change'}],
					device_ids: [{required: true, message: '请选择投放广告屏', trigger: 'change'}]
				},
				
				salesmanList: [], // 广告主业务员列表

				adCateList: [], // 广告类别列表
				shopCateList: [], // 店铺类别列表
				deviceList: [], // 设备列表

				// 区域 Tree 树形数据
				props: {
					label: 'region_name',
					isLeaf: 'leaf'
				},
				
				// 投放广告屏复选框全选效果
				checkAll: false,
				isIndeterminate: false
			}
		},
		created() {
			this.getSalesmanList();
			this.getAdCateList(); // 获取广告类别列表
			this.getShopCateList(); // 获取店铺类别列表
		},
		methods: {
			/**
			 * 获取广告主业务员列表
			 */
			getSalesmanList() {
				let self = this;
				this.$axios.get(this.$url + 'get_salesman_list', {
						params: {
							role_id: 5 //广告主业务员角色ID
						}
					})
					.then(function(res) {
						if (res.data.status == 1) {
							// 广告主业务员列表
							self.salesmanList = res.data.data;
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
			 * 获取广告类别列表
			 */
			getAdCateList() {
				let self = this;
				this.$axios.get(this.$url + 'ad_cate_list')
					.then(function(res) {
						if (res.data.status == 1) {
							// 广告类别列表
							self.adCateList = res.data.data;
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
			 * 获取店铺类别列表
			 */
			getShopCateList() {
				let self = this;
				this.$axios.get(this.$url + 'ad_cate_list')  // shop_cate_list
					.then(function(res) {
						if (res.data.status == 1) {
							// 店铺类别列表
							self.shopCateList = res.data.data;
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
			 * 懒加载（区域） Tree 树形数据
			 * @param {Object} node
			 * @param {Object} resolve
			 */
			loadNode(node, resolve) {
				let self = this;

				// 首次进入查询第一级
				let parent_id = 0;
				let level = 1;
				if (node.data) { // 逐级查询
					parent_id = node.data.region_id;
					level = node.data.level + 1;
				}

				this.$axios.get(this.$url + 'lazy_load_region_tree', {
						params: {
							parent_id: parent_id //父级ID
						}
					}).then(function(res) {
						if (res.data.status == 1) {
							const data = res.data.data;
							data.forEach((value, index) => {
								// 当不存在子级时，指定节点为叶子节点
								if (value.children_count == 0) {
									value.leaf = true;
								}
							})

							setTimeout(() => {
								resolve(data);
							}, 500);
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
			 * 当（区域）复选框被点击的时候触发
			 * @param {Object} data
			 * @param {Object} checkedObj
			 */
			handleCheck(data, checkedObj) {
				// console.log('handleCheck: ', data, checkedObj);
			
				// 获取投放区域ID集合（含全选与半选）
				let checkedRegionIds = this.$refs.tree.getCheckedKeys(); // 被选中的节点的 key 所组成的数组
				let halfCheckedRegionIds = this.$refs.tree.getHalfCheckedKeys(); // 半选中的节点的 key 所组成的数组
				this.form.region_ids = checkedRegionIds.length != 0 ? [checkedRegionIds, halfCheckedRegionIds] : []; // 判断全选是否为空 checkedRegionIds.length ?= 0，用于验证 Tree 树形在表单中的选中状态
				
				// 获取设备列表
				this.getDeviceList();
			},
			
			/**
			 * （广告类别）选中值发生变化时触发
			 */
			selectAdCateChange(data) {
				// console.log('shopCateIds:', data)
				
				// 获取设备列表
				this.getDeviceList();
			},
			
			/**
			 * （投放店铺类别）选中值发生变化时触发
			 */
			selectShopCateChange(data) {
				// console.log('shopCateIds:', data)
				
				// 获取设备列表
				this.getDeviceList();
			},
			
			/**
			 * 获取设备列表
			 */
			getDeviceList() {
				let self = this;
				// if (this.$refs.tree.getCheckedKeys().length != 0 && this.form.shop_cate_ids.length != 0) {
				if (this.$refs.tree.getCheckedKeys().length != 0 && this.form.ad_cate_id != '') {
					this.$axios.get(this.$url + 'device_list', {
						params: {
							region_ids: this.$refs.tree.getCheckedKeys(), // 投放区域ID集合（只含全选）
							// shop_cate_ids: this.form.shop_cate_ids // 投放店铺类别ID集合
							ad_cate_id: this.form.ad_cate_id // 广告所属行业类别（作为排除条件）
						}
					})
					.then(function(res) {
						if (res.data.status == 1) {
							// 设备列表
							self.deviceList = res.data.data;
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
				} else {
					this.deviceList = []; // 初始化设备列表
				}
			},

			/**
			 * 设备全选效果
			 * @param {Object} val
			 */
			handleCheckAllChange(val) {
				// 设备ID集合
				let deviceIds = [];
				this.deviceList.forEach((item) => {
					deviceIds.push(item.device_id);
				})
				this.form.device_ids = val ? deviceIds : [];
				
				this.isIndeterminate = false;
			},

			/**
			 * 设备选中状态变化时触发，用于显示全选框效果等
			 * @param {Object} value
			 */
			handleCheckedDeviceChange(value) {
				let checkedCount = value.length;
				this.checkAll = checkedCount === this.deviceList.length;
				this.isIndeterminate = checkedCount > 0 && checkedCount < this.deviceList.length;
			},

			/**
			 * 新增广告提交表单
			 * @param {Object} formName
			 */
			submitForm(formName) {
				let self = this;
				this.$refs[formName].validate((valid) => {
					if (valid) {
						this.$axios.post(this.$url + 'ad', {
								// 参数
								is_ad_combo: this.form.is_ad_combo,
								ad_name: this.form.ad_name,
								ad_cate_id: this.form.ad_cate_id,
								ad_price: this.form.ad_price,
								discount_ratio: this.form.discount_ratio,
								ad_datetime: this.form.ad_datetime,
								ad_time: this.form.ad_time,
								play_times: this.form.play_times,
								advertiser: this.form.advertiser,
								phone: this.form.phone,
								salesman_id: this.form.salesman_id,
								region_ids: this.form.region_ids,
								shop_cate_ids: this.form.shop_cate_ids,
								device_ids: this.form.device_ids,
								is_show: this.form.is_show,
								sort: this.form.sort
							})
							.then(function(res) {
								let type = res.data.status == 1 ? 'success' : 'warning';
								self.$message({
									message: res.data.message,
									type: type
								});
								self.$router.go(-1); // 返回上一页
							})
							.catch(function(error) {
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
			 * 重置表单
			 * @param {Object} formName
			 */
			resetForm(formName) {
				this.$refs[formName].resetFields();
			},

			/**
			 * 返回上一页
			 */
			back() {
				this.$router.go(-1);
			}
		}
	}
</script>

<style>
</style>
