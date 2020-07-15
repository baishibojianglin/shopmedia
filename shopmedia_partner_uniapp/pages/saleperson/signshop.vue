<template>
	<view class="wrapper uni-padding-wrap">
		<!-- 协议书 s -->
		<view class="blod uni-center title">
			店铺安装广告屏合作协议书
		</view>

		<view class="content-con">
			<view class="content">
				<text class="content-left blod">甲方：</text>
				<text class="content-right sign-border">四川狄霖店通传媒有限公司</text>
			</view>
			<view class="content">
				<text class="content-left blod">乙方：</text>
				<text class="content-right sign-border-text">{{shop.shop_name}}</text>
			</view>

			<view class="text-space">甲、乙双方依据中华人民共和国相关法律法规，经平等、友好协商达成如下合作协议：</view>
			<view class="blod">一、合作内容</view>
			<view class="content-text">
				<text class="content-left-text">设备编号：</text>
				<input class="content-right sign-border"  placeholder="" :disabled="inputDisabled" />
			</view>
			<view class="content-text">
				<text class="content-left-text">安装数量：</text>
				<input class="content-right sign-border"  placeholder="" :disabled="inputDisabled" />
			</view>
			<view class="content-text">
				<text class="content-left-text">安装位置：</text>
				<text class="content-right sign-border-text"></text>
			</view>
			<view class="content-text">
				<text class="content-left-text">设备总价值：</text>
				<text class="content-right sign-border-text"></text>
			</view>
			<view class="content-text">甲方将店通广告屏安装到乙方店内，并将该广告机 <text class="fix-text">{{party_b_share}}</text> %广告利润收入支付乙方。</view><!-- ，乙方在店通传媒每年享有一次<text class="fix-text">2</text>折投放广告的优惠权利 -->

			<view class="blod">二、权利与义务</view>
			<view>1、甲方承担设备的运营、管理、维修责任;</view>
			<view>2、乙方需配合甲方完成日常运营维护的必要工作；</view>
			<view>3、甲方负责广告的业务开拓、制作、剪辑、投放工作；</view>
			<view>4、甲方负责终端店智能广告系统的研发、系统升级、系统维护和系统安全防护等工作；</view>
			<view>5、当乙方因铺面装修、转让等原因申请拆除广告屏时，需提前通知甲方，甲方在收到通知之日起10天内完成拆除。</view>
			<view>6、智能屏安装后，无协议约定的特殊情况外，须保证至少6个月内不拆除；</view>
			<view>7、乙方的广告收入可以选择在甲方开发的APP中提取或由甲方转入乙方指定的账户，结算期为每个月最后一个工作日支付，如甲方逾期支付，乙方有权要求甲方按银行同期利率支付滞纳金。</view>
			<view class="blod">三、不可抗力因素</view>
			<view>发生地震、滑坡、泥石流、重大公共卫生疫情等不可抗力因素导致甲、乙双方均无法正常开展经营时，双方均无须承担相应责任。</view>
			<view class="blod">四、其他</view>
			<view>1、当甲、乙双方发生纠纷且无法协商解决时，提交甲方所在地人民法院依据相关法律法规办理。</view>
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
		<view v-if="!shop.party_b_signature">
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
				 @touchend="uploadScaleEnd"  canvas-id="handWriting">
				</canvas>
			</view>

			<view class="buttons sign-con" style="margin-bottom: 100px;">
				<button @click="retDraw" class="sign-con-item" style="margin: 0 15px;">重写</button>
				<button @click="subCanvas" class="sign-con-item" style="margin: 0 15px;">确认</button>
			</view>
		</view>
		<!--电子签名 e-->

	</view>
</template>

<script>
	import {mapState} from 'vuex';
	import Handwriting from "../../static/js/signature.js"

	export default {
		data() {
			return {
				csPhone: '', // 客服电话
				// 店铺（协议书）信息
				shop: {
					device_ids: '', // 广告屏编号集合
					device_quantity: '', // 安装广告屏数量
					device_price: '', // 广告屏总价格
					party_b_share: '', // 广告收益乙方（店铺）提成比例
				},
				party_b_share: 30,
				
				// 输入框是否禁用
				inputDisabled: false,

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
				share_popup: false
			}
		},
		computed: {
			...mapState(['hasLogin', 'forcedLogin', 'userInfo', 'commonheader'])
		},
		onNavigationBarButtonTap(e) {
			this.$common.actionSheetTap();
		},
		onLoad(event) {
			
			// 电子签名
			this.$nextTick(function() {
				this.handwriting = new Handwriting({
					lineColor: this.lineColor,
					slideValue: this.slideValue, // 0, 25, 50, 75, 100 
					canvasName: 'handWriting',
				})
			})
			
	

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
	.fix-text{
		display: inline-block;
		border-bottom:1px solid #000;
        font-weight: bold;
		padding: 0 10px;
	}
</style>
