<template>
	<div class="create">

		 <el-form  ref="ruleForm" :model="ruleForm" :rules="rules"  label-width="150px">
			 
		   <el-form-item label="设备品牌" prop="brand">	
			   <el-select v-model="ruleForm.brand" placeholder="请选择">
				 <el-option
				   v-for="item in brand_options"
				   :key="item.value"
				   :label="item.label"
				   :value="item.value">
				 </el-option>
			   </el-select>
		    </el-form-item>
		   

		   <el-form-item label="设备型号" prop="model">	
			   <el-select v-model="ruleForm.model" placeholder="请选择">
				 <el-option
				   v-for="item in model_options"
				   :key="item.value"
				   :label="item.label"
				   :value="item.value">
				 </el-option>
			   </el-select>
		   </el-form-item>		   

		   <el-form-item label="放置区域" prop="street_id">
			 <el-select @change="zone" v-model="ruleForm.area_id" placeholder="请选择">
			 				 <el-option
			 				   v-for="item in arealist"
			 				   :key="item.value"
			 				   :label="item.label"
			 				   :value="item.value">
			 				 </el-option>
			 </el-select> 
			 <el-select style="margin-left: 5px;" v-model="ruleForm.street_id" placeholder="请选择">
			 				 <el-option
			 				   v-for="item in streetlist"
			 				   :key="item.value"
			 				   :label="item.label"
			 				   :value="item.value">
			 				 </el-option>
			 </el-select>		 
		   </el-form-item>
	   
		   
		   <el-form-item label="租售价格" prop="sale_price">
			 <el-input style="width:217px;" type="number" clearable v-model="ruleForm.sale_price"></el-input>
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
				brand_options: [
				   {
					 value: '长虹',
					 label: '长虹'
				   },
				],
			    model_options: [
				    {
					 value: 'ch001',
					 label: 'ch001'
					},
					{
					 value: 'ch002',
					 label: 'ch002'
					}
				],
				ruleForm: {
				   brand:'长虹', //设备品牌
				   model:'',//设备型号
				   area_id:'',//县区id
				   street_id:'',//街道id
				   sale_price: '', //供应商名字
			       url_idcard:'', //身份证正面图片地址
				},
				rules: {
				  brand: [
					{ required: true, message: '请选择设备品牌', trigger: 'blur' }
				  ],
				  model: [
				  	{ required: true, message: '请选择设备型号', trigger: 'blur' }
				  ],
				  street_id: [
				  	{ required: true, message: '请选择投放区域', trigger: 'blur' }
				  ],
				  sale_price: [
					{ required: true, message: '请输入设备出售价格', trigger: 'blur' }
				  ],
				  url_idcard:[
					{ required: true, message: '请上传法人身份证正面照' }
				  ]											  
				},
				arealist:[],//县（区）
				streetlist:[],//街道
				flag_area:0, //加载县区数据标志
				dialogImageUrl: '',
				dialogVisible: false, //放大预览图片
				img_name:[], //存储图片名字
				hideUpload:[false,false] //隐藏图片添加按钮
		   }
     },
	 mounted(){
		   
		  //调用 -获取登录账号所属分公司信息 - 方法
 	       this.getcompany();
	 },
     methods: {

            /**
			 * 获取登录账号所属的分公司信息
			 */
            getcompany(){
				let self=this;
				let admin_user=JSON.parse(localStorage.getItem('admin_user')); //取出的缓存的登录账户信息
				this.$axios.post(this.$url+'getCompany',{
					company_id:admin_user.company_id
				}).then(function(res){
				   if(res.data.status==1){
                       self.zone(res.data.data.city_id);
				   }else{
					  self.$message({
							message:'网络繁忙，请重试',
							type: 'warning'
					  });					   
				   }
				})

			},
			/**
			 * 获取地区列表
			 */
			zone(t_parent_id){
				let self=this;
				let parent_id=0;
				if(t_parent_id){
					parent_id=t_parent_id;
				}
				self.ruleForm.street_id=''; //清楚上次选择的街道数据
				this.$axios.post(this.$url+'getzone',{
					parent_id:parent_id
				}).then(function(res){
				   if(res.data.status==1){
					   if(self.flag_area==0){  //县区
					      self.arealist.splice(0,self.arealist.length);
						  res.data.data.forEach((value,index)=>{
							  self.$set(self.arealist,index,{value:value.region_id,label:value.region_name});
						  })
						  self.flag_area=1;//县区已经加载记录标志						 
					   }else{  //街道
					      self.streetlist.splice(0,self.streetlist.length);
						  res.data.data.forEach((value,index)=>{
						  	  self.$set(self.streetlist,index,{value:value.region_id,label:value.region_name});
						  }) 
					   }

				   }else{
					  self.$message({
							message:'网络繁忙，请重试',
							type: 'warning'
					  });					   
				   }
				})				
			},
		 	 		 
		 
		 
		 
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
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
	    -webkit-appearance: none;
	}
	input[type="number"]{
	    -moz-appearance: textfield;
	}
	input{width: 200px;}
</style>
