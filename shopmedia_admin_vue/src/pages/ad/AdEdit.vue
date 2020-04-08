<template>
	<div class="create">
        
       <el-card class="box-card">
		   
		 <div slot="header" class="clearfix">
			<el-row :gutter="20" type="flex" justify="space-between">
				<el-col :span="24"><span class="el-icon-edit color-blue"></span> 编辑广告屏信息</el-col>
			</el-row>
		 </div>
	   
		 <el-form v-loading="loading"  ref="ruleForm" :model="ruleForm" :rules="rules"  label-width="150px">
			 
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
			   <el-upload :file-list="fileList"  :class="{hide:hideUpload[0]}" list-type="picture-card" :action="this.$url+'upload?name=image'" :limit="5" :on-success="function (res,file,fileList) { return returnUrl(res,file,fileList,'url_image',0)}" :on-change="function (file,fileList) { return delePlusButton(file,fileList,5,0)}"  :on-remove="function (file,fileList) { return handleRemove(file,fileList,0,5,'url_image')}" :on-preview="handlePictureCardPreview"  name='image'>
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

		   <el-form-item label="排除广告类型" prop="remove_ad_cate">
			   <el-select v-model="ruleForm.remove_ad_cate" placeholder="请选择">
				 <el-option
				   v-for="item in remove_ad_cate_options"
				   :key="item.value"
				   :label="item.label"
				   :value="item.value">
				 </el-option>
			   </el-select>
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
			   status_options: [
			      {
					 value: 0,
					 label: '故障'
			      },
			      {
					 value: 1,
					 label: '正常'
			      },
				  {
					 value: 2,
					 label: '上线'
				  },
				  {
					 value: 3,
					 label: '下线'
				  }
			   ],
				brand_options: [
				   {
					 value: 1,
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
					 value: 1,
					 label: '商超'
					},
					{
					 value: 2,
					 label: '餐饮'
					},
					{
					 value: 3,
					 label: '服装'
					},
					{
					 value: 4,
					 label: '生鲜'
					}
				],
				size_options: [
				    {
					 value: 22,
					 label: '22'
					},
					{
					 value: 32,
					 label: '32'
					}
				],
				environment_options: [
				    {
					 value: 1,
					 label: '商业区'
					},
					{
					 value: 2,
					 label: '居民区'
					}
				],
				level_options: [
				    {
					 value: 1,
					 label: '普通'
					},
					{
					 value: 2,
					 label: '优质'
					}
				],
				remove_ad_cate_options: [
				    {
					 value: 1,
					 label: '服装'
					},
					{
					 value: 2,
					 label: '餐饮'
					}
				],
				ruleForm: {
				   brand:'', //设备品牌
				   model:'',//设备型号
				   size:'',//设备尺寸
				   city_id:'',//市级id
				   area_id:'',//县区id
				   street_id:'',//街道id
				   address:'',//详细地址
				   shopname:'',//店铺名称
				   shopcate:'',//店铺类型
				   longitude:'',//定位经度
				   latitude:'',//定位纬度
				   shopsize:'',//店铺大小
				   environment:'',//店铺周边环境
			       url_image:'' ,//图片
				   describe:'',//基本描述
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
				   name_image:''//图片名字
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
				  remove_ad_cate:[
				  	{ required: true, message: '请选择类型', trigger: 'blur' }
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
				  ],
				  status:[
				  	{ required: true, message: '请选择状态', trigger: 'blur' }
				  ],
				  saled_part:[
				  	{ required: true, message: '请填写已售份额', trigger: 'blur' }
				  ]
				},
				fileList:[],
				file:[],
				arealist:[],//县（区）
				streetlist:[],//街道
				flag_area:0, //加载县区数据标志
				dialogImageUrl: '',
				dialogVisible: false, //放大预览图片
				hideUpload:[false,false] ,//隐藏图片添加按钮
				loading: true,
				device_id:'',//广告屏id
				url_image_list:[],//图片地址列表
				name_image_list:[]//图片名字列表
		   }
     },
	 mounted(){
		  //加载县区数据列表
		  this.zone(this.$route.query.city_id);
		  //加载街道数据列表
		   this.zone(this.$route.query.area_id);
		  //回显数据
		  this.getdata()
	 },
     methods: {
		   /**
			* 回显数据
			*/
		    getdata(){
			   let self=this;
			   this.$axios.post(this.$url+'getDevice',{
				 device_id:this.$route.query.device_id
			   }).then(function(res){
					if(res.data.status==1){
						//回显图片
						let urlStr = res.data.data.url_image.split(","); //图片地址
						let nameStr = res.data.data.name_image.split(","); //图片地址
						urlStr.forEach((value,index) => {
							       //上传组件赋初始值
						            let obj = new Object();
						            obj.url = value; 
									obj.name=nameStr[index];
						            self.fileList.push(obj);
									//列表赋初始值
									self.name_image_list.push(nameStr[index]);
									self.url_image_list.push(value);
						});
					   //剔除主键id
					   self.device_id=res.data.data.device_id;
					   delete res.data.data.device_id;
					   self.ruleForm=res.data.data;
					   self.loading=false;
					}
			   })		   
			},
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
					   		message:'编辑成功',
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
			this.ruleForm.area_id='';//重置县区id
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
			  this.url_image_list.push(response['url']);
			  this.name_image_list.push(response['name']);
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
			    let name='';
				this.name_image_list.forEach((value,index)=>{
					if(file.response){
						if(value==file.response.name){
							this.name_image_list.splice(index,1);
							this.url_image_list.splice(index,1);
						}
						name=file.response.name;				
					}else{
						if(value==file.name){
							this.name_image_list.splice(index,1);
							this.url_image_list.splice(index,1);
						}
						name=file.name;
					}

				})
			    let self=this;
				//删除oss上的图片
				this.$axios.post(this.$url+'deleteimages',{
					name:name
				}).then(function(res){	
					self.$set(self.hideUpload,index,fileList.length >= num);
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
