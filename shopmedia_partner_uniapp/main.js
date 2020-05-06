import Vue from 'vue'
import App from './App'

import store from './store'
import common from '@/common/common.js'

Vue.config.productionTip = false

Vue.prototype.$store = store  //注入store
Vue.prototype.$common = common
Vue.prototype.$serverUrl = 'http://dt.dilinsat.com/index.php/' //后台接口域名
//Vue.prototype.$serverUrl = 'http://www.shopmedia.com/index.php/' //后台接口域名
// Vue.prototype.$serverUrl = 'http://pet.dilinsat.com/index.php/'
Vue.prototype.$imgServerUrl = '' // 图片接口地址





App.mpType = 'app'

const app = new Vue({
	store,
	...App
})
app.$mount()
