<template>
	<div class="home">
		<el-row>
			
			<el-col :span="24" class="homeheader color-white"> <!--header s-->
				<el-col :span="5">
				   <div class="hometitle" v-if="name">{{name}}</div>
				   <div class="hometitle" v-else>店通传媒总平台</div>
				</el-col>
			</el-col> <!--header e-->

			<el-col :span="24"> <!--content s-->
				<el-col  :xs="6" :sm="5" :md="4" :lg="3" :xl="2"> <!-- menu s -->
					<div class="homemenu">
						<dl class="m0">
							
							<dt @click="menush(3)">
								<span class="el-icon-office-building" id="menu3"> 分公司管理</span>
								<span class="fr derection" :class="menuvalue[3]?derectionup:derectiondown"></span>
							</dt>
							<el-collapse-transition>
								<div v-show="menuvalue[3]">
									<router-link to="/home/companycreate"><dd id='menu31' :class="activevalue[31]?activeclass:''" @click="menuactive(31,3,1)">新增分公司</dd></router-link>
									<router-link to="/home/company"><dd id='menu32' :class="activevalue[32]?activeclass:''" @click="menuactive(32,3,2)">分公司列表</dd></router-link>
								</div>
							</el-collapse-transition>
							
							
							<dt @click="menush(1)">
								<span class="el-icon-mobile" id="menu1"> 广告屏管理</span>
								<span class="fr derection" :class="menuvalue[1]?derectionup:derectiondown"></span>
							</dt>
							<el-collapse-transition>
								<div v-show="menuvalue[1]">
									<router-link to="/home/adddevice"><dd id='menu11' :class="activevalue[11]?activeclass:''"  @click="menuactive(11,1,1)">新增广告屏</dd></router-link>
									<router-link to="/home/device"><dd id='menu12' :class="activevalue[12]?activeclass:''"  @click="menuactive(12,1,2)">广告屏列表</dd></router-link>
								</div>
							</el-collapse-transition>
							
							
							<dt @click="menush(6)">
								<span class="el-icon-s-flag" id="menu6"> 广告管理</span>
								<span class="fr derection" :class="menuvalue[6]?derectionup:derectiondown"></span>
							</dt>
							<el-collapse-transition>
								<div v-show="menuvalue[6]">
									<router-link to="/home/ad"><dd id='menu61' :class="activevalue[61]?activeclass:''"  @click="menuactive(61,6,1)">广告</dd></router-link>
									<router-link to="/home/ad_combo"><dd id='menu62' :class="activevalue[62]?activeclass:''"  @click="menuactive(62,6,2)">广告套餐</dd></router-link>
									<router-link to="/home/ad_combo_order"><dd id='menu63' :class="activevalue[63]?activeclass:''"  @click="menuactive(63,6,3)">广告套餐订单</dd></router-link>
									<router-link to="/home/ad_case"><dd id='menu64' :class="activevalue[64]?activeclass:''"  @click="menuactive(64,6,4)">广告案例</dd></router-link>
								</div>
							</el-collapse-transition>
							
							
							<dt @click="menush(5)">
								<span class="el-icon-user" id="menu5"> 用户管理</span>
								<span class="fr derection" :class="menuvalue[5]?derectionup:derectiondown"></span>
							</dt>
							<el-collapse-transition>
								<div v-show="menuvalue[5]">
									<router-link to="/home/user_role"><dd id='menu51' :class="activevalue[51]?activeclass:''"  @click="menuactive(51,5,1)">用户角色</dd></router-link>
									<!-- <router-link to="/home/user"><dd id='menu52' :class="activevalue[52]?activeclass:''"  @click="menuactive(52,5,2)">全部用户</dd></router-link> -->
									<router-link to="/home/user_salesman"><dd id='menu53' :class="activevalue[53]?activeclass:''"  @click="menuactive(53,5,3)">业务员</dd></router-link>
									<router-link to="/home/user_partner"><dd id='menu54' :class="activevalue[54]?activeclass:''"  @click="menuactive(54,5,4)">广告屏合作商</dd></router-link>
									<router-link to="/home/user_shopkeeper"><dd id='menu55' :class="activevalue[55]?activeclass:''"  @click="menuactive(55,5,5)">店家</dd></router-link>
									<router-link to="/home/user_advertiser"><dd id='menu56' :class="activevalue[56]?activeclass:''"  @click="menuactive(56,5,6)">广告主</dd></router-link>
								</div>
							</el-collapse-transition>


							<dt @click="menush(7)">
								<span class="el-icon-notebook-2" id="menu7"> 订单管理</span>
								<span class="fr derection" :class="menuvalue[7]?derectionup:derectiondown"></span>
							</dt>
							<el-collapse-transition>
								<div v-show="menuvalue[7]">
									<router-link to="/home/partner_order"><dd id='menu71' :class="activevalue[71]?activeclass:''"  @click="menuactive(71,7,1)">合作商订单</dd></router-link>
								</div>
							</el-collapse-transition>
							
							<dt @click="menush(10)">
								<span class="el-icon-present" id="menu10"> 活动管理</span>
								<span class="fr derection" :class="menuvalue[10]?derectionup:derectiondown"></span>
							</dt>
							<el-collapse-transition>
								<div v-show="menuvalue[10]">
									<router-link to="/home/activity"><dd id='menu101' :class="activevalue[101]?activeclass:''"  @click="menuactive(101,10,1)">活动</dd></router-link>
									<router-link to="/home/act_prize"><dd id='menu102' :class="activevalue[102]?activeclass:''"  @click="menuactive(102,10,2)">奖品</dd></router-link>
									<router-link to="/home/act_raffle"><dd id='menu103' :class="activevalue[103]?activeclass:''"  @click="menuactive(103,10,3)">中奖纪录</dd></router-link>
								</div>
							</el-collapse-transition>
							
							<dt @click="menush(8)">
								<span class="el-icon-news" id="menu8"> 新闻管理</span>
								<span class="fr derection" :class="menuvalue[8]?derectionup:derectiondown"></span>
							</dt>
							<el-collapse-transition>
								<div v-show="menuvalue[8]">
									<router-link to="/home/news"><dd id='menu81' :class="activevalue[81]?activeclass:''"  @click="menuactive(81,8,1)">新闻</dd></router-link>
								</div>
							</el-collapse-transition>
							
							<dt @click="menush(9)">
								<span class="el-icon-message" id="menu9"> 用户反馈</span>
								<span class="fr derection" :class="menuvalue[9]?derectionup:derectiondown"></span>
							</dt>
							<el-collapse-transition>
								<div v-show="menuvalue[9]">
									<router-link to="/home/feedback"><dd id='menu91' :class="activevalue[91]?activeclass:''"  @click="menuactive(91,9,1)">投诉建议</dd></router-link>
								</div>
							</el-collapse-transition>

							<dt @click="menush(4)">
								<span class="el-icon-user-solid" id="menu4"> 系统管理员</span>
								<span class="fr derection" :class="menuvalue[4]?derectionup:derectiondown"></span>
							</dt>
							<el-collapse-transition>
								<div v-show="menuvalue[4]">
									<router-link to="/home/auth_group"><dd id='menu41' :class="activevalue[41]?activeclass:''"  @click="menuactive(41,4,1)">角色</dd></router-link>
									<router-link to="/home/auth_rule"><dd id='menu42' :class="activevalue[42]?activeclass:''"  @click="menuactive(42,4,2)">权限</dd></router-link>
									<router-link to="/home/admin"><dd id='menu43' :class="activevalue[43]?activeclass:''"  @click="menuactive(43,4,3)">管理员</dd></router-link>
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
						
						<!-- 以下是动态数据 menus -->
						<dl class="m0" v-for="(item, index) in menus" :key="index" v-if="item.level == 1" style="color: #C0C4CC;">
							<dt @click="menush(item.id)">
								<span :class="item.icon" :id="'menu' + item.id"> {{item.title}}</span>
								<span class="fr derection" :class="menuvalue[item.id]?derectionup:derectiondown"></span>
							</dt>
							<el-collapse-transition>
								<div v-show="menuvalue[item.id]">
									<router-link v-for="(value, key) in menus" :key="key" v-if="value.pid == item.id" :to="value.name">
										<dd :id="'menu' + value.id" :class="activevalue[value.id] ? activeclass : ''" @click="menuactive(value.id, item.id, index)">{{value.title}}</dd>
									</router-link>
								</div>
							</el-collapse-transition>
						</dl>
					</div>
				</el-col> <!-- menu e -->
				
				<el-col  :xs="18" :sm="19" :md="20" :lg="21" :xl="22">  <!--main s-->

					<!-- <el-col  :span='24' class="createtitle">
						<span>{{menuonetitle}} </span>
						<span class="el-icon-arrow-right"></span>
						<span> {{menutwotitle}}</span>
					</el-col> -->
					
					<el-col  :span='24' class="createtitle">
						<span>{{onetitle}} </span>
						<span class="el-icon-arrow-right"></span>
						<span> {{twotitle}}</span>
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
				activeclass:'activecolor', //选中二级菜单样式
				onetitle:localStorage.getItem("onetitle"),//一级标题
				twotitle:localStorage.getItem("twotitle"),//二级标题
				menus: [] // 权限规则菜单（侧栏导航）
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
			// 获取供应商名称
			let account=JSON.parse(localStorage.getItem("admin_user"));
			this.name=account['company_name'];
			
			// 控制窗口高度
			wincontrol.wincontrol();
			
			// 获取权限规则菜单
			this.getAuthRuleMenus();
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
				//vuex设置标题(参考)
				let titleobject=new Object();
				titleobject.onetitletext=document.getElementById('menu'+onetitle).innerText;
				titleobject.twotitletext=document.getElementById('menu'+onetitle+''+twotitle).innerText;		
				this.menutitle(titleobject);
				//local设置标题
				localStorage.setItem("onetitle",titleobject.onetitletext);
				localStorage.setItem("twotitle",titleobject.twotitletext);
				this.onetitle=localStorage.getItem("onetitle");
				this.twotitle=localStorage.getItem("twotitle");
				
				
			},
			
			/**
			 * 获取权限规则菜单
			 */
			getAuthRuleMenus() {
				let self = this;
				this.$axios.get(this.$url + 'auth_rule_menus', {
					// headers请求头，不能注释掉，否则会出错
					headers: {
						'admin-user-id': JSON.parse(localStorage.getItem('admin_user')).id,
						'admin-user-token': JSON.parse(localStorage.getItem('admin_user')).token
					}
				})
				.then(function(res) {
					if (res.data.status == 1) {
						// 权限规则菜单
						self.menus = res.data.data;
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
