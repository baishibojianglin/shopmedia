import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
	  menuonetitle:'', //一级标题
	  menutwotitle:'' //二级标题
  },
  mutations:{
	  menutitle(state,title){
		 state.menuonetitle=title.onetitletext; 
		 state.menutwotitle=title.twotitletext;
	  }
  },
  actions: {
  },
  modules: {
  }
})
