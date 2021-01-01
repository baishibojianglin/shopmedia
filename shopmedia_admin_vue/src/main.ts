import Vue from 'vue'
import App from './App.vue'
import './registerServiceWorker'
import router from './router'
import store from './store'
import axios from 'axios'
import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'


// 公用配置
Vue.config.productionTip = false
Vue.prototype.$axios = axios  //ajax插件
// Vue.prototype.$serverUrl = 'http://media.sustock.local/index.php/' // 测试接口域名（本地）
// Vue.prototype.$url='http://media.dilinsat.com/index.php/'  //测试接口域名
Vue.prototype.$url='https://media.sustock.net/index.php/'  //正式接口域名
axios.defaults.withCredentials=true  //解决跨域后保持相同session(允许ajax携带cook)
Vue.use(ElementUI)

// 引入基本样式
import './assets/css/basic.css'

// 验证签名值
Vue.prototype.$sign = 'jl_goodshop'


new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
