<template>
	<div class="create">
		<el-row>
		  <el-col :span="22">
			 <el-steps :active="active" finish-status="success" style="margin-left: 50px;margin-bottom: 50px;">
			   <el-step title="填写基本信息"></el-step>
			   <el-step title="配置商品种类"></el-step>
			   <el-step title="配置销售地区"></el-step>
			   <el-step title="创建成功"></el-step>
			 </el-steps>			  
		  </el-col>
		 </el-row>
         
		
		 <el-form  ref="ruleForm" :model="ruleForm" :rules="rules"  label-width="150px">
			 
		   <el-form-item label="供应商名称" prop="name">
			 <el-input style="width:350px;"  v-model="ruleForm.name"></el-input>
		   </el-form-item>

		   <el-form-item label="供应商地址" prop="address">
			 <el-input style="width:350px;"  v-model="ruleForm.address"></el-input>
		   </el-form-item>

		   <el-form-item label="联系电话" prop="phone">
			 <el-input style="width:350px;"  v-model="ruleForm.phone"></el-input>
		   </el-form-item>

		   <el-form-item label="法人身份证" prop="url_idcard" class="idcard">
			   <el-input v-show='false' style="width:350px;"  v-model="ruleForm.url_idcard"></el-input>
			   <el-upload :class="{hide:hideUpload[0]}" list-type="picture-card" :action="this.$url+'upload?name=image'" :limit="1" :on-success="function (res,file,fileList) { return returnUrl(res,file,fileList,'url_idcard',0)}" :on-change="function (file,fileList) { return delePlusButton(file,fileList,1,0)}"  :on-remove="function (file,fileList) { return handleRemove(file,fileList,0,1,'url_idcard')}" :on-preview="handlePictureCardPreview"  name='image'>
				     <i class="el-icon-circle-plus-outline" style="font-size: 14px;"> 上传正面照</i>
			   </el-upload>
			   <el-dialog :visible.sync="dialogVisible">
			     <img width="100%" :src="dialogImageUrl" alt="">
			   </el-dialog>
		   </el-form-item>
	   
		   <el-form-item label="法人姓名" prop="legalperson_name">
		   			 <el-input style="width:350px;"  v-model="ruleForm.legalperson_name"></el-input>
		   </el-form-item>

		   <el-form-item label="法人身份证号码" prop="legalperson_idcard_code">
		   			 <el-input style="width:350px;"  v-model="ruleForm.legalperson_idcard_code"></el-input>
		   </el-form-item>
		   		   
		   <el-form-item label="营业执照" prop="url_license" class="license">
			   <el-input v-show='false' style="width:350px;"  v-model="ruleForm.url_license"></el-input>
			   <el-upload :class="{hide:hideUpload[1]}" list-type="picture-card" :action="this.$url+'upload?name=image'" :limit="1" :on-success="function (res,file,fileList) { return returnUrl(res,file,fileList,'url_license',1)}" :on-change="function (file,fileList) { return delePlusButton(file,fileList,1,1)}"  :on-remove="function (file,fileList) { return handleRemove(file,fileList,1,1,'url_license')}" :on-preview="handlePictureCardPreview"  name='image'>
				     <i class="el-icon-circle-plus-outline" style="font-size: 14px;"> 上传营业执照</i>
			   </el-upload>
			   <el-dialog :visible.sync="dialogVisible">
			     <img width="100%" :src="dialogImageUrl" alt="">
			   </el-dialog>
		   </el-form-item>  
		   
		   
		   <el-form-item label="社会统一信用码" prop="license_creditcode">
			 <el-input style="width:350px;"  v-model="ruleForm.license_creditcode"></el-input>
		   </el-form-item>		   
		   
		   
		   <el-form-item>
			 <el-button type="primary" @click="submitForm('ruleForm')">下一步</el-button>
			 <el-button @click="resetForm('ruleForm')">重置</el-button>
		   </el-form-item>
		   
		   
		 </el-form>    
	</div>
</template>

 <script>
   export default {
     data() {
		   return {
				ruleForm: {
				   name: '', //供应商名字
				   address:'', //供应商地址
				   phone:'', //供应商联系电话
			       url_idcard:'', //身份证正面图片地址
				   legalperson_name:'', //法人姓名
				   legalperson_idcard_code:'', //法人身份证号码
				   url_license:'', //营业执照图片地址
				   license_creditcode:'', //营业执照社会统一信用码
				   parent_id:'', //上级供应商id
				   step:1 //创建进度
				},
				rules: {
				  name: [
					{ required: true, message: '请输入供应商名称', trigger: 'blur' }
				  ],
				  address:[
					{ required: true, message: '请输入供应商地址', trigger: 'blur' }					  
				  ],
				  phone:[
					{ required: true, message: '请输入供应商电话', trigger: 'blur' }					  
				  ],
				  url_idcard:[
					{ required: true, message: '请上传法人身份证正面照' }
				  ],
				  legalperson_name:[
					{ required: true, message: '请输入法人姓名', trigger: 'blur' }
				  ],
				  legalperson_idcard_code:[
					{ required: true, message: '请填写法人身份证号码', trigger: 'blur' }
				  ],
				  url_license:[
					{ required: true, message: '请上传营业执照'}					  
				  ],																		
				  license_creditcode:[
					{ required: true, message: '请输入社会统一信用码', trigger: 'blur' }	
				  ]												  
				},
				dialogImageUrl: '',
				dialogVisible: false, //放大预览图片
				active: 0,  //步骤条
				img_name:[], //存储图片名字
				hideUpload:[false,false] //隐藏图片添加按钮
		   }
     },
     methods: {
		  /**
		  * 提交表单
		  * @param {Object} formName
		  */
		  submitForm(formName) {
			 
			let self=this;
			let company=JSON.parse(localStorage.getItem('company')); //取出的缓存的登录账户信息
			this.ruleForm.parent_id=company.company_id; //获取登录账号所属的供应商id，并赋值给表单
			this.ruleForm.create_user=company.user_id; //获取登录账号的用户id，并赋值给表单
			this.$refs[formName].validate((valid) => {
			  if (valid) {
				this.$axios.post(this.$url+'createCompany',{
				   data:this.ruleForm
				}).then(function(res){
                   if(res.data.status==1){
					  self.$message({
					   		message:'基本信息填写成功',
					   		type: 'success'
					  });
					  self.$router.push({path: "companycate", query: {companyid:res.data.companyid}});
					  self.next(); 
				   }
				})                
			  }else {
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
		   * 步骤条
		   */
		  next(){
			if (this.active++ > 2) this.active = 0;
		  },
		  /**
		   * 上传图片
		   * @param {string} response  返回图片地址
		   * @param {Object} file
		   * @param {Object} fileList
		   * @param {string} url_name 图片地址变量名
		   * @param {string} index 上传组件索引
		   */
		  returnUrl(response, file, fileList,url_name,index){
			  this.ruleForm[url_name]=response['url'];
			  this.$set(this.img_name,index,response['name']);
		  },
          /**
		   * 删除图片上传完后的添加按钮
		   * @param {Object} file
		   * @param {Object} fileList
		   * @param {Object} num 允许上传的图片张数
		   * @param {string} index 上传组件索引
		   */
		  delePlusButton(file,fileList,num,index){
			  this.$set(this.hideUpload,index,fileList.length >= num);
		  },
		  /**
		   * 删除图片
		   * @param {Object} file
		   * @param {Object} fileList
		   * @param {string} index 上传组件索引
		   * @param {Object} num 允许上传的图片张数
		   * @param {string} url_name 图片地址变量名
		   */
		   handleRemove(file,fileList,index,num,url_name) {
			    let self=this;
				//删除oss上的图片
				this.$axios.post(this.$url+'deleteimages',{
					name:self.img_name[index]
				}).then(function(res){	
					self.$set(self.hideUpload,index,fileList.length >= num);
					self.ruleForm[url_name]='';
				})			   
		   },
		   /**
			* 放大图片
			* @param {Object} file
			*/
		   handlePictureCardPreview(file) {
			  this.dialogImageUrl = file.url;
			  this.dialogVisible = true;
		   }
     }
   }
 </script>  

<style>
	.create{
		padding:20px 0 50px 0;
	}
	.el-upload-list{
		width: 180px;
	}
	.idcard .el-upload-list--picture-card .el-upload-list__item{
		width: 190px;
		height: 120px;
		line-height: 120px;
	}
	.idcard .el-upload--picture-card {
		width: 190px;
		height: 120px;
		line-height: 120px;
	}
	.license .el-upload-list--picture-card .el-upload-list__item{
		width: 120px;
		height: 170px;
		line-height: 170px;
	}
	.license .el-upload--picture-card {
		width: 140px;
		height: 198px;
		line-height: 198px;
	}
	.hide .el-upload--picture-card {
		display: none;
	}
</style>
