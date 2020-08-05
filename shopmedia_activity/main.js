import Vue from 'vue'
import App from './App'

Vue.config.productionTip = false
//Vue.prototype.$serverUrl = 'http://media.dilinsat.com/index.php/' // 测试接口域名
Vue.prototype.$serverUrl = 'https://media.sustock.net/index.php/' // 正式接口域名
App.mpType = 'app'

const app = new Vue({
    ...App
})
app.$mount()
