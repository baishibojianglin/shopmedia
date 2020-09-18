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
					<el-form-item prop="prize_type" label="奖品类型">
						<el-select v-model="form.prize_type" clearable filterable>
							<el-option v-for="item in prizeTypeList" :key="item.prize_type" :label="item.prize_type_name" :value="item.prize_type"></el-option>
						</el-select>
					</el-form-item>
					<el-form-item prop="prize_name" label="奖品名称">
						<el-input v-model="form.prize_name" placeholder="输入奖品名称" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="sort" label="排序">
						<el-input-number v-model="form.sort" :min="0" :step="1" :precision="0" controls-position="right"></el-input-number>
					</el-form-item>
					
					<el-form-item label="奖品图片" prop="prize_pic" class="idcard">
						<el-input v-show='false' style="width:350px;" v-model="form.prize_pic"></el-input>
						<el-upload :file-list="fileList" :class="{hide:hideUpload[0]}" list-type="picture-card" :action="this.$url+'upload?name=image'"
						 :limit="1" :on-success="function (res,file,fileList) { return returnUrl(res,file,fileList,'prize_pic',0)}"
						 :on-change="function (file,fileList) { return delePlusButton(file,fileList,1,0)}" :on-remove="function (file,fileList) { return handleRemove(file,fileList,0,1,'prize_pic')}"
						 :on-preview="handlePictureCardPreview" name='image'>
							<i class="el-icon-circle-plus-outline" style="font-size: 14px;"> 上传图片</i>
						</el-upload>
						<el-dialog :visible.sync="dialogVisible">
							<img width="100%" :src="dialogImageUrl" alt="">
						</el-dialog>
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
						<el-input v-model="form.sponsor" placeholder="输入奖品赞助商名称" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="phone" label="赞助商电话">
						<el-input v-model="form.phone" placeholder="输入赞助商电话" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="ad_cate_id" label="赞助商所属行业">
						<el-select v-model="form.ad_cate_id" clearable filterable>
							<el-option
								v-for="item in adCateList"
								:key="item.cate_id"
								:label="item.cate_name"
								:value="item.cate_id">
							</el-option>
						</el-select>
					</el-form-item>
					<el-form-item prop="address" label="赞助商领奖地址">
						<el-input v-model="form.address" placeholder="输入赞助商领奖地址" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="longitude" label="经度">
						<el-input-number v-model="form.longitude" :step="1" :precision="6" controls-position="right" style="width: 200px;"></el-input-number> <span class="text-info">{{form.longitude > 0 ? form.longitude + '°E' : -form.longitude + '°W'}}</span>
					</el-form-item>
					<el-form-item prop="latitude" label="纬度">
						<el-input-number v-model="form.latitude" :step="1" :precision="6" controls-position="right" style="width: 200px;"></el-input-number> <span class="text-info">{{form.latitude > 0 ? form.latitude + '°N' : -form.latitude + '°S'}}</span>
					</el-form-item>
					
					<el-form-item prop="is_sponsor_address" label="是否到赞助商处领奖">
						<el-radio-group v-model="form.is_sponsor_address">
							<el-radio v-for="(item, index) in {0: '否', 1: '是'}" :key="index" :label="Number(index)">{{item}}</el-radio>
						</el-radio-group>
					</el-form-item>
					
					<el-form-item prop="is_sponsor_raffle" label="是否只能在赞助商店铺抽奖">
						<el-radio-group v-model="form.is_sponsor_raffle">
							<el-radio v-for="(item, index) in {0: '本店铺与排除本行业的其他店铺', 1: '只在本店铺'}" :key="index" :label="Number(index)">{{item}}</el-radio>
						</el-radio-group>
					</el-form-item>
					
					<el-form-item prop="is_distance" label="是否限制抽奖店铺的距离">
						<el-radio-group v-model="form.is_distance">
							<el-radio v-for="(item, index) in {0: '否', 1: '是'}" :key="index" :label="Number(index)">{{item}}</el-radio>
						</el-radio-group>
					</el-form-item>
					<el-form-item prop="distance" label="抽奖店铺与赞助商的距离/㎞">
						<el-input-number v-model="form.distance" :step="1" :precision="3" controls-position="right" style="width: 200px;"></el-input-number> <span class="text-info"></span>
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
					prize_type: '', // 奖品类型
					prize_name: '', // 奖品名称
					sort: '', // 排序
					prize_pic: '', // 奖品图片
					quantity: '', // 奖品数量
					level: '', // 奖品等级
					percentage: '', // 中奖概率
					sponsor: '', // 奖品赞助商名称
					phone: '', // 赞助商电话
					ad_cate_id: '', // 赞助商所属行业 
					is_sponsor_address: '', // 是否到赞助商处领奖
					is_sponsor_raffle: '', // 是否只能在赞助商店铺抽奖
					is_distance: '', // 是否限制抽奖店铺的距离
					distance: '', // 是否限制抽奖店铺的距离
					address: '', // 赞助商领奖地址
					longitude: '', // 经度
					latitude: '', // 纬度
					status: '' // 奖品状态
				},
				rules: { // 验证规则
					act_id: [{required: true, message: '请选择所属活动', trigger: 'change'}],
					prize_type: [{required: true, message: '请选择奖品类型', trigger: 'change'}],
					prize_name: [
						{ required: true, message: '请输入奖品名称', trigger: 'blur' },
						{ min: 1, max: 150, message: '长度在 1 到 20 个字符', trigger: 'blur' }
					],
					prize_pic: [
						{required: true, message: '请上传照片'},
					],
					quantity: [{required: true, message: '奖品数量', trigger: 'blur'}],
					percentage: [{required: true, message: '中奖概率', trigger: 'blur'}],
					level: [{required: true, message: '请选择奖品等级', trigger: 'change'}],
				},
				
				actList: [], // 活动列表
				prizeTypeList: [{'prize_type': 1, 'prize_type_name': '实物'}, {'prize_type': 3, 'prize_type_name': '积分'}], // 奖品类型
				prizeLevelList: [{'level_id': '', 'level_name': ''}], // 活动奖品等级列表
				adCateList: [], // 赞助商所属行业列表
				
				dialogImageUrl: '',
				dialogVisible: false, //放大预览图片
				url_image_list: [], //图片地址列表
				hideUpload: [false, false], //隐藏图片添加按钮
				//回显数据增加字段
				loading: true,
				fileList: [],
				file: []
			}
		},
		mounted() {
			this.getActivityList();
			this.getPrizeLevelList();
			this.getParams();
			this.getActPrize();
			this.getAdCateList();
		},
		methods: {
			/**
			 * 获取路由带过来的参数
			 */
			getParams() {
				this.form.prize_id = this.$route.query.prize_id;
			},
			
			/**
			 * 获取赞助商所属行业列表
			 */
			getAdCateList() {
				let self = this;
				this.$axios.get(this.$url + 'ad_cate_list')
				.then(function(res) {
					if (res.data.status == 1) {
						// 赞助商所属行业列表
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
			 * 获取指定的活动奖品信息
			 */
			getActPrize() {
				let self = this;
				this.$axios.get(this.$url + 'act_prize/' + this.form.prize_id)
				.then(function(res) {
					if (res.data.status == 1) {
						self.form = res.data.data;
						
						// 回显图片
						let image = JSON.parse(res.data.data.prize_pic);
						image.forEach((value, index) => {
							//上传组件赋初始值
							let obj = new Object();
							obj.url = value.url;
							obj.name = value.name;
							self.fileList.push(obj);
							//列表赋初始值
							self.url_image_list.push(value);
						});
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
			 * 上传图片
			 * @param {string} response  返回图片地址
			 * @param {Object} file
			 * @param {Object} fileList
			 * @param {string} url_name 图片地址变量名
			 * @param {string} index 上传组件索引
			 */
			returnUrl(response, file, fileList, url_name, index) {
				this.url_image_list.push({
					name: response['name'],
					url: response['url']
				});
			},
			/**
			 * 删除图片上传完后的添加按钮
			 * @param {Object} file
			 * @param {Object} fileList
			 * @param {Object} num 允许上传的图片张数
			 * @param {string} index 上传组件索引
			 */
			delePlusButton(file, fileList, num, index) {
				this.$set(this.hideUpload, index, fileList.length >= num);
			},
			/**
			 * 删除图片
			 * @param {Object} file
			 * @param {Object} fileList
			 * @param {string} index 上传组件索引
			 * @param {Object} num 允许上传的图片张数
			 * @param {string} url_name 图片地址变量名
			 */
			handleRemove(file, fileList, index, num, url_name) {
				let name = '';
				this.url_image_list.forEach((value, index) => {
					if (file.response) {
						if (value.name == file.response.name) {
							this.url_image_list.splice(index, 1);
						}
						name = file.response.name;
					} else {
						if (value.name == file.name) {
							this.url_image_list.splice(index, 1);
						}
						name = file.name;
					}
				})
				let self = this;
				//删除oss上的图片
				this.$axios.post(this.$url + 'deleteimages', {
					name: name
				}).then(function(res) {
					self.$set(self.hideUpload, index, fileList.length >= num);
				})
			},
			/**
			 * 放大图片
			 * @param {Object} file
			 */
			handlePictureCardPreview(file) {
				this.dialogImageUrl = file.url;
				this.dialogVisible = true;
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
				this.form.prize_pic = JSON.stringify(this.url_image_list); //将图片地址处理成json字符串
				this.$refs[formName].validate((valid) => {
					if (valid) {
						this.$axios.put(this.$url + 'act_prize/' + this.form.prize_id, {
							// 参数
							act_id: this.form.act_id,
							prize_type: this.form.prize_type,
							prize_name: this.form.prize_name,
							sort: this.form.sort,
							prize_pic: this.form.prize_pic,
							quantity: this.form.quantity,
							level: this.form.level,
							percentage: this.form.percentage,
							sponsor: this.form.sponsor,
							phone: this.form.phone,
							ad_cate_id: this.form.ad_cate_id,
							is_sponsor_address: this.form.is_sponsor_address,
							is_sponsor_raffle: this.form.is_sponsor_raffle,
							is_distance: this.form.is_distance,
							distance: this.form.distance,
							address: this.form.address,
							longitude: this.form.longitude,
							latitude: this.form.latitude,
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
