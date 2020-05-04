<template>
	<view>
		<!-- <view class="banner" @click="goDetail(banner)">
			<image class="banner-img" :src="banner.cover"></image>
			<view class="banner-title">{{ banner.title }}</view>
		</view> -->
		<view class="uni-list">
			<view class="uni-list-cell" hover-class="uni-list-cell-hover" v-for="(value, key) in listData" :key="key" @click="goDetail(value)">
				<view class="uni-media-list">
					<!-- <image class="uni-media-list-logo" :src="value.thumb"></image> -->
					<view class="uni-media-list-body">
						<view class="uni-media-list-text-top">{{ value.title }}</view>
						<view class="uni-media-list-text-bottom">
							<text>{{ value.author }}</text>
							<text>{{ value.publish_time }}</text>
						</view>
					</view>
				</view>
			</view>
		</view>
		<uni-load-more :status="status" :icon-size="16" :content-text="contentText" />
	</view>
</template>

<script>
	var dateUtils = require('@/common/util.js').dateUtils;

	export default {
		data() {
			return {
				banner: {},

				/* 新闻列表 s */
				listData: [],
				last_id: '',
				reload: false,
				status: 'more',
				contentText: {
					contentdown: '上拉加载更多',
					contentrefresh: '加载中',
					contentnomore: '没有更多'
				}
				/* 新闻列表 e */
			};
		},
		onLoad() {
			this.getBanner();
			this.getList();
		},
		onPullDownRefresh() {
			this.reload = true;
			this.last_id = '';
			this.getBanner();
			this.getList();
		},
		onReachBottom() {
			this.status = 'more';
			this.getList();
		},
		onNavigationBarButtonTap(e) {
			this.$common.actionSheetTap();
		},
		methods: {
			getBanner() {
				let data = {
					column: 'id,post_id,title,author_name,cover,published_at' //需要的字段名
				};
				uni.request({
					url: 'https://unidemo.dcloud.net.cn/api/banner/36kr',
					data: data,
					success: data => {
						uni.stopPullDownRefresh();
						if (data.statusCode == 200) {
							this.banner = data.data;
						}
					},
					fail: (data, code) => {
						console.log('fail' + JSON.stringify(data));
					}
				});
			},

			/**
			 * 新闻列表
			 */
			getList() {
				var data = {};
				if (this.last_id) {
					//说明已有数据，目前处于上拉加载
					this.status = 'loading';
					data.minId = this.last_id;
					// data.time = new Date().getTime() + '';
					data.size = 5;
				}
				uni.request({
					url: this.$serverUrl + 'api/news',
					header: {
						'commonheader': this.$store.state.commonheader
					},
					data: data,
					success: res => {
						if (res.statusCode == 200) {
							// let list = res.data.data.data;
							let list = this.setTime(res.data.data.data);
							this.listData = this.reload ? list : this.listData.concat(list);
							this.last_id = list[list.length - 1].news_id;
							this.reload = false;
						} else if (res.statusCode == 404) {
							// 判断数据已经全部加载
							if (this.last_id == res.data.data.maxId) {
								this.status = '';
								return;
							}
						}
					},
					fail: (error, code) => {
						// console.log('fail' + JSON.stringify(error));
					}
				});
			},

			/**
			 * 跳转新闻详情页
			 * @param {Object} e
			 */
			goDetail: function(e) {
				// 				if (!/前|刚刚/.test(e.published_at)) {
				// 					e.published_at = dateUtils.format(e.published_at);
				// 				}
				let detail = {
					author: e.author,
					thumb: e.thumb,
					news_id: e.news_id,
					// post_id: e.post_id,
					publish_time: e.publish_time,
					title: e.title
				};
				uni.navigateTo({
					url: 'news-detail?detailDate=' + encodeURIComponent(JSON.stringify(detail))
				});
			},
			setTime: function(items) {
				var newItems = [];
				items.forEach(e => {
					newItems.push({
						author: e.author,
						thumb: e.thumb,
						news_id: e.news_id,
						// post_id: e.post_id,
						publish_time: dateUtils.format(e.publish_time),
						title: e.title
					});
				});
				return newItems;
			}
		}
	};
</script>

<style>
	.banner {
		height: 360rpx;
		overflow: hidden;
		position: relative;
		background-color: #ccc;
	}

	.banner-img {
		width: 100%;
	}

	.banner-title {
		max-height: 84rpx;
		overflow: hidden;
		position: absolute;
		left: 30rpx;
		bottom: 30rpx;
		width: 90%;
		font-size: 32rpx;
		font-weight: 400;
		line-height: 42rpx;
		color: white;
		z-index: 11;
	}

	.uni-media-list-logo {
		width: 180rpx;
		height: 140rpx;
	}

	.uni-media-list-body {
		height: auto;
		justify-content: space-around;
	}

	.uni-media-list-text-top {
		height: 74rpx;
		font-size: 28rpx;
		overflow: hidden;
	}

	.uni-media-list-text-bottom {
		display: flex;
		flex-direction: row;
		justify-content: space-between;
	}
</style>
