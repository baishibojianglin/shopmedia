<template>
	<div class="act_prize_create">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>编辑奖品</span></el-col>
					<el-col :span="3">
						<el-button size="mini" icon="el-icon-back" title="返回" @click="back()">返回</el-button>
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- Form 表单 s -->
				<el-form ref="ruleForm" :model="form" :rules="rules" label-width="200px" size="small" class="demo-form-inline">
					<el-form-item prop="act_id" label="所属活动">
						<el-select v-model="form.act_id" placeholder="请选择…" clearable filterable>
							<el-option v-for="item in actList" :key="item.act_id" :label="item.act_name" :value="item.act_id">
							</el-option>
						</el-select>
					</el-form-item>
					<el-form-item prop="prize_name" label="奖品名称">
						<el-input v-model="form.prize_name" placeholder="输入奖品名称" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="quantity" label="奖品数量">
						<el-input-number v-model="form.quantity" :min="0" :step="1" :precision="0" controls-position="right"></el-input-number>
					</el-form-item>
					<el-form-item prop="percentage" label="中奖概率">
						<el-input-number v-model="form.percentage" :min="0" :max="0.99" :step="0.01" :precision="2" controls-position="right"></el-input-number>
					</el-form-item>
					<el-form-item prop="level" label="奖品等级">
						<el-select v-model="form.level" placeholder="请选择…" clearable filterable>
							<el-option v-for="item in prizeLevelList" :key="item.level_id" :label="item.level_name" :value="item.level_id">
							</el-option>
						</el-select>
					</el-form-item>
					<el-form-item prop="sponsor" label="奖品赞助商">
						<el-input v-model="form.sponsor" placeholder="输入奖品赞助商" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="phone" label="赞助商电话">
						<el-input v-model="form.phone" placeholder="输入赞助商电话" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="status" label="奖品状态">
						<el-radio-group v-model="form.status">
							<el-radio v-for="(item, index) in {0: '下架', 1: '正常'}" :key="index" :label="Number(index)">{{item}}</el-radio>
						</el-radio-group>
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
	export default {
		data() {
			return {
				form: {
					prize_id: '', // 活动ID
					act_id: '', // 活动ID
					prize_name: '', // 奖品名称
					quantity: '', // 奖品数量
					level: '', // 奖品等级
					percentage: '', // 中奖概率
					sponsor: '', // 奖品赞助商
					phone: '', // 赞助商电话
					status: '' // 奖品状态
				},
				rules: { // 验证规则
					act_id: [{required: true, message: '请选择所属活动', trigger: 'change'}],
					prize_name: [
						{ required: true, message: '请输入奖品名称', trigger: 'blur' },
						{ min: 1, max: 150, message: '长度在 1 到 20 个字符', trigger: 'blur' }
					],
					quantity: [{required: true, message: '奖品数量', trigger: 'blur'}],
					percentage: [{required: true, message: '中奖概率', trigger: 'blur'}],
					level: [{required: true, message: '请选择奖品等级', trigger: 'change'}],
				},
				
				actList: [], // 活动列表
				prizeLevelList: [{'level_id': '', 'level_name': ''}] // 活动奖品等级列表
			}
		},
		mounted() {
			this.getActivityList();
			this.getPrizeLevelList();
			this.getParams();
			this.getActPrize();
		},
		methods: {
			/**
			 * 获取路由带过来的参数
			 */
			getParams() {
				this.form.prize_id = this.$route.query.prize_id;
			},
			
			/**
			 * 获取指定的活动奖品信息
			 */
			getActPrize() {
				let self = this;
				this.$axios.get(this.$url + 'act_prize/' + this.form.prize_id)
				.then(function(res) {
					if (res.data.status == 1) {
						self.form = res.data.data;
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
			 * 获取活动列表
			 */
			getActivityList() {
				let self = this;
				this.$axios.get(this.$url + 'activity_list')
				.then(function(res) {
					if (res.data.status == 1) {
						self.actList = res.data.data;
					} else {
						self.$message({
							message: '网络忙，请重试',
							type: 'warning'
						});
					}
				})
			},
			
			/**
			 * 获取活动奖品等级列表
			 */
			getPrizeLevelList() {
				let self = this;
				this.$axios.get(this.$url + 'act_prize_level')
				.then(function(res) {
					if (res.data.status == 1) {
						self.prizeLevelList = res.data.data;
					} else {
						self.$message({
							message: '网络忙，请重试',
							type: 'warning'
						});
					}
				})
			},
			
			/**
			 * 新增活动奖品提交表单
			 * @param {Object} formName
			 */
			submitForm(formName) {
				let self = this;
				this.$refs[formName].validate((valid) => {
					if (valid) {
						this.$axios.put(this.$url + 'act_prize/' + this.form.prize_id, {
							// 参数
							act_id: this.form.act_id,
							prize_name: this.form.prize_name,
							quantity: this.form.quantity,
							level: this.form.level,
							percentage: this.form.percentage,
							sponsor: this.form.sponsor,
							phone: this.form.phone,
							status: this.form.status
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
