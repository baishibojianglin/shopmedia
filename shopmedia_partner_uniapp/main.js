import Vue from 'vue'
import App from './App'

import store from './store'


Vue.config.productionTip = false

Vue.prototype.$store = store  //注入store
Vue.prototype.$serverUrl = 'http://www.shopmedia.com/index.php/' //后台接口域名
// Vue.prototype.$serverUrl = 'http://pet.dilinsat.com/index.php/' 





App.mpType = 'app'

const app = new Vue({
	store,
	...App,

})
app.$mount()
