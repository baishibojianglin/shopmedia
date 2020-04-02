<template>

	<view class="content">
		
        <u-row>
            <u-col span="24">
                   <text>商市通用户协议书</text>
			</u-col>
        </u-row>
	</view>
	
</template>

<script>
	import Vue from 'vue'
	import Row from '@/components/dl-grid/row.vue'
	import Col from '@/components/dl-grid/col.vue'
	import uniPopup from "@/components/uni-popup/uni-popup.vue"	
	Vue.component('u-row', Row); //<row>和<col>为H5原生标签, 不能直接用, 可起名<u-row>或者其他的
	Vue.component('u-col', Col);
	export default {
		components: {
             uniPopup
		},
		data() {
			return {

			}
		},
		mounted(){

		},
		methods: {
	    }
	}
</script>

<style>

</style>
