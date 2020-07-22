<template>
	<div class="ad_create">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>编辑广告</span></el-col>
					<el-col :span="3">
						<el-button size="mini" icon="el-icon-back" title="返回" @click="back()">返回</el-button>
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- Form 表单 s -->
				<el-form ref="ruleForm" :model="form" :rules="rules" label-width="200px" size="small" class="demo-form-inline">
					<el-form-item label="序号">{{form.ad_id}}</el-form-item>
					<el-form-item label="是否广告套餐">
						{{form.is_ad_combo === 1 ? '广告套餐' : '普通投放'}}
					</el-form-item>
					<el-form-item prop="ad_name" label="广告名称">
						<el-input v-model="form.ad_name" placeholder="输入广告名称" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="ad_cate_id" label="广告类别">
						<!-- TODO：封装公共 ad-cate-select 组件 -->
						<!-- <ad-cate-select :value="form.ad_cate_id"></ad-cate-select> -->
						<el-select v-model="form.ad_cate_id" placeholder="请选择…" clearable filterable>
							<el-option
								v-for="item in adCateList"
								:key="item.cate_id"
								:label="item.cate_name"
								:value="item.cate_id">
							</el-option>
						</el-select>
					</el-form-item>
					<el-form-item prop="ad_datetime" label="投放时间">
						<el-date-picker
							v-model="form.ad_datetime"
							type="datetimerange"
							range-separator="至"
							start-placeholder="开始日期"
							end-placeholder="结束日期">
						</el-date-picker>
					</el-form-item>
					<el-form-item prop="ad_time" label="每日播放时间段">
						<el-time-picker
							is-range
							v-model="form.ad_time"
							range-separator="至"
							start-placeholder="开始时间"
							end-placeholder="结束时间"
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
					<el-form-item v-if="!isLocation" prop="region_ids" label="投放区域">
						<!-- Tree 树形控件（可选择层级） s -->
						<el-tree ref="tree" empty-text="数据加载中…" node-key="region_id" :props="props" :load="loadNode" :default-expanded-keys="expandedRegionKeys" show-checkbox :default-checked-keys="checkedRegionKeys" lazy @check="handleCheck"></el-tree>
						<!-- Tree 树形控件 e -->
					</el-form-item>
					<el-form-item prop="shop_cate_ids" label="投放店铺类别">
						<!-- TODO：封装公共 shop-cate-select 组件 -->
						<!-- <shop-cate-select :value="form.shop_cate_ids"></shop-cate-select> -->
						<el-select v-model="form.shop_cate_ids" multiple placeholder="请选择…" clearable filterable @change="selectShopCateChange">
							<el-option v-for="item in shopCateList" :key="item.cate_id" :label="item.cate_name" :value="item.cate_id" :disabled="form.ad_cate_id == item.cate_id">
							</el-option>
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
					<el-form-item prop="audit_status" label="审核状态">
						<el-radio-group v-model="form.audit_status">
							<el-radio :label="0">待审核</el-radio>
							<el-radio :label="1">通过</el-radio>
							<el-radio :label="2">驳回</el-radio>
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
					ad_name: '', // 广告名称
					ad_cate_id: '', // 广告类别ID
					ad_price: '', // 广告价格
					discount_ratio: '', // 广告折扣率
					region_ids: [], // 区域ID集合（数组）
					shop_cate_ids: [], // 投放店铺类别ID集合
					device_ids: [] // 投放广告设备ID集合
					// …
				},
				rules: { // 验证规则
					ad_name: [
						{ required: true, message: '请输入广告名称', trigger: 'blur' },
						{ min: 1, max: 20, message: '长度在 1 到 20 个字符', trigger: 'blur' }
					],
					ad_cate_id: [
						{ required: true, message: '请选择广告类别', trigger: 'change' }
					],
					ad_price: [
						{ required: true, message: '请输入广告价格', trigger: 'blur' }
					],
					ad_datetime: [
						{ /* type: 'date', */ required: true, message: '请选择投放时间', trigger: 'change' }
					],
					ad_time: [
						{ /* type: 'date', */ required: true, message: '每日播放时间段', trigger: 'change' }
					],
					play_times: [
						{ required: true, message: '请输入每日播放次数', trigger: 'blur' }
					],
					advertiser: [
						{ required: true, message: '请输入广告主名称', trigger: 'blur' },
						{ min: 2, max: 20, message: '长度在 2 到 20 个字符', trigger: 'blur' }
					],
					phone: [
						{required: true, pattern: /^1[34578]\d{9}$/, message: '目前只支持中国大陆的手机号码',trigger: 'blur'},
					],
					region_ids: [
						{required: true, message: '请选择投放区域', trigger: 'change'},
					],
					shop_cate_ids: [
						{ required: true, message: '请选择投放店铺类别', trigger: 'change' }
					],
					device_ids: [
						{required: true, message: '请选择投放广告屏', trigger: 'change'}
					]
				},
				
				isLocation: false, // 是否通过定位投放
				
				adCateList: [], // 广告类别列表
				shopCateList: [], // 店铺类别列表
				deviceList: [], // 设备列表
				
				// 区域 Tree 树形数据
				props: {
					label: 'region_name',
					isLeaf: 'leaf'
				},
				expandedRegionKeys: [], // 默认展开的区域节点的 key 的数组
				checkedRegionKeys: [], // 默认勾选的区域节点的 key 的数组
				
				// 投放广告屏复选框全选效果
				checkAll: false,
				isIndeterminate: false
			}
		},
		created() {
			this.getParams();
			this.getAdCateList(); // 获取广告类别列表
			this.getShopCateList(); // 获取店铺类别列表
			this.getAd(); // 获取指定的广告信息
		},
		methods: {
			/**
			 * 获取路由带过来的参数
			 */
			getParams() {
				this.form.ad_id = this.$route.query.ad_id;
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
				.catch(function (error) {
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
				this.$axios.get(this.$url + 'ad_cate_list')
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
				.catch(function (error) {
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
				let getDeviceListCondition = this.$refs.tree.getCheckedKeys().length != 0 && this.form.shop_cate_ids.length != 0;
				this.getDeviceList(getDeviceListCondition, this.$refs.tree.getCheckedKeys());
			},
			
			/**
			 * （投放店铺类别）选中值发生变化时触发
			 */
			selectShopCateChange(data) {
				// console.log('shopCateIds:', data)
				
				// 获取设备列表
				let getDeviceListCondition = this.$refs.tree.getCheckedKeys().length != 0 && this.form.shop_cate_ids.length != 0;
				this.getDeviceList(getDeviceListCondition, this.$refs.tree.getCheckedKeys());
			},
			
			/**
			 * 获取设备列表
			 * @param {Object} condition 获取条件
			 * @param {Object} regionIds 投放区域ID集合（只含全选）
			 */
			getDeviceList(condition, regionIds) {
				let self = this;
				let params = {};
				if (condition) {
					params = {
						region_ids: regionIds, // 投放区域ID集合（只含全选）
						shop_cate_ids: this.form.shop_cate_ids // 投放店铺类别ID集合
					};
				} else {
					this.deviceList = []; // 初始化设备列表
					if (this.isLocation == true) {
						params = {
							region_ids: '', // 投放区域ID集合（只含全选）
							shop_cate_ids: this.form.shop_cate_ids // 投放店铺类别ID集合
						}
					}
				}
				this.$axios.get(this.$url + 'device_list', {
					params: params
				})
				.then(function(res) {
					if (res.data.status == 1) {
						// 设备列表
						self.deviceList = res.data.data;
						
						// 获取当前显示设备中的被勾选的设备（两数组求交集）
						let deviceIds  = self.form.device_ids;
						var deviceIdsArr = new Array();
						self.deviceList.forEach((value, index) => {
							for(var key in deviceIds){
								if (value['device_id'] == Number(deviceIds[key])) {
									deviceIdsArr.push(Number(deviceIds[key])); // 字符串数组转整数数组
								}
							}
						})
						self.form.device_ids = deviceIdsArr;
						
						// 全选效果
						self.checkAll = self.form.device_ids.length === self.deviceList.length;
						self.isIndeterminate = self.form.device_ids.length > 0 && self.form.device_ids.length < self.deviceList.length;
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
			 * 获取指定的广告信息
			 */
			getAd() {
				let self = this;
				this.$axios.get(this.$url + 'ad/' + this.form.ad_id, {
					/* headers: {
						'admin-user-id': JSON.parse(localStorage.getItem('admin_user')).id,
						'admin-user-token': JSON.parse(localStorage.getItem('admin_user')).token
					} */
				})
				.then(function(res) {
					if (res.data.status == 1) {
						// 广告信息
						self.form = res.data.data;
						
						// 广告投放店铺类别
						let shopCateIds  = self.form.shop_cate_ids;
						var shopCateIdsArr = new Array();
						for(var key in shopCateIds){
							shopCateIdsArr.push(Number(shopCateIds[key])); // 字符串数组转整数数组
						}
						self.form.shop_cate_ids = shopCateIdsArr;
						
						// 广告投放区域（非定位投放）
						if (self.form.region_ids) {
							self.form.region_ids = JSON.parse(res.data.data.region_ids);
							self.expandedRegionKeys = self.form.region_ids.half; // 半选时，默认展开
							self.checkedRegionKeys = self.form.region_ids.checked; // 全选时，默认勾选
							// 处理数据
							let regionIds  = self.form.region_ids;
							var regionIdsArr = new Array();
							for(var key in regionIds){
								regionIdsArr.push(regionIds[key]); // 关联数组转索引数组
							}
							self.form.region_ids = regionIdsArr;
						}
						// 通过定位投放广告
						if (self.form.distance != '0.000' && self.form.longitude != '0.000000' && self.form.latitude != '0.000000') {
							self.isLocation = true;
						}
						
						// 广告投放设备
						let deviceIds  = self.form.device_ids;
						var deviceIdsArr = new Array();
						for(var key in deviceIds){
							deviceIdsArr.push(Number(deviceIds[key])); // 字符串数组转整数数组
						}
						self.form.device_ids = deviceIdsArr;
						
						// 获取设备列表
						let getDeviceListCondition = self.checkedRegionKeys.length != 0 && self.form.shop_cate_ids.length != 0;
						self.getDeviceList(getDeviceListCondition, self.checkedRegionKeys);
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
			 * 编辑广告提交表单
			 * @param {Object} formName
			 */
			submitForm(formName) {
				let self = this;
				this.$refs[formName].validate((valid) => {
					if (valid) {
						this.$axios.put(this.$url + 'ad/' + this.form.ad_id, {
							// 参数
							ad_name: this.form.ad_name,
							ad_cate_id: this.form.ad_cate_id,
							ad_price: this.form.ad_price,
							discount_ratio: this.form.discount_ratio,
							ad_datetime: this.form.ad_datetime,
							ad_time: this.form.ad_time,
							play_times: this.form.play_times,
							advertiser: this.form.advertiser,
							phone: this.form.phone,
							region_ids: this.form.region_ids,
							shop_cate_ids: this.form.shop_cate_ids,
							device_ids: this.form.device_ids,
							is_show: this.form.is_show,
							audit_status: this.form.audit_status,
							sort: this.form.sort
						})
						.then(function(res) {
							// console.log(123, res)
							let type = res.data.status == 1 ? 'success' : 'warning';
							self.$message({
								message: res.data.message,
								type: type
							});
							self.$router.go(-1); // 返回上一页
						})
						.catch(function (error) {
							// console.log(223, error.response.data)
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
				this.getAd();
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
