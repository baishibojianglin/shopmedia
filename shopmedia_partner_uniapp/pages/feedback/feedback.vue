<template>
	<view>
		<view class='feedback-title'>
			<text>投诉和建议</text>
		</view>
		<uni-card>
			<view class="feedback-body">
				<textarea class="feedback-textare" v-model="sendDate.content" placeholder="请详细描述你的投诉和建议，不能超过200个字符..." maxlength="200" :auto-focus="true"></textarea>
			</view>
		</uni-card>
		<button type="default" class="feedback-submit" @tap="send">提 交</button>
		<view class='feedback-title'>
			<text>我们将安排专业人员及时对您反馈的问题进行处理。</text>
		</view>
	</view>
</template>
<script>
	import {mapState} from 'vuex';
	
	export default {
		data() {
			return {
				sendDate: {
					content: "" // 反馈内容
				}
			}
		},
		computed: {
			...mapState(['hasLogin', 'forcedLogin', 'userInfo', 'commonheader'])
		},
		methods: {
			/**
			 * 发送反馈
			 */
			send() {
				if (this.sendDate.content.length === 0) {
				    uni.showModal({
				        content: '请输入投诉和建议',
				        showCancel: false
				    })
				    return;
				}
				
				uni.request({
					url: this.$serverUrl + 'api/feedback',
					data: {
						user_id: this.userInfo.user_id,
						content: this.sendDate.content
					},
					header: {
						'commonheader': this.commonheader,
						'access-user-token': this.userInfo.token
					},
					method: 'POST',
					success: function(res) {
						if (res.statusCode == 201 && res.data.status == 1) {
							uni.showToast({
								title: res.data.message,
								success() {
									uni.switchTab({
										url: '/pages/main/main'
									});
								}
							});
						} else {
							uni.showToast({
								icon: 'none',
								title: res.data.message
							});
						}
					},
					fail:function(error){
						uni.showToast({
							icon: 'none',
							title: '请求异常'
						});
					}
				})
			}
		}
	}
</script>

<style>
	page {
		background-color: #EFEFF4;
	}
</style>
