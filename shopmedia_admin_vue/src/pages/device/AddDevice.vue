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
				  
				  

		   <el-form-item label="放置区域" prop="street_id">
			 <el-select @change="zone" v-model="ruleForm.area_id" placeholder="请选择区(县)">
			 				 <el-option
			 				   v-for="item in arealist"
			 				   :key="item.value"
			 				   :label="item.label"
			 				   :value="item.value">
			 				 </el-option>
			 </el-select> 
			 <el-select style="margin-left: 5px;" v-model="ruleForm.street_id" placeholder="请选择街道(乡镇)">
			 				 <el-option
			 				   v-for="item in streetlist"
			 				   :key="item.value"
			 				   :label="item.label"
			 				   :value="item.value">
			 				 </el-option>
			 </el-select>		 
		   </el-form-item>

		   <el-form-item label="详细地址" prop="address">
			 <el-input style="width:440px;"  clearable v-model="ruleForm.address"></el-input>
		   </el-form-item>	   

		   <el-form-item label="定位经度" prop="longitude">
			 <el-input style="width:217px;"  clearable v-model="ruleForm.longitude"></el-input>
		   </el-form-item>

		   <el-form-item label="定位纬度" prop="latitude">
			 <el-input style="width:217px;"  clearable v-model="ruleForm.latitude"></el-input>
		   </el-form-item>

		   <el-form-item label="店铺名称" prop="shopname">
			 <el-input style="width:217px;"  clearable v-model="ruleForm.shopname"></el-input>
		   </el-form-item>

		   <el-form-item label="店铺大小" prop="shopsize">
			 <el-input style="width:217px;"  clearable v-model="ruleForm.shopsize"></el-input>
		   </el-form-item>

		   <el-form-item label="店铺类型" prop="shopcate">	
			   <el-select v-model="ruleForm.shopcate" placeholder="请选择">
				 <el-option
				   v-for="item in shopcate_options"
				   :key="item.value"
				   :label="item.label"
				   :value="item.value">
				 </el-option>
			   </el-select>
		    </el-form-item>

		   <el-form-item label="周边环境" prop="environment">	
			   <el-select v-model="ruleForm.environment" placeholder="请选择">
				 <el-option
				   v-for="item in environment_options"
				   :key="item.value"
				   :label="item.label"
				   :value="item.value">
				 </el-option>
			   </el-select>
		    </el-form-item>
		   

		   <el-form-item label="实景(5张以内)" prop="url_image" class="idcard">
			   <el-input v-show='false' style="width:350px;"  v-model="ruleForm.url_image"></el-input>
			   <el-upload :class="{hide:hideUpload[0]}" list-type="picture-card" :action="this.$url+'upload?name=image'" :limit="5" :on-success="function (res,file,fileList) { return returnUrl(res,file,fileList,'url_image',0)}" :on-change="function (file,fileList) { return delePlusButton(file,fileList,5,0)}"  :on-remove="function (file,fileList) { return handleRemove(file,fileList,0,5,'url_idcard')}" :on-preview="handlePictureCardPreview"  name='image'>
				     <i class="el-icon-circle-plus-outline" style="font-size: 14px;"> 上传图片</i>
			   </el-upload>
			   <el-dialog :visible.sync="dialogVisible">
			     <img width="100%" :src="dialogImageUrl" alt="">
			   </el-dialog>
		   </el-form-item>

		   <el-form-item label="基本描述" prop="describe">
			 <el-input style="width:440px;" type="textarea"  clearable v-model="ruleForm.describe"></el-input>
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
		   
		   <el-form-item label="租售价格" prop="sale_price">
			 <el-input style="width:217px;" type="number" clearable v-model="ruleForm.sale_price"></el-input>
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
					 value: '1',
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
			    shopcate_options: [
				    {
					 value: '1',
					 label: '商超'
					},
					{
					 value: '2',
					 label: '餐饮'
					},
					{
					 value: '3',
					 label: '服装'
					},
					{
					 value: '4',
					 label: '生鲜'
					}
				],
				size_options: [
				    {
					 value: '22',
					 label: '22'
					},
					{
					 value: '32',
					 label: '32'
					}
				],
				environment_options: [
				    {
					 value: '1',
					 label: '商业区'
					},
					{
					 value: '2',
					 label: '居民区'
					}
				],
				level_options: [
				    {
					 value: '1',
					 label: '普通'
					},
					{
					 value: '2',
					 label: '优质'
					}
				],
				ruleForm: {
				   brand:'1', //设备品牌
				   model:'1',//设备型号
				   area_id:'1',//县区id
				   street_id:'1',//街道id
				   address:'1',//详细地址
				   shopname:'1',//店铺名称
				   shopcate:'1',//店铺类型
				   longitude:'1',//定位经度
				   latitude:'1',//定位纬度
				   shopsize:'1',//店铺大小
				   environment:'1',//店铺周边环境
			       url_image:'1' ,//图片
				   describe:'1',//基本描述
				   level:'1',//等级
				   sale_price: '1', //价格
				   company_id:'1', //分公司id
				   create_user:'1'//创建人id
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
				  street_id: [
				  	{ required: true, message: '请选择投放区域', trigger: 'blur' }
				  ],
				  address: [
				  	{ required: true, message: '请填写详细地址', trigger: 'blur' }
				  ],
				  shopname: [
				  	{ required: true, message: '请填写店铺名称', trigger: 'blur' }
				  ],
				  shopcate: [
				  	{ required: true, message: '请选择店铺类型', trigger: 'blur' }
				  ],
				  longitude: [
				  	{ required: true, message: '请填写店铺经度', trigger: 'blur' }
				  ],
				  latitude: [
				  	{ required: true, message: '请填写店铺纬度', trigger: 'blur' }
				  ],
				  shopsize: [
				  	{ required: true, message: '请填写店铺大小', trigger: 'blur' }
				  ],
				  environment: [
				  	{ required: true, message: '请选择店铺商业环境', trigger: 'blur' }
				  ],
				  url_image:[
				  	{ required: true, message: '请上传照片' }
				  ],
				  describe:[
				  	{ required: true, message: '请填写基本情况', trigger: 'blur' }
				  ],
				  level:[
				  	{ required: true, message: '请选择等级', trigger: 'blur' }
				  ],
				  sale_price:[
					{ required: true, message: '请输入设备出售价格', trigger: 'blur' }
				  ],										  
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
			let admin_user=JSON.parse(localStorage.getItem('admin_user')); //取出的缓存的登录账户信息
			this.ruleForm.company_id=admin_user.company_id; //获取登录账号所属的供应商id，并赋值给表单
			this.ruleForm.create_user=admin_user.id; //获取登录账号的用户id，并赋值给表单
			//去除图片地址最后一个符号","
			this.ruleForm['url_image']=this.ruleForm['url_image'].slice(0,-1);
			this.$refs[formName].validate((valid) => {
			  if (valid) {
				this.$axios.post(this.$url+'addDevice',{
				   data:this.ruleForm
				}).then(function(res){
					console.log(res.data)
                   if(res.data.status==1){
					  self.$message({
					   		message:'基本信息填写成功',
					   		type: 'success'
					  });
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
			  this.ruleForm[url_name]=this.ruleForm[url_name]+response['url']+',';
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
