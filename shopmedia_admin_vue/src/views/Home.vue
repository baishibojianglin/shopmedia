<template>
	<div class="home">
		<el-row>
			
			<el-col :span="24" class="homeheader color-white"> <!--header s-->
				<el-col :span="5">
				   <div class="hometitle">{{name}}</div>
				</el-col>
			</el-col> <!--header e-->

			<el-col :span="24"> <!--content s-->
				<el-col  :xs="6" :sm="5" :md="4" :lg="3" :xl="2">  <!--menu s-->
					<div class="homemenu">
						<dl class="m0">
							
							<dt @click="menush(1)">
								<span class="el-icon-menu" id="menu1"> 设备管理</span>
								<span class="fr derection" :class="menuvalue[1]?derectionup:derectiondown"></span>
							</dt>
							<el-collapse-transition>
								<div v-show="menuvalue[1]">
									<router-link to="/home/addservice"><dd id='menu11' :class="activevalue[11]?activeclass:''"  @click="menuactive(11,1,1)">新增设备</dd></router-link>
									<router-link to="/home/companycreate"><dd id='menu12' :class="activevalue[12]?activeclass:''"  @click="menuactive(12,1,2)">经销商列表</dd></router-link>
								</div>
							</el-collapse-transition>
							
							
							<dt @click="menush(3)">
								<span class="el-icon-s-goods" id="menu3"> 商品管理</span>
								<span class="fr derection" :class="menuvalue[3]?derectionup:derectiondown"></span>
							</dt>
							<el-collapse-transition>
								<div v-show="menuvalue[3]">
									<!-- <router-link to="/"><dd id='menu31' :class="activevalue[31]?activeclass:''" @click="menuactive(31,3,1)">商品列表</dd></router-link> -->
									<router-link to="/home/goodscate"><dd id='menu32' :class="activevalue[32]?activeclass:''" @click="menuactive(32,3,2)">商品类别</dd></router-link>
									<router-link to="/home/goodsbrand"><dd id='menu33' :class="activevalue[33]?activeclass:''" @click="menuactive(33,3,3)">品牌管理</dd></router-link>
								</div>
							</el-collapse-transition>
							
							<dt @click="menush(4)">
								<span class="el-icon-user-solid" id="menu4"> 账户管理</span>
								<span class="fr derection" :class="menuvalue[4]?derectionup:derectiondown"></span>
							</dt>
							<el-collapse-transition>
								<div v-show="menuvalue[4]">
									<router-link to="/home/auth_group"><dd id='menu41' :class="activevalue[41]?activeclass:''"  @click="menuactive(41,4,1)">角色管理</dd></router-link>
									<router-link to="/home/auth_rule"><dd id='menu42' :class="activevalue[42]?activeclass:''"  @click="menuactive(42,4,2)">权限规则</dd></router-link>
									<router-link to="/home/company_user"><dd id='menu43' :class="activevalue[43]?activeclass:''"  @click="menuactive(43,4,3)">供应商账户</dd></router-link>
								</div>
							</el-collapse-transition>

							<dt @click="menush(2)">
								<span class="el-icon-s-tools" id="menu2"> 系统设置</span>
								<span class="fr derection" :class="menuvalue[2]?derectionup:derectiondown"></span>
							</dt>
							<el-collapse-transition>
								<div v-show="menuvalue[2]">
									<router-link to="/home/region"><dd id='menu21' :class="activevalue[21]?activeclass:''" @click="menuactive(21,2,1)">区域管理</dd></router-link>
								</div>
							</el-collapse-transition>							
							
							
							
						</dl>
					</div>
				</el-col> <!--menu e-->
				
				<el-col  :xs="18" :sm="19" :md="20" :lg="21" :xl="22">  <!--main s-->
				
					<el-col  :span='24' class="createtitle">
						<span>{{menuonetitle}} </span>
						<span class="el-icon-arrow-right"></span>
						<span> {{menutwotitle}}</span>
					</el-col>
					
					<el-col :span='24' class="homemain">
						<router-view></router-view>
					</el-col>
				</el-col> <!--main e-->
			</el-col> <!--content e-->
			
		</el-row>
	</div>
</template>


<script>
	import wincontrol from '@/assets/js/wincontrol.js';
	import { mapState } from 'vuex';
	import { mapMutations } from 'vuex';
	
	export default {
		name: 'home',
		data(){
			return {
				name:'', //供应商名字
				menuvalue:[], //菜单层级
				derectiondown:'el-icon-arrow-down', //一级菜单向上箭头
				derectionup:'el-icon-arrow-up', //一级菜单向下箭头
				activevalue:[], //激活菜单数组
				activeclass:'activecolor' //选中二级菜单样式
			}
		},
		components: {
			
		},
		computed: {
			...mapState([
				'menuonetitle', //home主页一级标题
				'menutwotitle'  //home主页二级标题
			])
		},
		mounted(){
			/**
			 * 获取供应商名称
			 */
			let account=JSON.parse(localStorage.getItem("company"));
			this.name=account['name'];
			
			/**
			 * 控制窗口高度
			 */
			wincontrol.wincontrol();
		},
		methods: {
			/**
			 * vuex存储共享数据
			 */
			...mapMutations([
				'menutitle', //存储菜单标题
			]),
			  
			/**
			 * menu折叠效果
			 * @param {int} val 一级菜单索引
			 */
			menush(val){
				//控制一级菜单折叠
				let self=this;
				this.menuvalue.forEach((value,index)=>{
					if(index!=val){
						self.$set(this.menuvalue,index,false);
					}
				});
				this.$set(this.menuvalue,val,!this.menuvalue[val]);
				//所有一级菜单折叠后，将二级菜单状态初始化到最初状态
				if(!this.menuvalue[val]){
					this.activevalue.forEach((value,index)=>{
						self.$set(this.activevalue,index,false);
					});
				}
			},
			
			/**
			 * menu折叠效果
			 * @param {int} val 被选中状态样式数组索引
			 * @param {string} onetitle 一级标题
			 * @param {string} twotitle 二级标题
			 */
			menuactive(val,onetitle,twotitle){
				//控制选中menu样式
				let self=this;
				this.activevalue.forEach((value,index)=>{
					if(index!=val){
					 self.$set(this.activevalue,index,false);
					}
				});
				this.$set(this.activevalue,val,true);
				//设置标题
				let titleobject=new Object();
				titleobject.onetitletext=document.getElementById('menu'+onetitle).innerText;
				titleobject.twotitletext=document.getElementById('menu'+onetitle+''+twotitle).innerText;		
				this.menutitle(titleobject);
			}
		}
	}
</script>

<style>	
    .homemain{
		overflow-y:scroll;
    }
	.homeheader{
		background-color:#003366;
	}
	.hometitle{
		line-height: 50px;
		text-indent: 10px;
	}
	.homemenu{
		padding: 10px 10px;
		border-right: 1px solid #E3E0D5;
		background-color: #EEEEEE;
	}
	.derection{
		padding: 10px 5px; 
	}
	dt,dd{
		line-height: 35px;
		border-bottom: 1px solid #E3E0D5;
		cursor: pointer;
	}
	a:hover{
		color:#1D72B9;
	}
	a {
		text-decoration: none;
		color:#000;
	}
	.router-link-active {
		text-decoration: none;
	}
	.activecolor{
		color:#1D72B9;
	}
	.createtitle{
		border-bottom: 1px solid #E8EAED;
		line-height: 40px;
		background-color: #FBFCFC;
		padding: 0 10px;
		font-size: 1em;
	}
</style>
