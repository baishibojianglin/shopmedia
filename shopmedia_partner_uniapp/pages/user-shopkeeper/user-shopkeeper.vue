<template>
	<view class="uni-page-body">
		<view>
			<!-- <uni-card is-shadow> -->
				<map class="map" :longitude="longitude" :latitude="latitude" :scale="9" :markers="markers" :enable-satellite="false"></map>
			<!-- </uni-card> -->
		</view>
		
		<view class="uni-common-mt mb">
			<uni-card v-if="!shopCount" is-shadow>
				<view class="uni-center">有店铺，想安装智能广告屏？</view>
				<view><button  class="main-color ask-mt">有什么好处</button></view>
			    <uni-list>
			        <uni-list-item title="获得30%的广告收入" :show-arrow="false"></uni-list-item>
			        <uni-list-item title="超低优惠打广告" :show-arrow="false"></uni-list-item>
			        <uni-list-item title="利用店通智能数据分析提升销量" :show-arrow="false"></uni-list-item>
			        <uni-list-item title="遇到支持你的生意伙伴"  :show-arrow="false"></uni-list-item>
			    </uni-list>
				<view><button @click="shopask()" class="main-color ask-mt">联系安装</button></view>
			</uni-card>
			<uni-card v-else  v-for="(item, index) in shopList" :key="index" note="Tips" is-shadow>
				<uni-list>
					<uni-list-item :title="item.shop.shop_name" :note="Number(item.device.length) != 0 ? '合计 ' + Number(item.device.length) + ' 台' : ''" rightText="导航" @click="openLocation(item)"></uni-list-item>					
					<uni-grid v-if="item.device.length != 0" class="uni-center" :column="3" :showBorder="true" :square="false">
						<uni-grid-item>
							<text class="">广告屏编号</text>
						</uni-grid-item>
						<uni-grid-item>
							<text class="">累计收入(￥)</text>
						</uni-grid-item>
						<uni-grid-item>
							<text class="">今日收入(￥)</text>
						</uni-grid-item>
					</uni-grid>
					<uni-grid v-if="item.device.length != 0" v-for="(value, key) in item.device" :key="key" class="uni-center" :column="3" :showBorder="true" :square="false">
						<uni-grid-item>
							<text class="">{{value.device_id}}</text>
						</uni-grid-item>
						<uni-grid-item>
							<text class="color-red">{{value.total_income}}</text>
						</uni-grid-item>
						<uni-grid-item>
							<text class="color-red">{{value.today_income}}</text>
						</uni-grid-item>
					</uni-grid>
				</uni-list>
				<template slot="footer">
					<view class="footer-box">
 						<view v-if="!item.shop.party_b_signature" @click.stop="footerClick(item)"> <button class="mini-btn" :type="item.shop.party_b_signature ? 'default' : 'warn'" size="mini" :plain="false">{{item.shop.party_b_signature ? '查看协议' : '签署协议'}}</button></view>
					</view>
				</template>
			</uni-card>
						
		</view>
		
	</view>
</template>

<script>
	import {mapState} from 'vuex';
	
	export default {
		data() {
			return {
				userId: '', // 用户ID
				roleId: '', // 用户角色ID			
				latitude: 30.657420, //纬度
				longitude: 104.065840, //经度
				markers: [], //地图图标
				shopCount: 0, // 店家店铺数量
				shopList: [] // 店家店铺列表
			}
		},
		computed: {
			...mapState(['hasLogin', 'forcedLogin', 'userInfo', 'commonheader'])
		},
		onLoad(event) {
			// 获取参数
			this.userId = event.user_id;
			this.roleId = event.role_id;
		},
		onNavigationBarButtonTap(e) {
			this.$common.actionSheetTap();
		},
		onShow(){
			this.getShopList();
		},
		methods: {
			/**
			 * 咨询安装广告屏
			 */
			shopask(){
				uni.makePhoneCall({
					phoneNumber: '13693444308'
				});
			},
			/**
			 * 获取店家拥有的店铺列表
			 */
			getShopList() {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/shopkeeper_shop_list',
					data: {
						user_id: this.userId
					},
					method:'POST',
					header: {
						'commonheader': this.$store.state.commonheader,
						'access-user-token': this.userInfo.token
					},
					success: (res) => {
						self.shopList=res.data
						self.shopCount = self.shopList.length;
					},
					fail(error) {
						uni.showToast({
							icon: 'none',
							title: '请求异常'
						});
					}
				});
			},
			
			/**
			 * 查看位置
			 * @param {Object} item
			 */
			openLocation(item) {
				// 使用应用内置地图查看位置
				uni.openLocation({
					longitude: Number(item.shop.longitude),
					latitude: Number(item.shop.latitude),
					name: item.shop_name,
					address: item.address
				});
			},
			
			/**
			 * 跳转签署店铺合作协议页面
			 * @param {Object} item
			 */
			footerClick(item) {
				let device_count=item.device.length;
				let total_price=0;
				item.device.forEach((value,index)=>{
					total_price=total_price+parseFloat(value.sale_price);
				})
				uni.navigateTo({
					url: '/pages/shop/shop-agreement?shop_name='+item.shop.shop_name+'&device_count='+device_count+'&address='+item.shop.address+'&total_price='+total_price+'&shop_id='+item.shop.shop_id
				})
			}
		}
	}
</script>

<style>
	.map {
		width: 100%;
		height: 400rpx;
	}
	
	/* uni-card s */
	.footer-box {
		/* #ifndef APP-NVUE */
		display: flex;
		/* #endif */
		justify-content: flex-end;
		flex-direction: row;
	}
	/* uni-card e */
	.ask-mt{
		margin-top: 10px;
	}
</style>
