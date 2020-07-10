<template>
	<view class="uni-page-body">
		<uni-card title="我的邀请码" :isShadow="true">
			<!-- <view class="uni-title uni-bold uni-common-mb">我的邀请码</view> -->
			<uni-grid class="uni-center" :column="2" :showBorder="false" :square="false">
				<uni-grid-item>
					<text class="uni-text-small">客户邀请码</text>
					<text class="uni-bold">{{advertiserSalesman.invitation_code}}</text>
				</uni-grid-item>
				<uni-grid-item>
					<text class="uni-text-small">业务员邀请码</text>
					<text class="uni-bold">{{advertiserSalesman.son_invitation_code}}</text>
				</uni-grid-item>
			</uni-grid>
		</uni-card>
		
		<uni-card title="广告业务" :isShadow="true">
			<uni-grid class="uni-center" :column="4" :showBorder="false" :square="false">
				<uni-grid-item>
					<text class="uni-text-small">合计</text>
					<text class="uni-bold">{{adCount.total_ad_count}}</text>
				</uni-grid-item>
				<uni-grid-item>
					<text class="uni-text-small">正常</text>
					<text class="uni-bold">{{adCount.enable_ad_count}}</text>
				</uni-grid-item>
				<uni-grid-item>
					<text class="uni-text-small">待审核</text>
					<text class="uni-bold">{{adCount.pending_ad_count}}</text>
				</uni-grid-item>
				<uni-grid-item>
					<text class="uni-text-small">驳回</text>
					<text class="uni-bold">{{adCount.reject_ad_count}}</text>
				</uni-grid-item>
			</uni-grid>
		</uni-card>
		
		<view class="uni-padding-wrap">
			<navigator url="/pages/ad-combo/ad-combo">
				<button size="" class="bg-main-color color-white">
					<text>广告套餐</text>
					<text class="uni-icon uni-icon-arrowright fon14"></text>
				</button>
			</navigator>
		</view>
		
		<uni-card title="我的资金" :isShadow="true">
			<uni-grid class="uni-center" :column="3" :showBorder="false" :square="false">
				<uni-grid-item>
					<text class="uni-text-small">总收入</text>
					<text class="uni-bold">￥{{advertiserSalesman.income}}</text>
				</uni-grid-item>
				<uni-grid-item>
					<text class="uni-text-small">已提现</text>
					<text class="uni-bold">￥{{advertiserSalesman.cash}}</text>
				</uni-grid-item>
				<uni-grid-item>
					<text class="uni-text-small">余额</text>
					<text class="uni-bold">￥{{advertiserSalesman.money}}</text>
				</uni-grid-item>
			</uni-grid>
		</uni-card>
		
		<uni-card title="我的团队" :isShadow="true">
			<view class="uni-list">
				<view class="uni-list-cell">
					<text>业务员电话</text>
					<text>广告数量</text>
				</view>
			</view>
			<view class="uni-list" v-for="(item, index) in advertiserSalesmanList" :key="index">
				<view class="uni-list-cell">
					<text>{{item.phone}}</text>
					<text>{{item.ad_count}}</text>
				</view>
			</view>
		</uni-card>
	</view>
</template>

<script>
	import {mapState, mapMutations} from 'vuex';
	
	export default {
		data() {
			return {
				advertiserSalesman: [], // 广告主业务员信息
				adCount: [], // 统计广告数量
				advertiserSalesmanList: [] // （下级）广告主业务员列表
			}
		},
		computed: mapState(['forcedLogin', 'hasLogin', 'userInfo', 'commonheader']),
		onLoad() {
			this.getAdvertiserSalesman();
			this.getAdvertiserSalesmanAdCount();
			this.getAdvertiserSalesmanList();
		},
		onNavigationBarButtonTap(e) {
			this.$common.actionSheetTap();
		},
		methods: {
			/**
			 * 获取指定广告主业务员信息
			 */
			getAdvertiserSalesman() {
				let self=this;
				uni.request({
					url: this.$serverUrl + 'api/get_advertiser_salesman',
					data: {
						user_id: this.userInfo.user_id
					},
					method: 'GET',
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					success: (res) => {
						if (res.data.status == 1) {
							self.advertiserSalesman = res.data.data;
						}
					}
				});	
			},
			
			/**
			 * 统计广告主业务员广告数
			 */
			getAdvertiserSalesmanAdCount() {
				let self=this;
				uni.request({
					url: this.$serverUrl + 'api/get_advertiser_salesman_ad_count',
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					success: (res) => {
						if (res.data.status == 1) {
							self.adCount = res.data.data;
						}
					}
				});
			},
			
			/**
			 * 获取（下级）广告主业务员列表
			 */
			getAdvertiserSalesmanList() {
				let self=this;
				uni.request({
					url: this.$serverUrl + 'api/get_advertiser_salesman_list',
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					success: (res) => {
						if (res.data.status == 1) {
							self.advertiserSalesmanList = res.data.data;
						}
					}
				});
			}
		}
	}
</script>

<style>

</style>
