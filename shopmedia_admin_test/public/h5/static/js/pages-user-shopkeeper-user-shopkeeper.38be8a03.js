(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-user-shopkeeper-user-shopkeeper"],{2436:function(t,e,i){"use strict";var r={"uni-card":i("25a2").default,"uni-list":i("24cc").default,"uni-list-item":i("3e44").default,"uni-grid":i("5a60").default,"uni-grid-item":i("d218").default},n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",[i("v-uni-view",[i("v-uni-map",{staticClass:"map",attrs:{longitude:t.longitude,latitude:t.latitude,scale:9,markers:t.markers,"enable-satellite":!1}})],1),i("v-uni-view",[t.shopCount?t._e():i("uni-card",{attrs:{"is-shadow":!0}},[i("v-uni-view",{staticClass:"uni-center"},[t._v("有店铺，想安装智能广告屏？")]),i("v-uni-view",[i("v-uni-button",{staticClass:"main-color ask-mt"},[t._v("有什么好处")])],1),i("uni-list",[i("uni-list-item",{attrs:{title:"获得30%的广告收入","show-arrow":!1}}),i("uni-list-item",{attrs:{title:"超低优惠打广告","show-arrow":!1}}),i("uni-list-item",{attrs:{title:"利用店通智能数据分析提升销量","show-arrow":!1}}),i("uni-list-item",{attrs:{title:"遇到支持你的生意伙伴","show-arrow":!1}})],1),i("v-uni-view",[i("v-uni-button",{staticClass:"main-color ask-mt",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.shopask()}}},[t._v("联系安装")])],1)],1),t.shopCount?i("uni-card",{attrs:{title:"我的资金",isShadow:!0}},[i("uni-grid",{staticClass:"uni-center",attrs:{column:3,showBorder:!1,square:!1}},[i("uni-grid-item",[i("v-uni-text",{staticClass:"uni-text-small"},[t._v("总收入")]),i("v-uni-text",{staticClass:"uni-bold"},[t._v("￥"+t._s(t.shopkeeper.income))])],1),i("uni-grid-item",[i("v-uni-text",{staticClass:"uni-text-small"},[t._v("已提现")]),i("v-uni-text",{staticClass:"uni-bold"},[t._v("￥"+t._s(t.shopkeeper.cash))])],1),i("uni-grid-item",[i("v-uni-text",{staticClass:"uni-text-small"},[t._v("余额")]),i("v-uni-text",{staticClass:"uni-bold"},[t._v("￥"+t._s(t.shopkeeper.money))])],1)],1)],1):t._e(),i("uni-card",{attrs:{title:"奖品发放",isShadow:!0}},[i("uni-grid",{staticClass:"uni-center",attrs:{column:2,showBorder:!1,square:!1}},[i("uni-grid-item",[i("v-uni-navigator",{attrs:{url:"/pages/act-raffle/raffle-prize?prize_status=0"}},[i("v-uni-view",{staticClass:"uni-text-small"},[t._v("待发放")]),i("v-uni-view",{staticClass:"uni-bold color-red"},[t._v(t._s(t.rafflePrizeCount.rafflePrizeCount0))])],1)],1),i("uni-grid-item",[i("v-uni-navigator",{attrs:{url:"/pages/act-raffle/raffle-prize?prize_status=1"}},[i("v-uni-view",{staticClass:"uni-text-small"},[t._v("已发放")]),i("v-uni-view",{staticClass:"uni-bold"},[t._v(t._s(t.rafflePrizeCount.rafflePrizeCount1))])],1)],1)],1)],1),t._l(t.shopList,(function(e,r){return t.shopCount?i("uni-card",{key:r,attrs:{note:"Tips","is-shadow":!0}},[i("uni-list",[i("uni-list-item",{attrs:{title:e.shop.shop_name,note:0!=Number(e.device.length)?"合计 "+Number(e.device.length)+" 台":"",rightText:"导航"},on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.openLocation(e)}}})],1),0!=e.device.length?i("uni-grid",{directives:[{name:"show",rawName:"v-show",value:!1,expression:"false"}],staticClass:"uni-center",attrs:{column:3,showBorder:!0,square:!1}},[i("uni-grid-item",[i("v-uni-text",{},[t._v("屏编号")])],1),i("uni-grid-item",[i("v-uni-text",{},[t._v("总收入(￥)")])],1),i("uni-grid-item",[i("v-uni-text",{},[t._v("今日收入(￥)")])],1)],1):t._e(),t._l(e.device,(function(r,n){return 0!=e.device.length?i("uni-grid",{directives:[{name:"show",rawName:"v-show",value:!1,expression:"false"}],key:n,staticClass:"uni-center",attrs:{column:3,showBorder:!1,square:!1}},[i("uni-grid-item",[i("v-uni-text",{},[t._v(t._s(r.device_id))])],1),i("uni-grid-item",[i("v-uni-text",{staticClass:"color-red"},[t._v(t._s(r.total_income))])],1),i("uni-grid-item",[i("v-uni-text",{staticClass:"color-red"},[t._v(t._s(r.today_income))])],1)],1):t._e()})),i("template",{slot:"footer"},[i("v-uni-view",{staticClass:"footer-box"},[e.shop.party_b_signature?t._e():i("v-uni-view",{on:{click:function(i){i.stopPropagation(),arguments[0]=i=t.$handleEvent(i),t.footerClick(e)}}},[i("v-uni-button",{staticClass:"mini-btn",attrs:{type:e.shop.party_b_signature?"default":"warn",size:"mini",plain:!1}},[t._v(t._s(e.shop.party_b_signature?"查看协议":"签署协议"))])],1)],1)],1)],2):t._e()}))],2)],1)},o=[];i.d(e,"b",(function(){return n})),i.d(e,"c",(function(){return o})),i.d(e,"a",(function(){return r}))},"2c73":function(t,e,i){"use strict";var r=i("7f70"),n=i.n(r);n.a},3429:function(t,e,i){"use strict";i("4160"),i("a434"),i("a9e3"),i("159b"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var r={name:"UniGridItem",inject:["grid"],props:{index:{type:Number,default:0}},data:function(){return{column:0,showBorder:!0,square:!0,highlight:!0,left:0,top:0,openNum:2,width:0,borderColor:"#e5e5e5"}},created:function(){this.column=this.grid.column,this.showBorder=this.grid.showBorder,this.square=this.grid.square,this.highlight=this.grid.highlight,this.top=0===this.hor?this.grid.hor:this.hor,this.left=0===this.ver?this.grid.ver:this.ver,this.borderColor=this.grid.borderColor,this.grid.children.push(this),this.width=this.grid.width},beforeDestroy:function(){var t=this;this.grid.children.forEach((function(e,i){e===t&&t.grid.children.splice(i,1)}))},methods:{_onClick:function(){this.grid.change({detail:{index:this.index}})}}};e.default=r},"514a":function(t,e,i){"use strict";var r=i("d1ee"),n=i.n(r);n.a},"5a60":function(t,e,i){"use strict";i.r(e);var r=i("93b1"),n=i("da73");for(var o in n)"default"!==o&&function(t){i.d(e,t,(function(){return n[t]}))}(o);i("2c73");var s,a=i("f0c5"),u=Object(a["a"])(n["default"],r["b"],r["c"],!1,null,"6771f5db",null,!1,r["a"],s);e["default"]=u.exports},"7f70":function(t,e,i){var r=i("a663");"string"===typeof r&&(r=[[t.i,r,""]]),r.locals&&(t.exports=r.locals);var n=i("4f06").default;n("c25f55cc",r,!0,{sourceMap:!1,shadowMode:!1})},"8f6c":function(t,e,i){var r=i("e4a3");"string"===typeof r&&(r=[[t.i,r,""]]),r.locals&&(t.exports=r.locals);var n=i("4f06").default;n("f0e99daa",r,!0,{sourceMap:!1,shadowMode:!1})},"93b1":function(t,e,i){"use strict";var r,n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{staticClass:"uni-grid-wrap"},[i("v-uni-view",{ref:"uni-grid",staticClass:"uni-grid",class:{"uni-grid--border":t.showBorder},style:{"border-left-style":"solid","border-left-color":t.borderColor,"border-left-width":t.showBorder?"1px":0},attrs:{id:t.elId}},[t._t("default")],2)],1)},o=[];i.d(e,"b",(function(){return n})),i.d(e,"c",(function(){return o})),i.d(e,"a",(function(){return r}))},a5d5:function(t,e,i){"use strict";i.r(e);var r=i("3429"),n=i.n(r);for(var o in r)"default"!==o&&function(t){i.d(e,t,(function(){return r[t]}))}(o);e["default"]=n.a},a663:function(t,e,i){var r=i("24fb");e=r(!1),e.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\r\n/* 颜色变量 */\r\n/* 行为相关颜色 */\r\n/* 文字基本颜色 */\r\n/* 背景颜色 */\r\n/* 边框颜色 */\r\n/* 尺寸变量 */\r\n/* 文字尺寸 */\r\n/* 图片尺寸 */\r\n/* Border Radius */\r\n/* 水平间距 */\r\n/* 垂直间距 */\r\n/* 透明度 */\r\n/* 文章场景相关 */.uni-grid-wrap[data-v-6771f5db]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-flex:1;-webkit-flex:1;flex:1;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;flex-direction:column;width:100%}.uni-grid[data-v-6771f5db]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-direction:row;flex-direction:row;-webkit-flex-wrap:wrap;flex-wrap:wrap}.uni-grid--border[data-v-6771f5db]{border-left-color:#e5e5e5;border-left-style:solid;border-left-width:1px}',""]),t.exports=e},ac26:function(t,e,i){"use strict";var r=i("8f6c"),n=i.n(r);n.a},b02f:function(t,e,i){var r=i("24fb");e=r(!1),e.push([t.i,".map[data-v-72654d1f]{width:100%;height:%?400?%}\n\n/* uni-card s */.footer-box[data-v-72654d1f]{\ndisplay:-webkit-box;display:-webkit-flex;display:flex;\n-webkit-box-pack:end;-webkit-justify-content:flex-end;justify-content:flex-end;-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-direction:row;flex-direction:row}\n\n/* uni-card e */.ask-mt[data-v-72654d1f]{margin-top:10px}",""]),t.exports=e},ccb6:function(t,e,i){"use strict";i.r(e);var r=i("e455"),n=i.n(r);for(var o in r)"default"!==o&&function(t){i.d(e,t,(function(){return r[t]}))}(o);e["default"]=n.a},d1ee:function(t,e,i){var r=i("b02f");"string"===typeof r&&(r=[[t.i,r,""]]),r.locals&&(t.exports=r.locals);var n=i("4f06").default;n("0f67fed2",r,!0,{sourceMap:!1,shadowMode:!1})},d218:function(t,e,i){"use strict";i.r(e);var r=i("f0ba"),n=i("a5d5");for(var o in n)"default"!==o&&function(t){i.d(e,t,(function(){return n[t]}))}(o);i("ac26");var s,a=i("f0c5"),u=Object(a["a"])(n["default"],r["b"],r["c"],!1,null,"083810c0",null,!1,r["a"],s);e["default"]=u.exports},d797:function(t,e,i){"use strict";i("4160"),i("a9e3"),i("d3b7"),i("e25e"),i("ac1f"),i("25f0"),i("159b"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var r={name:"UniGrid",props:{column:{type:Number,default:3},showBorder:{type:Boolean,default:!0},borderColor:{type:String,default:"#e5e5e5"},square:{type:Boolean,default:!0},highlight:{type:Boolean,default:!0}},provide:function(){return{grid:this}},data:function(){var t="Uni_".concat(Math.ceil(1e6*Math.random()).toString(36));return{elId:t,width:0}},created:function(){this.children=[]},mounted:function(){this.init()},methods:{init:function(){var t=this;setTimeout((function(){t._getSize((function(e){t.children.forEach((function(t,i){t.width=e}))}))}),50)},change:function(t){this.$emit("change",t)},_getSize:function(t){var e=this;uni.createSelectorQuery().in(this).select("#".concat(this.elId)).boundingClientRect().exec((function(i){e.width=parseInt((i[0].width-1)/e.column)+"px",t(e.width)}))}}};e.default=r},da73:function(t,e,i){"use strict";i.r(e);var r=i("d797"),n=i.n(r);for(var o in r)"default"!==o&&function(t){i.d(e,t,(function(){return r[t]}))}(o);e["default"]=n.a},e455:function(t,e,i){"use strict";var r=i("ee27");i("4160"),i("a9e3"),i("acd8"),i("159b"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var n=r(i("f3f3")),o=i("2f62"),s={data:function(){return{userId:"",roleId:"",shopkeeper:[],latitude:30.65742,longitude:104.06584,markers:[],shopCount:0,shopList:[],rafflePrizeCount:[]}},computed:(0,n.default)({},(0,o.mapState)(["hasLogin","forcedLogin","userInfo","commonheader"])),onLoad:function(t){this.userId=t.user_id,this.roleId=t.role_id,this.getShopkeeper()},onNavigationBarButtonTap:function(t){this.$common.actionSheetTap()},onShow:function(){this.getShopList(),this.getRafflePrizeCount()},methods:{shopask:function(){uni.makePhoneCall({phoneNumber:"13693444308"})},getShopkeeper:function(){var t=this;uni.request({url:this.$serverUrl+"api/get_shopkeeper",data:{user_id:this.userInfo.user_id},method:"GET",header:{commonheader:this.commonheader,"access-user-token":this.userInfo.token},success:function(e){1==e.data.status&&(t.shopkeeper=e.data.data)}})},getShopList:function(){var t=this;uni.request({url:this.$serverUrl+"api/shopkeeper_shop_list",data:{user_id:this.userId},header:{commonheader:this.$store.state.commonheader,"access-user-token":this.userInfo.token},method:"GET",success:function(e){t.shopList=e.data,t.shopCount=t.shopList.length},fail:function(t){uni.showToast({icon:"none",title:"请求异常"})}})},openLocation:function(t){uni.openLocation({longitude:Number(t.shop.longitude),latitude:Number(t.shop.latitude),name:t.shop.shop_name,address:t.shop.address})},footerClick:function(t){var e=t.device.length,i=0;t.device.forEach((function(t,e){i+=parseFloat(t.sale_price)})),uni.navigateTo({url:"/pages/shop/shop-agreement?shop_name="+t.shop.shop_name+"&device_count="+e+"&address="+t.shop.address+"&total_price="+i+"&shop_id="+t.shop.shop_id})},getRafflePrizeCount:function(){var t=this;uni.request({url:this.$serverUrl+"api/raffle_prize_count",data:{user_id:this.userInfo.user_id},method:"GET",header:{commonheader:this.commonheader,"access-user-token":this.userInfo.token},success:function(e){1==e.data.status&&(t.rafflePrizeCount=e.data.data)}})}}};e.default=s},e4a3:function(t,e,i){var r=i("24fb");e=r(!1),e.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\r\n/* 颜色变量 */\r\n/* 行为相关颜色 */\r\n/* 文字基本颜色 */\r\n/* 背景颜色 */\r\n/* 边框颜色 */\r\n/* 尺寸变量 */\r\n/* 文字尺寸 */\r\n/* 图片尺寸 */\r\n/* Border Radius */\r\n/* 水平间距 */\r\n/* 垂直间距 */\r\n/* 透明度 */\r\n/* 文章场景相关 */.uni-grid-item[data-v-083810c0]{height:100%;display:-webkit-box;display:-webkit-flex;display:flex}.uni-grid-item__box[data-v-083810c0]{display:-webkit-box;display:-webkit-flex;display:flex;width:100%;position:relative;-webkit-box-flex:1;-webkit-flex:1;flex:1;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;flex-direction:column}.uni-grid-item--border[data-v-083810c0]{position:relative;border-bottom-color:#e5e5e5;border-bottom-style:solid;border-bottom-width:1px;border-right-color:#e5e5e5;border-right-style:solid;border-right-width:1px}.uni-grid-item--border-top[data-v-083810c0]{border-top-color:#e5e5e5;border-top-style:solid;border-top-width:1px;height:100%;-webkit-box-sizing:border-box;box-sizing:border-box}.uni-highlight[data-v-083810c0]:active{background-color:#f1f1f1}',""]),t.exports=e},ee3e:function(t,e,i){"use strict";i.r(e);var r=i("2436"),n=i("ccb6");for(var o in n)"default"!==o&&function(t){i.d(e,t,(function(){return n[t]}))}(o);i("514a");var s,a=i("f0c5"),u=Object(a["a"])(n["default"],r["b"],r["c"],!1,null,"72654d1f",null,!1,r["a"],s);e["default"]=u.exports},f0ba:function(t,e,i){"use strict";var r,n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return t.width?i("v-uni-view",{staticClass:"uni-grid-item",style:"width:"+t.width+";"+(t.square?"height:"+t.width:"")},[i("v-uni-view",{staticClass:"uni-grid-item__box",class:{"uni-grid-item--border":t.showBorder,"uni-grid-item--border-top":t.showBorder&&t.index<t.column,"uni-highlight":t.highlight},style:{"border-right-color":t.borderColor,"border-bottom-color":t.borderColor,"border-top-color":t.borderColor},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t._onClick.apply(void 0,arguments)}}},[t._t("default")],2)],1):t._e()},o=[];i.d(e,"b",(function(){return n})),i.d(e,"c",(function(){return o})),i.d(e,"a",(function(){return r}))}}]);