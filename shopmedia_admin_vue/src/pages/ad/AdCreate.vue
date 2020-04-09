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
					<el-form-item prop="ad_price" label="广告价格(元)">
						<el-input-number v-model="form.ad_price" :min="0" :step="1" :precision="2" controls-position="right"></el-input-number>
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
					<el-form-item prop="advertisers" label="广告主名称">
						<el-input v-model="form.advertisers" placeholder="输入广告主名称" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="phone" label="广告主电话">
						<el-input v-model="form.phone" placeholder="输入广告主联系电话" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="shop_cate_id" label="投放店铺类别">
						<!-- TODO：封装公共 shop-cate-select 组件 -->
						<!-- <shop-cate-select :value="form.shop_cate_id"></shop-cate-select> -->
						<el-select v-model="form.shop_cate_id" placeholder="请选择…" clearable filterable>
							<el-option
								v-for="item in shopCateList"
								:key="item.cate_id"
								:label="item.cate_name"
								:value="item.cate_id">
							</el-option>
						</el-select>
					</el-form-item>
					<el-form-item prop="region" label="投放区域">
						<!-- TODO -->
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
					ad_name: '', // 广告名称
					ad_cate_id: '', // 广告类别ID
					ad_price: '', // 广告价格
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
					advertisers: [
						{ required: true, message: '请输入广告主名称', trigger: 'blur' },
						{ min: 2, max: 20, message: '长度在 2 到 20 个字符', trigger: 'blur' }
					],
					phone: [
						{required: true, pattern: /^1[34578]\d{9}$/, message: '目前只支持中国大陆的手机号码',trigger: 'blur'},
					],
					shop_cate_id: [
						{ required: true, message: '请选择投放店铺类别', trigger: 'change' }
					],
				},
				
				adCateList: [], // 广告类别列表
				shopCateList: [] // 店铺类别列表
			}
		},
		mounted() {
			this.getAdCateList(); // 获取广告类别列表
			this.getShopCateList(); // 获取店铺类别列表
		},
		methods: {
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
				this.$axios.get(this.$url + 'shop_cate_list')
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
			 * 新增广告类别提交表单
			 * @param {Object} formName
			 */
			submitForm(formName) {
				let self = this;
				this.$refs[formName].validate((valid) => {
					if (valid) {
						this.$axios.post(this.$url + 'ad', {
							// 参数
							ad_name: this.form.ad_name,
							ad_cate_id: this.form.ad_cate_id,
							ad_price: this.form.ad_price,
							ad_datetime: this.form.ad_datetime,
							ad_time: this.form.ad_time,
							play_times: this.form.play_times,
							advertisers: this.form.advertisers,
							phone: this.form.phone,
							shop_cate_id: this.form.shop_cate_id,
							province_id: this.form.province_id,
							city_id: this.form.city_id,
							county_id: this.form.county_id,
							town_id: this.form.town_id,
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
			 * 重置表单
			 * @param {Object} formName
			 */
			resetForm(formName) {
				this.$refs[formName].resetFields();
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
