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
			  <el-col :span="24" style="text-align: center;">					
				       <p style="margin-top: 5%;">恭喜，供应商创建成功！</p>															  
					   <p style="margin-top: 60px;">
						 <el-button type="primary">去查看</el-button>
						 <el-button>上一步</el-button>
					   </p>																															  
			  </el-col>
		 </el-row>		 
	 


		
	
	</div>
</template>

 <script>
   export default {
     data() {
		   return {
			    active: 4  //步骤条
		    }
     },
	 mounted(){
		 let self=this;
		 let company=JSON.parse(localStorage.getItem('company')); //取出的缓存的登录账户信息
		 this.companyid=company.company_id; //获取登录账号所属的供应商id	
	 },

     methods: {
		 
		  
     }
   }
 </script>  

<style>
	.area{
		padding:20px 0 50px 0;
	}


</style>
