import Vue from 'vue'
import App from './App'

import store from './store'
import common from '@/common/common.js'

Vue.config.productionTip = false

Vue.prototype.$store = store  //注入store
Vue.prototype.$common = common
// Vue.prototype.$serverUrl = 'http://www.shopmedia.com/index.php/' // 测试环境（本地）
// Vue.prototype.$serverUrl = 'http://dt.dilinsat.com/index.php/' // 临时环境(线上)
Vue.prototype.$serverUrl = 'http://media.dilinsat.com/index.php/' // 测试环境（线上）
Vue.prototype.$imgServerUrl = '' // 图片接口地址





App.mpType = 'app'

const app = new Vue({
	store,
	...App
})
app.$mount()
