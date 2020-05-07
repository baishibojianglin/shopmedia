<template>
	<view class="uni-page-body">
		<view>
			<uni-card is-shadow>
				<map class="map" :longitude="longitude" :latitude="latitude" :scale="9" :markers="markers" :enable-satellite="false"></map>
			</uni-card>
		</view>
		
		<view class="uni-common-mt mb">
			<uni-card v-for="(item, index) in shopList" :key="index" note="Tips" is-shadow>
				<uni-list>
					<uni-list-item :title="item.shop_name" :note="Number(item.device_list.length) != 0 ? '合计 ' + Number(item.device_list.length) + ' 台' : ''" rightText="导航" @click="openLocation(item)"></uni-list-item>
					<uni-grid v-if="item.device_list.length != 0" class="uni-center" :column="3" :showBorder="true" :square="false">
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
					<uni-grid v-if="item.device_list.length != 0" v-for="(value, key) in item.device_list" :key="key" class="uni-center" :column="3" :showBorder="true" :square="false">
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
					<uni-grid v-if="item.device_list.length == 0" class="uni-center" :column="1" :showBorder="false" :square="false">
						<uni-grid-item>
							<text>暂无数据</text>
						</uni-grid-item>
					</uni-grid>
				</uni-list>
				
				<template slot="footer">
					<view class="footer-box">
						<view @click.stop="footerClick(item)"> <button class="mini-btn" :type="item.party_b_signature ? 'default' : 'warn'" size="mini" :plain="false">{{item.party_b_signature ? '查看协议' : '签署协议'}}</button></view>
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
			
			this.getShopList();
		},
		onShow(){
			this.getShopList();
		},
		methods: {
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
					header: {
						'commonheader': this.$store.state.commonheader,
						'access-user-token': this.userInfo.token
					},
					success: (res) => {
						self.shopCount = res.data.data.total;
						self.shopList = res.data.data.data;
						self.shopList.forEach((item, index) => {
							self.$set(self.markers, index, {
								title: item.shop_name,
								longitude: item.longitude,
								latitude: item.latitude
							});
						})
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
					longitude: Number(item.longitude),
					latitude: Number(item.latitude),
					name: item.shop_name,
					address: item.address
				});
			},
			
			/**
			 * 跳转签署店铺合作协议页面
			 * @param {Object} item
			 */
			footerClick(item) {
				uni.navigateTo({
					url: '/pages/shop/shop-agreement?shop=' + encodeURIComponent(JSON.stringify(item))
				})
			}
		}
	}
</script>

<style>
	.map {
		width: 100%;
		height: 320rpx;
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
</style>
