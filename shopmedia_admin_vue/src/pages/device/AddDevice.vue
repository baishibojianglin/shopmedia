<template>
	<div class="create">
        
       <el-card class="box-card">	
		 <el-form  ref="ruleForm" :model="ruleForm" :rules="rules"  label-width="150px">
			 
		   <el-form-item label="设备品牌" prop="brand">	
			   <el-select @change="getmodel" v-model="ruleForm.brand" placeholder="请选择">
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
				  
		   <el-form-item label="设备尺寸" prop="size">	
			   <el-select v-model="ruleForm.size" placeholder="请选择">
				 <el-option
				   v-for="item in size_options"
				   :key="item.value"
				   :label="item.label"
				   :value="item.value">
				 </el-option>
			   </el-select>
		   </el-form-item>				  

		   <el-form-item label="安装店铺" prop="shop">	
			   <el-select v-model="ruleForm.shop" placeholder="请选择">
				 <el-option
				   v-for="item in shop_options"
				   :key="item.value"
				   :label="item.label"
				   :value="item.value">
				 </el-option>
			   </el-select>
		   </el-form-item>	         
			  	   
		   
		   <el-form-item label="排除广告类型" prop="remove_ad_cate">
			 <el-input style="width:217px;" type="text" clearable v-model="ruleForm.remove_ad_cate"></el-input>
		   </el-form-item>	   
		   
		   <el-form-item label="租售价格" prop="sale_price">
			 <el-input style="width:217px;" type="number" clearable v-model="ruleForm.sale_price"></el-input>
		   </el-form-item>

		   <el-form-item label="小店广告收益率" prop="shop_ad_rate">
			 <el-input style="width:217px;" type="number" clearable v-model="ruleForm.shop_ad_rate"></el-input> %
		   </el-form-item>

		   <el-form-item label="业务员广告收益率" prop="saleperson_ad_rate">
			 <el-input style="width:217px;" type="number" clearable v-model="ruleForm.saleperson_ad_rate"></el-input> %
		   </el-form-item>
		   
		   <el-form-item label="合作伙伴广告收益率" prop="partner_ad_rate">
		   	 <el-input style="width:217px;" type="number" clearable v-model="ruleForm.partner_ad_rate"></el-input> %
		   </el-form-item>	   
		   
		   <el-form-item label="厂家广告收益率" prop="factory_ad_rate">
		   	 <el-input style="width:217px;" type="number" clearable v-model="ruleForm.factory_ad_rate"></el-input> %
		   </el-form-item>	

		   <el-form-item label="状态" prop="status">
			   <el-select v-model="ruleForm.status" placeholder="请选择">
				 <el-option
				   v-for="item in status_options"
				   :key="item.value"
				   :label="item.label"
				   :value="item.value">
				 </el-option>
			   </el-select>
		   </el-form-item>					  
				  
		   <el-form-item label="已售份额" prop="saled_part">
		   	 <el-input style="width:217px;" type="number" clearable v-model="ruleForm.saled_part"></el-input> %
		   </el-form-item>					  
	
			<el-form-item label="评估等级" prop="level">
						   <el-select v-model="ruleForm.level" placeholder="请选择">
							 <el-option
							   v-for="item in level_options"
							   :key="item.value"
							   :label="item.label"
							   :value="item.value">
							 </el-option>
						   </el-select>
			</el-form-item>	
	
				  
				  
		   		   
		   <el-form-item>
			 <el-button type="primary" @click="submitForm('ruleForm')">提交</el-button>
			 <el-button @click="resetForm('ruleForm')">重置</el-button>
		   </el-form-item>
		   
		   
		 </el-form> 
		</el-card>
	</div>
</template>

 <script>
   export default {
     data() {
		   return {
			   shop_options: [
			     {
			       value:'',
			   	   label:''
			     }
			   ],
			    status_options: [
			      {
			   	    value:'',
			   		label:''
			      }
			    ],
				brand_options: [
				   {
					 value: '',
					 label: ''
				   }
				],
			    model_options: [
				    {
					 value: '',
					 label: ''
					}
				],
				size_options: [
				    {
					 value: '',
					 label: ''
					}
				],
				level_options: [
				    {
					 value: '',
					 label: ''
					},
				],
				ruleForm: {
				   brand:'', //设备品牌
				   model:'',//设备型号
				   size:'',//设备尺寸
				   level:'',//等级
				   remove_ad_cate:'',//排除广告类型
				   sale_price: '', //价格
				   shop_ad_rate:'',//小店广告收益率
				   company_id:'', //分公司id
				   create_user:'',//创建人id
				   saleperson_ad_rate:'',//业务员收益率
				   partner_ad_rate:'',//合作伙伴收益率
				   factory_ad_rate:'',//厂家收益率
				   status:'',//状态
				   saled_part:'',//已售份额
				   shop:''//安装店铺
				},
				rules: {
				  brand: [
					{ required: true, message: '请选择设备品牌', trigger: 'blur' }
				  ],
				  model: [
				  	{ required: true, message: '请选择设备型号', trigger: 'blur' }
				  ],
				  size: [
				  	{ required: true, message: '请选择设备尺寸', trigger: 'blur' }
				  ],
				  level:[
				  	{ required: true, message: '请选择等级', trigger: 'blur' }
				  ],
				  remove_ad_cate:[
				  	{ required: true, message: '请确定排除类型', trigger: 'blur' }
				  ],
				  sale_price:[
					{ required: true, message: '请输入设备出售价格', trigger: 'blur' }
				  ],
				  shop_ad_rate:[
				  	{ required: true, message: '请填写小店广告收益率', trigger: 'blur' }
				  ],			  
				  saleperson_ad_rate:[
				  	{ required: true, message: '请填写业务员广告收益率', trigger: 'blur' }
				  ],
				  partner_ad_rate:[
				  	{ required: true, message: '请填写合作伙伴广告收益率', trigger: 'blur' }
				  ],
				  factory_ad_rate:[
				  	{ required: true, message: '请填写厂家广告收益率', trigger: 'blur' }
				  ]	,
				  status:[
				  	{ required: true, message: '请选择状态', trigger: 'blur' }
				  ],
				  saled_part:[
				  	{ required: true, message: '请填写已售份额', trigger: 'blur' }
				  ]
				},
				device_id:''//广告屏id
		   }
     },
	 mounted(){
		   
		  //初始化 
		   this.getbrand();//设备品牌
		   this.getsize();//设备尺寸
		   this.getstatus();//设备状态
		   this.getshop();//店铺信息
		   
		   
	 },
     methods: {
       /**
		* 获取店铺列表
		*/
	   getshop(){
			let self=this;			
			this.$axios.get(this.$url+'get_device_shop')
			.then(function (response){
				if(response.data.status==1){
					  // response.data.data.forEach((value,index)=>{
						 //  self.$set(self.status_options,index,{value:value.status_id,label:value.status_name})
					  // })
				}else{
					  self.$message({
							message:'网络繁忙，请重试',
							type: 'warning'
					  });					   
				}
			})		   
	   },
	    /**
		* 获取广告屏设备状态
		*/
		getstatus(){
			let self=this;			
			this.$axios.get(this.$url+'get_device_status')
			.then(function (response){
				if(response.data.status==1){
					  response.data.data.forEach((value,index)=>{
						  self.$set(self.status_options,index,{value:value.status_id,label:value.status_name})
					  })
				}else{
					  self.$message({
							message:'网络繁忙，请重试',
							type: 'warning'
					  });					   
				}
			})
		
		}, 
		 
		/**
		* 获取广告屏设备尺寸
		*/
		getsize(){
			let self=this;			
			this.$axios.get(this.$url+'get_device_size')
			.then(function (response){
				if(response.data.status==1){
					  response.data.data.forEach((value,index)=>{
						  self.$set(self.size_options,index,{value:value.size_id,label:value.size_name})
					  })
				}else{
					  self.$message({
							message:'网络繁忙，请重试',
							type: 'warning'
					  });					   
				}
			})
		
		}, 
		 
		 
		 
		 /**
		  * 获取设备型号
		  */
		 getmodel(brand_id){
			    //清空设备型号
				let model_length=this.model_options.length;
				this.model_options.splice(0,model_length);
				let self=this;	
				this.$axios.post(this.$url+'get_device_model',{
					brand_id:brand_id
				}).then(function(response){				
					if(response.data.status==1){
                         response.data.data.forEach((value,index)=>{
							 self.$set(self.model_options,index,{value:value.model_id,label:value.model_name})
						 })
					}else{
					  self.$message({
							message:'网络繁忙，请重试',
							type: 'warning'
					  });					   
					}
					
				})						 
		 },
		   /**
			* 获取广告屏设备品牌
			*/
		   getbrand(){
				let self=this;			
				this.$axios.get(this.$url+'get_device_brand')
				  .then(function (response){
					if(response.data.status==1){
                         response.data.data.forEach((value,index)=>{
							 self.$set(self.brand_options,index,{value:value.brand_id,label:value.brand_name})
						 })
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
			let admin_user=JSON.parse(localStorage.getItem('admin_user')); //取出的缓存的登录账户信息
			this.ruleForm.company_id=admin_user.company_id; //获取登录账号所属的供应商id，并赋值给表单
			this.ruleForm.create_user=admin_user.id; //获取登录账号的用户id，并赋值给表单
			//将图片地址和名字组装成一个字符串
			this.ruleForm.name_image=this.name_image_list.join();
			this.ruleForm.url_image=this.url_image_list.join();		
			this.$refs[formName].validate((valid) => {
			  if (valid) {
				this.$axios.post(this.$url+'addDevice',{
				   data:this.ruleForm,
				   device_id:this.device_id
				}).then(function(res){
                   if(res.data.status==1){
					  self.$message({
					   		message:'设备添加成功',
					   		type: 'success'
					  });
					  self.$router.push({path: "device", query: {device_id:res.data.device_id}});
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
		width: 170px;
		height: 120px;
		line-height: 120px;
	}
	.idcard .el-upload--picture-card {
		width: 170px;
		height: 120px;
		line-height: 120px;
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
