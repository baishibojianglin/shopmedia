import Vue from 'vue'
import App from './App'

Vue.config.productionTip = false
Vue.prototype.$url='http://www.shopmedia.com/index.php/'  //后台域名

App.mpType = 'app'

const app = new Vue({
    ...App
})
app.$mount()
