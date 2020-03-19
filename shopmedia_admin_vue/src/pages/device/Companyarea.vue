<template>
	<div class="area">
		<el-row>
		  <el-col :span="22">
			 <el-steps :active="active" finish-status="success" style="margin-left: 50px;margin-bottom: 20px;">
			   <el-step title="填写基本信息"></el-step>
			   <el-step title="配置商品种类"></el-step>
			   <el-step title="配置销售地区"></el-step>
			   <el-step title="创建成功"></el-step>
			 </el-steps>			  
		  </el-col>
		 </el-row>
		 
	     <el-row>
			  <el-col :span="15" style="margin-left: 50px;">
					<el-form  ref="ruleForm" :model="ruleForm"  label-width="0px">
						
				       <p><span style="color:#f00;">*</span> 选择销售地区：</p>
					   <el-tree ref="tree" :default-expanded-keys="opendata" :default-checked-keys="checkdata" show-checkbox node-key="region_id"  :props="props" :load="loadNode" empty-text='' lazy show-checkbox></el-tree> 
                      															  
					   <el-form-item v-show="loadfinish"  style="margin-top:20px;">
						 <el-button type="primary" @click="submitForm('ruleForm')">下一步</el-button>
						 <el-button @click="resetForm('ruleForm')">重置</el-button>
					   </el-form-item>																															
															  
					</el-form>    
			  </el-col>
		 </el-row>		 
	 


		
	
	</div>
</template>

 <script>
   export default {
     data() {
		   return {
			    active: 1,  //步骤条
				loadfinish:false, //地区是否加载完成
				opendata:[], //默认要展开的节点id
				checkdata:[], //默认要选中的节点id
				props: {
				  label: 'region_name',
				  isLeaf: 'leaf'
				},
				ruleForm: {
				   salearea:'', //区域数据 
				   id:this.$route.query.companyid ,//新建的该供应商id
				   step:3 //创建进度
				}

		    }
     },
	 mounted(){
		 let self=this;
		 let company=JSON.parse(localStorage.getItem('company')); //取出的缓存的登录账户信息
		 this.companyid=company.company_id; //获取登录账号所属的供应商id	
	 },

     methods: {
		 
		  /**
		  * 提交表单
		  * @param {Object} formName
		  */
		  submitForm(formName) {
			 let self=this;
             self.ruleForm.salearea='';

			 //获取全选的数据
			 this.$refs.tree.getCheckedNodes().forEach((value,index)=>{
			 	self.ruleForm.salearea=self.ruleForm.salearea+value.region_id+'|';
			 })
			 //去除最后一个符号"|"
			 self.ruleForm.salearea=self.ruleForm.salearea.slice(0,-1);
			 //如果存在半选，设置半选分割点
			 if(this.$refs.tree.getHalfCheckedNodes().length>0){
				 self.ruleForm.salearea=self.ruleForm.salearea+',';
				 //获取半选的数据
				 this.$refs.tree.getHalfCheckedNodes().forEach((value,index)=>{
				 	self.ruleForm.salearea=self.ruleForm.salearea+value.region_id+'|';
				 })
				 //去除最后一个符号"|"
				 self.ruleForm.salearea=self.ruleForm.salearea.slice(0,-1);
			 }

			 //验证销售地区
			 if(self.ruleForm.salearea==''){
				 self.$message({
				 					 message:'请勾选销售地区',
				 					 type: 'warning'
				 });
				 return false;
			 }

             //提交数据
			this.$refs[formName].validate((valid) => {
			  if (valid) {
				this.$axios.post(this.$url+'submitArea',{
				   data:this.ruleForm
				}).then(function(res){
                   if(res.data.status==1){
					   self.$message({
					    		message:'销售地区配置成功',
					    		type: 'success'
					   });
					  self.$router.push({path: "companysuccess", query: {companyid:self.$route.query.companyid }});
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
		  * 获取tree形数据
		  * @param {Object} node
		  * @param {Object} resolve
		  */
		  loadNode(node, resolve) {	
			let self=this;
			let parent_id=0; //首次进入查询第一级
			let level=1;
			if(node.data){  //逐级查询
			   parent_id=node.data.region_id;
			   level=node.data.level+1;
			}
									
			this.$axios.post(this.$url+'getarea',{
				 parent_id:parent_id,  //父级id
				 id:this.$route.query.companyid //新建的经销商id
			}).then(function(res){
					//赋值已配置的数据
					if(self.checkdata.length==0){
						let catestring=res.data.selectdata['salecate'];
						if(catestring){
							//检测是否有半选节点
							let ishalf=catestring.indexOf(',');
							if(ishalf==-1){ //无半选节点
								self.checkdata=catestring.split("|")
							}else{  //有半选节点
								//全选和半选节点的分割
								let cate_total_array=catestring.split(","); 
								//半选节点，赋值给展开数组
								self.opendata=cate_total_array[1].split("|");
								//全选数组，赋值给要勾选数组
								let count_checkdata=cate_total_array[0].split("|").length;
								var checkdata_time=setInterval(function(){
									self.checkdata=cate_total_array[0].split("|");
									if(self.checkdata.length==count_checkdata){
										clearInterval(checkdata_time);
									}
								},1000);	
							}														
						}
					}
					self.loadfinish=true; //地区加载显示完成
					if(level==4){ //第四级时不再显示三角形
						res.data.data.forEach((value,index)=>{
							value.leaf=true;
						})
					}
					if (node.level === 0) {
					  return resolve(res.data.data);
					}
					if (node.level > 1) return resolve(res.data.data);

					setTimeout(() => {
					  const data = res.data.data;		
					  resolve(data);
					}, 500);							
								
			})		
				
		  }


		  
     }
   }
 </script>  

<style>
	.area{
		padding:20px 0 50px 0;
	}

</style>
