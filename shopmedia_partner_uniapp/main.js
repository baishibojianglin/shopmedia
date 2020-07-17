import Vue from 'vue'
import App from './App'

import store from './store'
import common from '@/common/common.js'

Vue.config.productionTip = false

Vue.prototype.$store = store  //注入store
Vue.prototype.$common = common
// Vue.prototype.$serverUrl = 'http://media.sustock.local/index.php/' // 测试接口域名（本地）
Vue.prototype.$serverUrl = 'http://media.dilinsat.com/index.php/' // 测试接口域名
// Vue.prototype.$serverUrl = 'https://media.sustock.net/index.php/' // 正式接口域名
Vue.prototype.$imgServerUrl = '' // 图片接口地址





App.mpType = 'app'

const app = new Vue({
	store,
	...App
})
app.$mount()
