(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-device-device-detail2"],{"113f":function(t,e,i){"use strict";i.r(e);var a=i("e36d"),n=i("ea9b");for(var s in n)"default"!==s&&function(t){i.d(e,t,(function(){return n[t]}))}(s);i("d628");var o,r=i("f0c5"),l=Object(r["a"])(n["default"],a["b"],a["c"],!1,null,"1fa90946",null,!1,a["a"],o);e["default"]=l.exports},"25a2":function(t,e,i){"use strict";i.r(e);var a=i("a181"),n=i("dcde");for(var s in n)"default"!==s&&function(t){i.d(e,t,(function(){return n[t]}))}(s);i("9f74");var o,r=i("f0c5"),l=Object(r["a"])(n["default"],a["b"],a["c"],!1,null,"46b53ee7",null,!1,a["a"],o);e["default"]=l.exports},4777:function(t,e,i){"use strict";var a=i("ee27");i("a9e3"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var n=a(i("f3f3")),s=i("2f62"),o={data:function(){return{device_id:0,datalist:{},cate_name:"",imglist:[],device_size:{},device_price:{}}},computed:(0,n.default)({},(0,s.mapState)(["hasLogin","forcedLogin","userInfo","commonheader"])),onLoad:function(t){this.device_id=t.device_id,this.getCate(),this.deviceDetail(),this.getSize(),this.getPrice()},onNavigationBarButtonTap:function(t){this.$common.actionSheetTap()},methods:{getCate:function(){var t=this;uni.request({url:this.$serverUrl+"api/shop_cate_list",header:{commonheader:this.commonheader,"access-user-token":this.userInfo.token},method:"GET",success:function(e){t.shopcatelist=e.data.data}})},getSize:function(){var t=this;uni.request({url:this.$serverUrl+"api/device_size",header:{commonheader:this.commonheader,"access-user-token":this.userInfo.token},method:"GET",success:function(e){t.device_size=e.data}})},getPrice:function(){var t=this;uni.request({url:this.$serverUrl+"api/device_price",header:{commonheader:this.commonheader,"access-user-token":this.userInfo.token},method:"GET",success:function(e){t.device_price=e.data}})},deviceDetail:function(){var t=this;uni.request({url:this.$serverUrl+"api/device_detail",data:{device_id:t.device_id},header:{commonheader:this.commonheader,"access-user-token":this.userInfo.token},method:"GET",success:function(e){t.datalist=e.data.data,t.cate_name=t.shopcatelist[t.datalist.shop_cate-1].cate_name,t.imglist=JSON.parse(e.data.data.url_image)}})},openLocation:function(){uni.openLocation({latitude:Number(this.datalist.latitude),longitude:Number(this.datalist.longitude),name:this.datalist.shop_name,address:this.datalist.address})}}};e.default=o},"4e09":function(t,e,i){var a=i("24fb");e=a(!1),e.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\r\n/* 颜色变量 */\r\n/* 行为相关颜色 */\r\n/* 文字基本颜色 */\r\n/* 背景颜色 */\r\n/* 边框颜色 */\r\n/* 尺寸变量 */\r\n/* 文字尺寸 */\r\n/* 图片尺寸 */\r\n/* Border Radius */\r\n/* 水平间距 */\r\n/* 垂直间距 */\r\n/* 透明度 */\r\n/* 文章场景相关 */.uni-card[data-v-46b53ee7]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-flex:1;-webkit-flex:1;flex:1;-webkit-box-shadow:0 0 0 transparent;box-shadow:0 0 0 transparent;margin:12px;background-color:#fff;position:relative;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;flex-direction:column;border-color:#e5e5e5;border-style:solid;border-width:1px;border-radius:3px;overflow:hidden}.uni-card__thumbnailimage[data-v-46b53ee7]{position:relative;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;flex-direction:column;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;height:150px;overflow:hidden}.uni-card__thumbnailimage-box[data-v-46b53ee7]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-flex:1;-webkit-flex:1;flex:1;-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-direction:row;flex-direction:row;overflow:hidden}.uni-card__thumbnailimage-image[data-v-46b53ee7]{-webkit-box-flex:1;-webkit-flex:1;flex:1}.uni-card__thumbnailimage-title[data-v-46b53ee7]{display:-webkit-box;display:-webkit-flex;display:flex;position:absolute;bottom:0;left:0;right:0;-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-direction:row;flex-direction:row;padding:%?16?% %?24?%;background-color:rgba(0,0,0,.4)}.uni-card__thumbnailimage-title-text[data-v-46b53ee7]{-webkit-box-flex:1;-webkit-flex:1;flex:1;font-size:%?28?%;color:#fff}.uni-card__title[data-v-46b53ee7]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-direction:row;flex-direction:row;padding:10px;border-bottom-color:#f5f5f5;border-bottom-style:solid;border-bottom-width:1px}.uni-card__title-header[data-v-46b53ee7]{width:40px;height:40px;overflow:hidden;border-radius:5px}.uni-card__title-header-image[data-v-46b53ee7]{width:40px;height:40px}.uni-card__title-content[data-v-46b53ee7]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;flex-direction:column;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;padding-left:10px;height:40px;overflow:hidden}.uni-card__title-content-title[data-v-46b53ee7]{font-size:%?28?%;line-height:22px;lines:1}.uni-card__title-content-extra[data-v-46b53ee7]{font-size:%?26?%;line-height:%?35?%;color:#999}.uni-card__header[data-v-46b53ee7]{display:-webkit-box;display:-webkit-flex;display:flex;position:relative;-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-direction:row;flex-direction:row;padding:%?24?%;-webkit-box-align:center;-webkit-align-items:center;align-items:center;border-bottom-color:#e5e5e5;border-bottom-style:solid;border-bottom-width:1px}.uni-card__header-title[data-v-46b53ee7]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-direction:row;flex-direction:row;margin-right:%?16?%;-webkit-box-pack:start;-webkit-justify-content:flex-start;justify-content:flex-start;-webkit-box-align:center;-webkit-align-items:center;align-items:center}.uni-card__header-title-text[data-v-46b53ee7]{font-size:%?32?%;-webkit-box-flex:1;-webkit-flex:1;flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.uni-card__header-extra-img[data-v-46b53ee7]{height:%?40?%;width:%?40?%;margin-right:%?16?%}.uni-card__header-extra-text[data-v-46b53ee7]{-webkit-box-flex:1;-webkit-flex:1;flex:1;margin-left:%?16?%;font-size:%?28?%;text-align:right;color:#999}.uni-card__content[data-v-46b53ee7]{color:#333}.uni-card__content--pd[data-v-46b53ee7]{padding:%?24?%}.uni-card__content-extra[data-v-46b53ee7]{font-size:%?28?%;padding-bottom:10px;color:#999}.uni-card__footer[data-v-46b53ee7]{-webkit-box-pack:justify;-webkit-justify-content:space-between;justify-content:space-between;padding:10px;border-top-color:#e5e5e5;border-top-style:solid;border-top-width:1px}.uni-card__footer-text[data-v-46b53ee7]{color:#999;font-size:%?28?%}.uni-card--shadow[data-v-46b53ee7]{border-color:#e5e5e5;border-style:solid;border-width:1px;-webkit-box-shadow:0 1px 2px rgba(0,0,0,.2);box-shadow:0 1px 2px rgba(0,0,0,.2)}.uni-card--full[data-v-46b53ee7]{margin:0;border-radius:0}',""]),t.exports=e},"60aa":function(t,e,i){var a=i("4e09");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var n=i("4f06").default;n("7cc845da",a,!0,{sourceMap:!1,shadowMode:!1})},6140:function(t,e,i){var a=i("7a97");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var n=i("4f06").default;n("2f735626",a,!0,{sourceMap:!1,shadowMode:!1})},"7a97":function(t,e,i){var a=i("24fb");e=a(!1),e.push([t.i,".swiper[data-v-1fa90946]{width:100%;height:250px}.swiper-item[data-v-1fa90946]{width:100%;height:100%}.swiper-item uni-image[data-v-1fa90946]{width:100%;height:100%}.datalist[data-v-1fa90946]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-direction:row;flex-direction:row;padding:0 %?20?%;line-height:40px;border-bottom:1px solid #e5e2df}\n\n/* .datalist-title {\n\tflex-basis: 70px;\n\ttext-align: right;\n} */.datalist-content[data-v-1fa90946]{-webkit-box-flex:1;-webkit-flex:1;flex:1;text-align:right}.bottom-con[data-v-1fa90946]{margin-bottom:100px}",""]),t.exports=e},"9f74":function(t,e,i){"use strict";var a=i("60aa"),n=i.n(a);n.a},a181:function(t,e,i){"use strict";var a,n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{staticClass:"uni-card",class:{"uni-card--full":!0===t.isFull||"true"===t.isFull,"uni-card--shadow":!0===t.isShadow||"true"===t.isShadow},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.onClick.apply(void 0,arguments)}}},["style"===t.mode?i("v-uni-view",{staticClass:"uni-card__thumbnailimage"},[i("v-uni-view",{staticClass:"uni-card__thumbnailimage-box"},[i("v-uni-image",{staticClass:"uni-card__thumbnailimage-image",attrs:{src:t.thumbnail,mode:"aspectFill"}})],1),t.title?i("v-uni-view",{staticClass:"uni-card__thumbnailimage-title"},[i("v-uni-text",{staticClass:"uni-card__thumbnailimage-title-text"},[t._v(t._s(t.title))])],1):t._e()],1):t._e(),"title"===t.mode?i("v-uni-view",{staticClass:"uni-card__title"},[i("v-uni-view",{staticClass:"uni-card__title-header"},[i("v-uni-image",{staticClass:"uni-card__title-header-image",attrs:{src:t.thumbnail,mode:"scaleToFill"}})],1),i("v-uni-view",{staticClass:"uni-card__title-content"},[i("v-uni-text",{staticClass:"uni-card__title-content-title"},[t._v(t._s(t.title))]),i("v-uni-text",{staticClass:"uni-card__title-content-extra"},[t._v(t._s(t.extra))])],1)],1):t._e(),"basic"===t.mode&&t.title?i("v-uni-view",{staticClass:"uni-card__header"},[t.thumbnail?i("v-uni-view",{staticClass:"uni-card__header-extra-img-view"},[i("v-uni-image",{staticClass:"uni-card__header-extra-img",attrs:{src:t.thumbnail}})],1):t._e(),i("v-uni-text",{staticClass:"uni-card__header-title-text"},[t._v(t._s(t.title))]),t.extra?i("v-uni-text",{staticClass:"uni-card__header-extra-text"},[t._v(t._s(t.extra))]):t._e()],1):t._e(),i("v-uni-view",{staticClass:"uni-card__content uni-card__content--pd"},["style"===t.mode&&t.extra?i("v-uni-view",{},[i("v-uni-text",{staticClass:"uni-card__content-extra"},[t._v(t._s(t.extra))])],1):t._e(),t._t("default")],2),t.note?i("v-uni-view",{staticClass:"uni-card__footer"},[t._t("footer",[i("v-uni-text",{staticClass:"uni-card__footer-text"},[t._v(t._s(t.note))])])],2):t._e()],1)},s=[];i.d(e,"b",(function(){return n})),i.d(e,"c",(function(){return s})),i.d(e,"a",(function(){return a}))},d628:function(t,e,i){"use strict";var a=i("6140"),n=i.n(a);n.a},dcde:function(t,e,i){"use strict";i.r(e);var a=i("eca1"),n=i.n(a);for(var s in a)"default"!==s&&function(t){i.d(e,t,(function(){return a[t]}))}(s);e["default"]=n.a},e36d:function(t,e,i){"use strict";var a={"uni-card":i("25a2").default},n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{staticClass:"uni-page-body"},[i("v-uni-view",[i("v-uni-swiper",{staticClass:"swiper",attrs:{autoplay:!0,interval:3e3,circular:!0,"indicator-dots":!0,"indicator-active-color":"#fff"}},t._l(t.imglist,(function(t,e){return i("v-uni-swiper-item",{key:e},[i("v-uni-view",{staticClass:"swiper-item"},[i("v-uni-image",{attrs:{src:t.url}})],1)],1)})),1)],1),i("uni-card",{staticStyle:{"margin-bottom":"60px"},attrs:{"is-shadow":!0}},[i("v-uni-view",[i("v-uni-view",{staticClass:"datalist"},[i("v-uni-text",{staticClass:"datalist-title"},[t._v("屏编号：")]),i("v-uni-text",{staticClass:"datalist-content"},[t._v(t._s(t.datalist.device_id))])],1),i("v-uni-view",{staticClass:"datalist"},[i("v-uni-text",{staticClass:"datalist-title"},[t._v("屏尺寸：")]),i("v-uni-text",{staticClass:"datalist-content"},[t._v(t._s(t.datalist.size)),t._v("寸")])],1),i("v-uni-view",{staticClass:"datalist"},[i("v-uni-text",{staticClass:"datalist-title"},[t._v("安装店铺：")]),i("v-uni-text",{staticClass:"datalist-content"},[t._v(t._s(t.datalist.shop_name))])],1),i("v-uni-view",{staticClass:"datalist"},[i("v-uni-text",{staticClass:"datalist-title"},[t._v("店铺面积：")]),i("v-uni-text",{staticClass:"datalist-content"},[t._v(t._s(t.datalist.shop_area)+" ㎡")])],1),i("v-uni-view",{staticClass:"datalist"},[i("v-uni-text",{staticClass:"datalist-title"},[t._v("所属行业：")]),i("v-uni-text",{staticClass:"datalist-content"},[t._v(t._s(t.cate_name))])],1),i("v-uni-view",{staticClass:"datalist"},[i("v-uni-text",{staticClass:"datalist-title"},[t._v("位置：")]),i("v-uni-view",{staticClass:"datalist-content",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.openLocation()}}},[i("v-uni-text",{staticStyle:{position:"relative",top:"6px"}},[i("v-uni-text",{staticClass:"uni-icon uni-icon-location-filled color-blue"}),t._v(t._s(t.datalist.address))],1)],1)],1),i("v-uni-view",{staticClass:"datalist"},[i("v-uni-text",{staticClass:"datalist-title"},[t._v("周围环境：")]),i("v-uni-text",{staticClass:"datalist-content"},[t._v(t._s(t.datalist.environment))])],1),i("v-uni-view",{staticClass:"datalist"},[i("v-uni-text",{staticClass:"datalist-title"},[t._v("投放价格：")]),i("v-uni-text",{staticClass:"datalist-content color-red"},[t._v("1 元/天起")])],1)],1)],1)],1)},s=[];i.d(e,"b",(function(){return n})),i.d(e,"c",(function(){return s})),i.d(e,"a",(function(){return a}))},ea9b:function(t,e,i){"use strict";i.r(e);var a=i("4777"),n=i.n(a);for(var s in a)"default"!==s&&function(t){i.d(e,t,(function(){return a[t]}))}(s);e["default"]=n.a},eca1:function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var a={name:"UniCard",props:{title:{type:String,default:""},extra:{type:String,default:""},note:{type:String,default:""},thumbnail:{type:String,default:""},mode:{type:String,default:"basic"},isFull:{type:Boolean,default:!1},isShadow:{type:Boolean,default:!1}},methods:{onClick:function(){this.$emit("click")}}};e.default=a}}]);