<template>
	<div class="news_create">
		<el-card class="main-card">
			<div slot="header" class="clearfix">
				<el-row :gutter="20" type="flex" justify="space-between">
					<el-col :span="6"><span>编辑新闻</span></el-col>
					<el-col :span="3">
						<el-button size="mini" icon="el-icon-back" title="返回" @click="back()">返回</el-button>
					</el-col>
				</el-row>
			</div>
			<div class="">
				<!-- Form 表单 s -->
				<el-form ref="ruleForm" :model="form" :rules="rules" label-width="200px" size="small" class="demo-form-inline">
					<el-form-item prop="title" label="新闻标题">
						<el-input v-model="form.title" placeholder="输入新闻标题" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="author" label="新闻作者">
						<el-input v-model="form.author" placeholder="输入新闻作者" clearable style="width:350px;"></el-input>
					</el-form-item>
					<!-- <el-form-item prop="cate_id" label="新闻类别">
						<el-select v-model="form.cate_id" placeholder="请选择…" clearable filterable>
							<el-option
								v-for="item in newsCateList"
								:key="item.cate_id"
								:label="item.cate_name"
								:value="item.cate_id">
							</el-option>
						</el-select>
					</el-form-item> -->
					<el-form-item prop="keywords" label="搜索关键字">
						<el-input v-model="form.keywords" placeholder="输入搜索关键字" clearable style="width:350px;"></el-input>
					</el-form-item>
					<el-form-item prop="brief" label="新闻摘要">
						<el-input type="textarea" :rows="5" v-model="form.brief" placeholder="输入新闻摘要" clearable></el-input>
					</el-form-item>
					<!-- <el-form-item prop="thumb" label="新闻缩略图">
						<el-input v-model="form.thumb" placeholder="" clearable style="width:350px;"></el-input>
					</el-form-item> -->
					<el-form-item prop="content" label="正文内容">
						<!-- <el-input type="textarea" :rows="10" v-model="form.content" placeholder="输入正文内容" clearable style="width: 100%"></el-input> -->
						<quill-editor ref="myTextEditor" v-model="form.content" :options="editorOption" style="height:600px;margin-bottom: 6.25rem;"></quill-editor>
					</el-form-item>
					<el-form-item prop="status" label="状态">
						<el-radio-group v-model="form.status">
							<el-radio v-for="(item, index) in {0: '草稿', 3: '驳回', 4: '发布', 5: '下架'}" :key="index" :label="Number(index)">{{item}}</el-radio><!-- , 1: '通过', 2: '待审核' -->
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
	import 'quill/dist/quill.core.css'
	import 'quill/dist/quill.snow.css'
	import 'quill/dist/quill.bubble.css'
	
	import { quillEditor } from 'vue-quill-editor'
	
	export default {
		components: {
			quillEditor
		},
		data() {
			return {
				form: {
					title: '', // 新闻标题
					cate_id: '', // 新闻类别ID
					// …
				},
				rules: { // 验证规则
					title: [
						{ required: true, message: '请输入新闻标题', trigger: 'blur' },
						{ min: 1, max: 150, message: '长度在 1 到 150 个字符', trigger: 'blur' }
					],
					/* cate_id: [
						{ required: true, message: '请选择新闻类别', trigger: 'change' }
					], */
					content: [
						{ required: true, message: '请输入新闻正文内容', trigger: 'blur' }
					]
				},
				
				newsCateList: [], // 新闻类别列表
				
				editorOption: {
					placeholder: '输入正文内容'
				}
			}
		},
		created() {
			this.getParams();
			// this.getNewsCateList(); // 获取新闻类别列表
			this.getNews(); // 获取指定的新闻信息
		},
		methods: {
			/**
			 * 获取路由带过来的参数
			 */
			getParams() {
				this.form.news_id = this.$route.query.news_id;
			},
			
			/**
			 * 获取新闻类别列表
			 */
			getNewsCateList() {
				let self = this;
				this.$axios.get(this.$url + 'news_cate_list')
				.then(function(res) {
					if (res.data.status == 1) {
						// 新闻类别列表
						self.newsCateList = res.data.data;
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
			 * 获取指定的新闻信息
			 */
			getNews() {
				let self = this;
				this.$axios.get(this.$url + 'news/' + this.form.news_id)
				.then(function(res) {
					if (res.data.status == 1) {
						// 新闻信息
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
			 * 编辑新闻提交表单
			 * @param {Object} formName
			 */
			submitForm(formName) {
				let self = this;
				this.$refs[formName].validate((valid) => {
					if (valid) {
						this.$axios.put(this.$url + 'news/' + this.form.news_id, {
							// 参数
							title: this.form.title,
							author: this.form.author,
							// cate_id: this.form.cate_id,
							keywords: this.form.keywords,
							brief: this.form.brief,
							// thumb: this.form.thumb,
							content: this.form.content,
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
				this.getNews();
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
