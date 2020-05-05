<template>
	<view class="wrapper uni-padding-wrap">
		<!-- 协议书 s -->
		<view class="blod uni-center title">
			智能广告屏合作协议书
		</view>

		<view class="content-con">
			<view class="content">
				<text class="content-left blod">甲方：</text>
				<text class="content-right sign-border">四川狄霖店通传媒有限公司</text>
			</view>
			<view class="content">
				<text class="content-left blod">乙方：</text>
				<input class="content-right sign-border" :focus="true" placeholder="请填写姓名" v-model="user_name" />
			</view>

			<view class="text-space">甲、乙双方依据中华人民共和国相关法律法规，经平等、友好协商达成如下合作协议：</view>
			<view class="blod">一、合作内容</view>
			<view class="content-text">
				<text class="content-left-text">设备编号：</text>
				<text class="content-right sign-border blod">{{'SUSTOCK-'+device.device_id}}</text>
			</view>
			<view class="content-text">
				<text class="content-left-text">安装位置：</text>
				<text class="content-right sign-border-text">{{device.address+device.shopname}}</text>
			</view>
			<view>
				上述智能广告屏设备安装到店总成本为（智能屏生产成本、店通智能数据系统研发成本、运至店铺的运输成本、店铺安装的人工成本等）
				<text class="text-word blod">{{device.sale_price}}</text>
				元，甲、乙双方各出资<input :disabled="true" class="text-word blod" v-model="share" /> % 合作经营，甲方出资
				<input :disabled="true" class="text-word blod" v-model="company_out" />
				元，乙方出资
				<input :disabled="true" class="text-word blod" v-model="person_out" />
				元，甲、乙双方按对应出资比例分担和享有该广告屏的经营成本和广告收入。
			</view>

			<view class="blod">二、权利与义务</view>
			<view>1、甲方承担设备的运营、管理、维修责任;</view>
			<view>2、乙方需配合甲方完成日常运营维护的必要工作；</view>
			<view>3、甲方负责广告的业务开拓、制作、剪辑、投放工作；</view>
			<view>4、甲方负责终端店智能广告系统的研发、系统升级、系统维护和系统安全防护等工作。</view>
			<view>5、当终端店设备损坏时，由甲方出资生产新的智能广告屏重新安装到终端店铺；</view>
			<view>6、当终端店铺发生铺面转让、店铺倒闭等造成安装在该店的智能广告屏无法正常经营时，由甲方负责开拓新的店铺安装该台智能广告屏，保证该台智能广告屏恢复正常的广告投放业务。</view>
			<view>7、智能广告屏的广告利润收入，甲乙双方各占{{share}}%；</view>
			<view>8、甲、乙双方均不得从事有损合作经营的活动；</view>
			<view>9、甲、乙双方签订协议后3个工作日内，须将款项转入成都商市通电子商务有限公司公户，账户信息如下：</view>
			<view><text class="blod">账户名称：成都商市通电子商务有限公司</text></view>
			<view><text class="blod">账&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;号：8028 2001 0122 7004 51</text></view>
			<view><text class="blod">开 &nbsp;户 &nbsp;行：恒丰银行股份有限公司成都金牛支行</text></view>
			<view class="blod">三、不可抗力因素</view>
			<view>1、发生地震、滑坡、泥石流、重大公共卫生疫情等不可抗力因素导致智能屏无法正常投放广告的，甲、乙双方承担各自应负担的损失，无须承担其他责任；</view>
			<view>2、乙方可以根据自身情况提请退出合作经营，退出合作经营时甲方应退还乙方扣除设备折旧、运营成本后乙方剩余的出资款，退出后乙方不再承担合作协议约定的权利与义务；</view>
			<view>A、合作协议生效起3个月内不能退出经营，否则甲方有权不退乙方出资款；</view>
			<view>B、12个月内退出经营时，扣除出资款30%作为设备折旧和运营成本；</view>
			<view>3、当遭遇公司破产清算等情况时，该智能屏设备所有权归乙方所有。</view>
			<view class="blod">四、其他</view>
			<view>1、当甲、乙双方发生纠纷且无法协商解决时，提交甲方所在地仲裁委员会或人民法院依据相关法律法规办理；</view>
			<view>2、如有本协议未约定的其他事项应签署补充协议约定。</view>
		</view>

		<view class="sign-con">
			<view class="sign-con-item">
				<view class="blod">甲方（公章）：</view>
				<view></view>
			</view>
			<view class="sign-con-item">
				<view class="blod">乙方（签名）：</view>
				<view>
					<view class="showimg">
						<image v-if="showimg" :src="showimg" mode=""></image>
						<text v-else class="uni-center"></text>
					</view>
				</view>
			</view>
		</view>
		<!-- 协议书 e -->

		<!--电子签名 s-->
		<view class="handBtn" v-if="false">
			<view class="slide-wrapper">
				<text>选择粗细</text>
				<slider @change="updateValue" value="50" show-value class="slider" step="25" />
			</view>
			<view class="color">
				<text>选择颜色</text>
				<image @click="selectColorEvent('black')" :src="selectColor === 'black' ? '../../static/img/color_black_selected.png' : '../../static/img/color_black.png'"
				 :class="selectColor === 'black' ? 'color_select' : ''" class="black-select"></image>
				<image @click="selectColorEvent('red')" :src="selectColor === 'red' ? '../../static/img/color_red_selected.png' : '../../static/img/color_red.png' "
				 :class="selectColor === 'red' ? 'color_select' : ''" class="red-select"></image>
			</view>
		</view>
		<view class="handCenter">
			<canvas class="handWriting" disable-scroll="true" @touchstart="uploadScaleStart" @touchmove="uploadScaleMove"
			 @touchend="uploadScaleEnd" @tap="mouseDown" canvas-id="handWriting">
			</canvas>
		</view>

		<view class="buttons sign-con" style="margin-bottom: 100px;">
			<button @click="retDraw" class="sign-con-item" style="margin: 0 15px;">重写</button>
			<button @click="subCanvas" class="sign-con-item" style="margin: 0 15px;">确认</button>
		</view>
		<!--电子签名 e-->

		<view class="goods-carts">
			<uni-goods-nav :options="options" :button-group="buttonGroup" :fill="true" @click="onClick" @buttonClick="buttonClick" />
		</view>
	</view>
</template>

<script>
	import {mapState} from 'vuex';
	import Handwriting from "../../static/js/signature.js"

	export default {
		data() {
			return {
				csPhone: '', // 客服电话
				device: '', // 广告屏信息

				/* GoodsNav 商品导航 s */
				options: [{
					icon: 'headphones',
					text: '联系客服'
				}],
				buttonGroup: [{
					text: '提交协议',
					backgroundColor: '#409EFF',
					color: '#fff'
				}],
				/* GoodsNav 商品导航 e */

				// 电子签名
				lineColor: 'black',
				slideValue: 50,
				handwriting: '',
				selectColor: 'black',
				color: '',
				showimg: '', // 签名图片地址
				share_popup: false,

				// 协议书
				user_name: '', // 乙方用户姓名
				share: 50, // 份额
				company_out: 0, // 甲方出资额
				person_out: 0, // 乙方出资额
			}
		},
		computed: {
			...mapState(['hasLogin', 'forcedLogin', 'userInfo', 'commonheader'])
		},
		onNavigationBarButtonTap(e) {
			this.$common.actionSheetTap();
		},
		onLoad(event) {
			this.csPhone = event.cs_phone;
			this.device = JSON.parse(decodeURIComponent(event.device));

			// 电子签名
			this.$nextTick(function() {
				this.handwriting = new Handwriting({
					lineColor: this.lineColor,
					slideValue: this.slideValue, // 0, 25, 50, 75, 100 
					canvasName: 'handWriting',
				})
			})

			// 协议书
			this.company_out = (this.device.sale_price / 2).toFixed(2);
			this.person_out = (this.device.sale_price / 2).toFixed(2);
		},
		methods: {
			/* 电子签名 s */
			/**
			 * 选择画笔颜色
			 * @param {Object} event
			 */
			selectColorEvent(event) {
				this.selectColor = event;
				if (event == 'black') {
					this.color = '#1A1A1A'
				} else if (event == 'red') {
					this.color = '#ca262a'
				}
				this.handwriting.selectColorEvent(this.color)
			},
			retDraw() {
				this.handwriting.retDraw()
				this.showimg = '';
			},
			/**
			 * 笔迹粗细滑块
			 * @param {Object} e
			 */
			updateValue(e) {
				//console.log(e.detail);
				this.slideValue = e.detail.value;
				this.handwriting.selectSlideValue(this.slideValue);
			},
			uploadScaleStart(event) {
				this.handwriting.uploadScaleStart(event)
			},
			uploadScaleMove(event) {
				this.handwriting.uploadScaleMove(event)
			},
			uploadScaleEnd(event) {
				this.handwriting.uploadScaleEnd(event)
			},
			subCanvas() {
				this.handwriting.saveCanvas().then(res => {
					this.showimg = res;
					// console.log(res);
				}).catch(err => {
					// console.log(err);
				});
			},
			/* 电子签名 e */

			/* GoodsNav 商品导航 s */
			/**
			 * GoodsNav 左侧点击事件
			 * @param {Object} e
			 */
			onClick(e) {
				// 联系客服
				if (e.index == 0) {
					this.callPhone(this.csPhone);
				}
			},
			/**
			 * GoodsNav 右侧按钮组点击事件
			 * @param {Object} e
			 */
			buttonClick(e) {
				let self = this;
				if (this.user_name == '') {
					uni.showToast({
						icon: 'none',
						title: '请填写姓名',
						duration: 2000
					});
					return false;
				}
				if (this.showimg == '') {
					uni.showToast({
						icon: 'none',
						title: '请签名后在提交',
						duration: 2000
					});
					return false;
				}
				// 确认合作
				if (e.index == 0) {
					uni.showModal({
						title: `${e.content.text}`,
						content: '我已阅读并同意《广告屏合作协议》，确认合作。（如有疑问，请联系客服。）',
						cancelText: '联系客服',
						success: function(res) {
							if (res.confirm) {
								self.createOrder();
							}
							if (res.cancel) {
								self.callPhone(self.csPhone);
							}
						}
					});
				}
			},
			/* GoodsNav 商品导航 e */

			/**
			 * 拨打电话
			 * @param {Object} phone
			 */
			callPhone(phone) {
				uni.makePhoneCall({
					phoneNumber: phone
				});
			},

			/**
			 * 创建广告屏合作商订单
			 */
			createOrder() {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'api/partner_order',
					data: {
						user_id: this.userInfo.user_id,
						role_id: uni.getStorageSync('role_id'),
						phone: this.userInfo.phone,
						device_id: this.device.device_id,
						device_price: this.device.sale_price,
						user_name: this.user_name, // 乙方用户姓名
						party_a_investment: this.company_out, // 甲方出资金额
						party_b_investment: this.person_out, // 乙方出资金额
						party_a_share: this.share, // 广告屏甲方占股
						party_b_share: this.share, // 广告屏乙方占股
						party_b_signature: this.showimg // 合同乙方签名
					},
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'POST',
					success: function(res) {
						if (res.data.status == 1) {
							uni.showToast({
								icon: 'none',
								title: res.data.message
							});
						} else {
							uni.showModal({
								title: '提交失败',
								content: res.data.message,
								confirmText: '联系客服',
								success: function(res) {
									if (res.confirm) {
										self.callPhone(self.csPhone);
									}
								}
							});
						}
					}
				})
			}
		}
	}
</script>

<style scoped="true">
	.title {
		line-height: 50px;
	}

	.content {
		display: flex;
		;
		flex-flow: row wrap;
		text-align: left;
		margin-bottom: 15px;
		line-height: 30px;
	}

	.content-text {
		display: flex;
		flex-flow: row wrap;
		text-align: left;
	}

	.content-left {
		flex-basis: 60px;
	}

	.content-left-text {
		flex-basis: 80px;
	}

	.content-right {
		flex: 1;
	}

	.sign-border {
		border-bottom: 1px solid #333333;
		text-align: center;
		font-size: 14px;
	}

	.sign-border-text {
		border-bottom: 1px solid #333333;
		text-align: center;
		font-size: 14px;
	}

	.text-space {
		text-indent: 20px;
	}

	.text-word {
		display: inline-block;
		width: 80px;
		border-bottom: 1px solid #000;
		text-align: center;
		font-size: 14px;
	}

	.content-con {
		margin-bottom: 60px;
	}

	.signname {
		margin-top: 10px;
	}

	.sign-con {
		display: flex;
		flex-flow: row wrap;
		text-align: center;
	}

	.sign-con-item {
		flex: 1;
	}


	.wrapper {
		margin: 30upx 0;
		overflow: hidden;
		display: flex;
		align-content: center;
		flex-direction: column;
		justify-content: center;
		font-size: 28upx;
	}

	.handWriting {
		background: #fff;
		width: 100%;
		height: 350upx;
	}

	.handRight {
		align-items: center;
	}

	.handCenter {
		border: 4upx dashed #e9e9e9;
		flex: 5;
		overflow: hidden;
		box-sizing: border-box;
		width: 90%;
		margin: 0 auto;
	}

	.handTitle {
		flex: 1;
		color: #666;
		justify-content: center;
		font-size: 30upx;
	}

	.handBtn {
		flex-direction: column;
		padding: 40upx 20upx;
	}

	.buttons {
		width: 100%;
		margin-top: 20upx;
		justify-content: space-between;
	}

	.buttons>button {
		font-size: 30upx;
		height: 80upx;
		width: 120upx;
	}

	.delBtn {
		color: #666;
	}

	.color {
		align-items: center;
	}

	.color>text {
		margin-right: 20upx;
	}

	.subBtn {
		background: #008ef6;
		color: #fff;
		text-align: center;
		justify-content: center;
	}

	.black-select {
		width: 60upx;
		height: 60upx;
	}

	.black-select.color_select {
		width: 90upx;
		height: 90upx;
	}

	.red-select {
		width: 60upx;
		height: 60upx;
	}

	.red-select.color_select {
		width: 90upx;
		height: 90upx;
	}

	.slide-wrapper {
		align-items: center;
		margin-bottom: 20upx;
	}

	.slider {
		width: 400upx;
		padding-left: 20upx;
	}

	.drop {
		width: 50upx;
		height: 50upx;
		border-radius: 50%;
		background: #FFF;
		position: absolute;
		left: 0upx;
		top: -10upx;
		box-shadow: 0px 1px 5px #888888;
	}

	.slide {
		width: 250upx;
		height: 30upx;
	}

	.showimg {
		border: 0upx solid #e9e9e9;
		overflow: hidden;
		width: 90%;
		margin: 0 auto;
		background: none;
		height: 350upx;
		margin-top: 40upx;
		align-items: center;
		justify-content: center;
	}

	.showimg>image {
		width: 100%;
		height: 100%;
	}

	.showimg>text {
		font-size: 40upx;
		color: #888;
	}
</style>
