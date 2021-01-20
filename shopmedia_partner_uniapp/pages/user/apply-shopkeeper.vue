<template>
	<view class="content">

		<view>
			<view class="input-line-height">
				<text class="input-line-height-1">认证码</text>
				<input class="input-line-height-2" type="text" v-model="applycode" placeholder="填写认证码(业务经理处获取)" />
			</view>
			<view class="input-line-height">
				<text class="input-line-height-1">申请者</text>
				<input class="input-line-height-2" type="text" v-model="this.userInfo.phone" disabled="true" />
			</view>
		</view>

		<view>
			<uni-card mode="style" thumbnail="https://sustock-app.oss-cn-chengdu.aliyuncs.com/applydevice.jpg" title="店通传媒"
			 :is-shadow="true">
				<view style="text-align: left;">安装店通智能广告屏的合作者可以获得该台智能屏30%的广告收入，并享受独有的广告投放3折优惠。店通坚持共赢共享，互利互惠的宗旨原则，为店铺合作者的产品和服务投广告做推广，店铺合作者还能享受到店内广告屏的广告收入，快来为你的店铺申请安装店通智能屏吧！</view>
			</uni-card>
		</view>

		<view class="tipcon">申请提交后，我们的业务人员会与您主动联系，以确保您的店铺是否符合智能广告屏的安装条件。</view>

		<view>
			<button class="login-button" @click="applyShopkeeper()">申请安装</button>
		</view>

	</view>
</template>

<script>
	import {mapState} from 'vuex';

	export default {
		data() {
			return {
				applycode: 111111
			}
		},
		computed: {
			...mapState(['forcedLogin', 'userInfo', 'commonheader'])
		},
		onLoad() {},
		methods: {
			/**
			 * 申请成为店家
			 */
			applyShopkeeper() {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/apply_shopkeeper',
					data: {
						user_id: this.userInfo.user_id,
						invitation_code: this.applycode
					},
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'POST',
					success: function(res) {
						if (res.data.status == 1) { //申请成功
							uni.showModal({
								title: '恭喜',
								content: '申请已提交,请等待审核...',
								showCancel: false,
								success: function(res) {
									if (res.confirm) { //申请成功跳转首页
										uni.reLaunch({
											url: '../main/main'
										});
									}
								}
							});
						} else { //失败
							uni.showToast({
								icon: 'none',
								title: res.data.words,
								duration: 2000
							});
						}
					}
				})
			}
		}
	}
</script>

<style>
	.content {
		margin: 0;
		padding: 0;
		text-align: center;
	}

	.input-line-height {
		display: flex;
		align-items: center;
		line-height: 50px;
		border-bottom: 1px solid #ECECEC;
		font-size: 16px;
	}

	.input-line-height-1 {
		flex: 1;
		padding-left: 5px;
		text-align: right;
	}

	.input-line-height-2 {
		flex: 3;
		font-size: 16px;
		text-align: left;
		padding-left: 15px;
	}

	.login-button {
		background-color: #409EFF;
		color: #fff;
		margin: 40px 10px 20px 10px;
	}

	.tipcon {
		padding: 10px 10px 0px 10px;
		text-align: left;
	}
</style>
