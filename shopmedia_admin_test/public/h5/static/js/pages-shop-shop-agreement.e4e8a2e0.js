(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-shop-shop-agreement"],{"0161":function(t,e,i){"use strict";i.r(e);var n=i("919a"),a=i.n(n);for(var o in n)"default"!==o&&function(t){i.d(e,t,(function(){return n[t]}))}(o);e["default"]=a.a},"0734":function(t,e,i){"use strict";var n=i("ee27");Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var a=n(i("4509")),o={name:"UniGoodsNav",components:{uniIcons:a.default},props:{options:{type:Array,default:function(){return[{icon:"shop",text:"店铺"},{icon:"cart",text:"购物车"}]}},buttonGroup:{type:Array,default:function(){return[{text:"加入购物车",backgroundColor:"#ffa200",color:"#fff"},{text:"立即购买",backgroundColor:"#ff0000",color:"#fff"}]}},fill:{type:Boolean,default:!1}},methods:{onClick:function(t,e){this.$emit("click",{index:t,content:e})},buttonClick:function(t,e){uni.report&&uni.report(e.text,e.text),this.$emit("buttonClick",{index:t,content:e})}}};e.default=o},"10ec":function(t,e,i){"use strict";var n=i("ee27");i("cb29"),i("b680"),i("d3b7"),i("acd8"),i("ac1f"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var a=n(i("276c")),o=n(i("e954")),s=n(i("fc11")),r=function(){function t(e){(0,a.default)(this,t),(0,s.default)(this,"ctx",""),(0,s.default)(this,"canvasWidth",300),(0,s.default)(this,"canvasHeight",900),(0,s.default)(this,"linePrack",[]),(0,s.default)(this,"currentLine",[]),(0,s.default)(this,"transparent",1),(0,s.default)(this,"pressure",.5),(0,s.default)(this,"smoothness",100),(0,s.default)(this,"lineSize",1.5),(0,s.default)(this,"lineMin",.5),(0,s.default)(this,"lineMax",2),(0,s.default)(this,"currentPoint",{}),(0,s.default)(this,"firstTouch",!0),(0,s.default)(this,"radius",1),(0,s.default)(this,"cutArea",{top:0,right:0,bottom:0,left:0}),(0,s.default)(this,"lastPoint",0),(0,s.default)(this,"chirography",[]),(0,s.default)(this,"startY",0),(0,s.default)(this,"deltaY",0),(0,s.default)(this,"startValue",0),this.lineColor=e.lineColor||"#1A1A1A",this.slideValue=e.slideValue||50,this.canvasName=e.canvasName||"handWriting",this.init()}return(0,o.default)(t,[{key:"init",value:function(){var t=this;this.ctx=uni.createCanvasContext(this.canvasName);var e=uni.createSelectorQuery();e.select(".handCenter").boundingClientRect((function(e){t.canvasWidth=e.width,t.canvasHeight=e.height})).exec(),this.selectSlideValue(this.slideValue)}},{key:"uploadScaleStart",value:function(t){var e=t.mp;if("touchstart"!=e.type)return!1;this.ctx.setFillStyle(this.lineColor),this.ctx.setGlobalAlpha(this.transparent),this.currentPoint={x:e.touches[0].x,y:e.touches[0].y},this.currentLine.unshift({time:(new Date).getTime(),dis:0,x:this.currentPoint.x,y:this.currentPoint.y}),this.firstTouch&&(this.cutArea={top:this.currentPoint.y,right:this.currentPoint.x,bottom:this.currentPoint.y,left:this.currentPoint.x},this.firstTouch=!1),this.pointToLine(this.currentLine)}},{key:"uploadScaleMove",value:function(t){var e=t.mp;if("touchmove"!=e.type)return!1;e.cancelable&&(e.defaultPrevented||e.preventDefault());var i={x:e.touches[0].x,y:e.touches[0].y};i.y<this.cutArea.top&&(this.cutArea.top=i.y),i.y<0&&(this.cutArea.top=0),i.x>this.cutArea.right&&(this.cutArea.right=i.x),this.canvasWidth-i.x<=0&&(this.cutArea.right=this.canvasWidth),i.y>this.cutArea.bottom&&(this.cutArea.bottom=i.y),this.canvasHeight-i.y<=0&&(this.cutArea.bottom=this.canvasHeight),i.x<this.cutArea.left&&(this.cutArea.left=i.x),i.x<0&&(this.cutArea.left=0),this.lastPoint=this.currentPoint,this.currentPoint=i,this.currentLine.unshift({time:(new Date).getTime(),dis:this.distance(this.currentPoint,this.lastPoint,"move"),x:i.x,y:i.y}),this.pointToLine(this.currentLine)}},{key:"uploadScaleEnd",value:function(t){var e=t.mp;if("touchend"!=e.type)return 0;var i={x:e.changedTouches[0].x,y:e.changedTouches[0].y};if(this.lastPoint=this.currentPoint,this.currentPoint=i,this.currentLine.unshift({time:(new Date).getTime(),dis:this.distance(this.currentPoint,this.lastPoint,"end"),x:i.x,y:i.y}),this.currentLine.length>2)this.currentLine[0].time,this.currentLine[this.currentLine.length-1].time,this.currentLine.length;this.pointToLine(this.currentLine);var n={lineSize:this.lineSize,lineColor:this.lineColor};this.chirography.unshift(n),this.linePrack.unshift(this.currentLine),this.currentLine=[]}},{key:"retDraw",value:function(){this.ctx.clearRect(0,0,700,730),this.ctx.draw()}},{key:"pointToLine",value:function(t){this.calcBethelLine(t)}},{key:"calcBethelLine",value:function(t){if(t.length<=1)t[0].r=this.radius;else{var e,i,n,a,o,s,r,c,l,u,d=0,h=0,f=.5;t.length<=2?(e=t[1].x,a=t[1].y,n=t[1].x+(t[0].x-t[1].x)*f,s=t[1].y+(t[0].y-t[1].y)*f,i=e+(n-e)*f,o=a+(s-a)*f):(e=t[2].x+(t[1].x-t[2].x)*f,a=t[2].y+(t[1].y-t[2].y)*f,i=t[1].x,o=t[1].y,n=i+(t[0].x-i)*f,s=o+(t[0].y-o)*f),l=this.distance({x:n,y:s},{x:e,y:a},"calc"),u=this.radius;for(var x=0;x<t.length-1;x++)if(d+=t[x].dis,h+=t[x].time-t[x+1].time,d>this.smoothness)break;this.radius=Math.min(h/l*this.pressure+this.lineMin,this.lineMax)*this.lineSize,t[0].r=this.radius,t.length<=2?(r=(u+this.radius)/2,c=r,c):(r=(t[2].r+t[1].r)/2,c=t[1].r,(t[1].r+t[0].r)/2);for(var v=5,b=[],p=0;p<v;p++){var w=p/(v-1),g=(1-w)*(1-w)*e+2*w*(1-w)*i+w*w*n,y=(1-w)*(1-w)*a+2*w*(1-w)*o+w*w*s,k=u+(this.radius-u)/v*p;if(b.push({x:g,y:y,r:k}),3==b.length){var m=this.ctaCalc(b[0].x,b[0].y,b[0].r,b[1].x,b[1].y,b[1].r,b[2].x,b[2].y,b[2].r);m[0].color=this.lineColor,this.bethelDraw(m,1),b=[{x:g,y:y,r:k}]}}}}},{key:"distance",value:function(t,e,i){var n=e.x-t.x,a=e.y-t.y;return 5*Math.sqrt(n*n+a*a)}},{key:"ctaCalc",value:function(t,e,i,n,a,o,s,r,c){var l,u,d,h,f,x,v,b,p,w=[];l=n-t,u=a-e,d=2*Math.sqrt(l*l+u*u+1e-4),l=l/d*i,u=u/d*i,h=u,f=-l,x=n-s,v=a-r,d=2*Math.sqrt(x*x+v*v+1e-4),x=x/d*c,v=v/d*c,b=-v,p=x,w.push({mx:t+h,my:e+f,color:"#1A1A1A"}),w.push({c1x:n+h,c1y:a+f,c2x:n+b,c2y:a+p,ex:s+b,ey:r+p}),w.push({c1x:s+b-x,c1y:r+p-v,c2x:s-b-x,c2y:r-p-v,ex:s-b,ey:r-p}),w.push({c1x:n-b,c1y:a-p,c2x:n-h,c2y:a-f,ex:t-h,ey:e-f}),w.push({c1x:t-h-l,c1y:e-f-u,c2x:t+h-l,c2y:e+f-u,ex:t+h,ey:e+f}),w[0].mx=w[0].mx.toFixed(1),w[0].mx=parseFloat(w[0].mx),w[0].my=w[0].my.toFixed(1),w[0].my=parseFloat(w[0].my);for(var g=1;g<w.length;g++)w[g].c1x=w[g].c1x.toFixed(1),w[g].c1x=parseFloat(w[g].c1x),w[g].c1y=w[g].c1y.toFixed(1),w[g].c1y=parseFloat(w[g].c1y),w[g].c2x=w[g].c2x.toFixed(1),w[g].c2x=parseFloat(w[g].c2x),w[g].c2y=w[g].c2y.toFixed(1),w[g].c2y=parseFloat(w[g].c2y),w[g].ex=w[g].ex.toFixed(1),w[g].ex=parseFloat(w[g].ex),w[g].ey=w[g].ey.toFixed(1),w[g].ey=parseFloat(w[g].ey);return w}},{key:"bethelDraw",value:function(t,e,i){this.ctx.beginPath(),this.ctx.moveTo(t[0].mx,t[0].my),void 0!=i?(this.ctx.setFillStyle(i),this.ctx.setStrokeStyle(i)):(this.ctx.setFillStyle(t[0].color),this.ctx.setStrokeStyle(t[0].color));for(var n=1;n<t.length;n++)this.ctx.bezierCurveTo(t[n].c1x,t[n].c1y,t[n].c2x,t[n].c2y,t[n].ex,t[n].ey);this.ctx.stroke(),void 0!=e&&this.ctx.fill(),this.ctx.draw(!0)}},{key:"selectColorEvent",value:function(t){this.lineColor=t}},{key:"selectSlideValue",value:function(t){switch(t){case 0:this.lineSize=.1,this.lineMin=.1,this.lineMax=.1;break;case 25:this.lineSize=1,this.lineMin=.5,this.lineMax=2;break;case 50:this.lineSize=1.5,this.lineMin=1,this.lineMax=3;break;case 75:this.lineSize=1.5,this.lineMin=2,this.lineMax=3.5;break;case 100:this.lineSize=3,this.lineMin=2,this.lineMax=3.5;break}}},{key:"saveCanvas",value:function(){var t=this;return new Promise((function(e,i){uni.canvasToTempFilePath({canvasId:t.canvasName,success:function(t){e(t.tempFilePath)},fail:function(t){i(t)}})}))}}]),t}(),c=r;e.default=c},1148:function(t,e,i){"use strict";var n=i("a691"),a=i("1d80");t.exports="".repeat||function(t){var e=String(a(this)),i="",o=n(t);if(o<0||o==1/0)throw RangeError("Wrong number of repetitions");for(;o>0;(o>>>=1)&&(e+=e))1&o&&(i+=e);return i}},1448:function(t,e,i){"use strict";var n={"uni-goods-nav":i("441d").default},a=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{staticClass:"wrapper uni-padding-wrap"},[i("v-uni-view",{staticClass:"blod uni-center title"},[t._v("店铺安装广告屏合作协议书")]),i("v-uni-view",{staticClass:"content-con"},[i("v-uni-view",{staticClass:"content"},[i("v-uni-text",{staticClass:"content-left blod"},[t._v("甲方：")]),i("v-uni-text",{staticClass:"content-right sign-border"},[t._v("四川狄霖店通传媒有限公司")])],1),i("v-uni-view",{staticClass:"content"},[i("v-uni-text",{staticClass:"content-left blod"},[t._v("乙方：")]),i("v-uni-text",{staticClass:"content-right sign-border-text"},[t._v(t._s(t.shop.shop_name))])],1),i("v-uni-view",{staticClass:"text-space"},[t._v("甲、乙双方依据中华人民共和国相关法律法规，经平等、友好协商达成如下合作协议：")]),i("v-uni-view",{staticClass:"blod"},[t._v("一、合作内容")]),i("v-uni-view",{staticClass:"content-text"},[i("v-uni-text",{staticClass:"content-left-text"},[t._v("安装数量：")]),i("v-uni-input",{staticClass:"content-right sign-border",attrs:{placeholder:"",disabled:t.inputDisabled},model:{value:t.shop.plan_quantity,callback:function(e){t.$set(t.shop,"plan_quantity",e)},expression:"shop.plan_quantity"}})],1),i("v-uni-view",{staticClass:"content-text"},[i("v-uni-text",{staticClass:"content-left-text"},[t._v("安装位置：")]),i("v-uni-text",{staticClass:"content-right sign-border-text"},[t._v(t._s(t.shop.address))])],1),i("v-uni-view",{staticClass:"content-text"},[i("v-uni-text",{staticClass:"content-left-text"},[t._v("设备总价：")]),i("v-uni-text",{staticClass:"content-right sign-border-text"},[t._v(t._s(t.shop.device_price))])],1),i("v-uni-view",{staticClass:"content-text"},[t._v("甲方将店通广告屏安装到乙方店内，并将该广告机"),i("v-uni-text",{staticClass:"fix-text"},[t._v(t._s(t.party_b_share))]),t._v("%广告利润收入支付乙方。")],1),i("v-uni-view",{staticClass:"blod"},[t._v("二、权利与义务")]),i("v-uni-view",[t._v("1、甲方承担设备的运营、管理、维修责任;")]),i("v-uni-view",[t._v("2、乙方需配合甲方完成日常运营维护的必要工作；")]),i("v-uni-view",[t._v("3、甲方负责广告的业务开拓、制作、剪辑、投放工作；")]),i("v-uni-view",[t._v("4、甲方负责终端店智能广告系统的研发、系统升级、系统维护和系统安全防护等工作；")]),i("v-uni-view",[t._v("5、当乙方因铺面装修、转让等原因申请拆除广告屏时，需提前通知甲方，甲方在收到通知之日起10天内完成拆除。")]),i("v-uni-view",[t._v("6、智能屏安装后，无协议约定的特殊情况外，须保证至少6个月内不拆除；")]),i("v-uni-view",[t._v("7、乙方的广告收入可以选择在甲方开发的APP中提取或由甲方转入乙方指定的账户，结算期为每个月最后一个工作日支付，如甲方逾期支付，乙方有权要求甲方按银行同期利率支付滞纳金。")]),i("v-uni-view",{staticClass:"blod"},[t._v("三、不可抗力因素")]),i("v-uni-view",[t._v("发生地震、滑坡、泥石流、重大公共卫生疫情等不可抗力因素导致甲、乙双方均无法正常开展经营时，双方均无须承担相应责任。")]),i("v-uni-view",{staticClass:"blod"},[t._v("四、其他")]),i("v-uni-view",[t._v("1、当甲、乙双方发生纠纷且无法协商解决时，提交甲方所在地人民法院依据相关法律法规办理。")]),i("v-uni-view",[t._v("2、如有本协议未约定的其他事项应签署补充协议约定。")])],1),i("v-uni-view",{staticClass:"sign-con"},[i("v-uni-view",{staticClass:"sign-con-item"},[i("v-uni-view",{staticClass:"blod"},[t._v("甲方（公章）：")]),i("v-uni-view",[i("v-uni-image",{staticStyle:{width:"120px",height:"120px",margin:"5px 10px 0 0"},attrs:{src:t.gz}})],1)],1),i("v-uni-view",{staticClass:"sign-con-item"},[i("v-uni-view",{staticClass:"blod"},[t._v("乙方（签名）：")]),i("v-uni-view",[i("v-uni-view",{staticClass:"showimg"},[t.showimg?i("v-uni-image",{attrs:{src:t.showimg,mode:""}}):i("v-uni-text",{staticClass:"uni-center"})],1)],1)],1)],1),t.shop.party_b_signature?t._e():i("v-uni-view",[t._e(),i("v-uni-view",{staticClass:"handCenter"},[i("v-uni-canvas",{staticClass:"handWriting",attrs:{"disable-scroll":"true","canvas-id":"handWriting"},on:{touchstart:function(e){arguments[0]=e=t.$handleEvent(e),t.uploadScaleStart.apply(void 0,arguments)},touchmove:function(e){arguments[0]=e=t.$handleEvent(e),t.uploadScaleMove.apply(void 0,arguments)},touchend:function(e){arguments[0]=e=t.$handleEvent(e),t.uploadScaleEnd.apply(void 0,arguments)},click:function(e){arguments[0]=e=t.$handleEvent(e),t.mouseDown.apply(void 0,arguments)}}})],1),i("v-uni-view",{staticClass:"buttons sign-con",staticStyle:{"margin-bottom":"100px"}},[i("v-uni-button",{staticClass:"sign-con-item",staticStyle:{margin:"0 15px"},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.retDraw.apply(void 0,arguments)}}},[t._v("重写")]),i("v-uni-button",{staticClass:"sign-con-item",staticStyle:{margin:"0 15px"},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.subCanvas.apply(void 0,arguments)}}},[t._v("确认")])],1)],1),t.shop.party_b_signature?t._e():i("v-uni-view",{staticClass:"goods-carts"},[i("uni-goods-nav",{attrs:{options:[],"button-group":t.buttonGroup,fill:!0},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.onClick.apply(void 0,arguments)},buttonClick:function(e){arguments[0]=e=t.$handleEvent(e),t.buttonClick.apply(void 0,arguments)}}})],1)],1)},o=[];i.d(e,"b",(function(){return a})),i.d(e,"c",(function(){return o})),i.d(e,"a",(function(){return n}))},"213e":function(t,e,i){var n=i("9d09");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var a=i("4f06").default;a("d2af7ac6",n,!0,{sourceMap:!1,shadowMode:!1})},"2fab":function(t,e,i){"use strict";i.r(e);var n=i("1448"),a=i("0161");for(var o in a)"default"!==o&&function(t){i.d(e,t,(function(){return a[t]}))}(o);i("52ee");var s,r=i("f0c5"),c=Object(r["a"])(a["default"],n["b"],n["c"],!1,null,"10975684",null,!1,n["a"],s);e["default"]=c.exports},"30dd":function(t,e,i){var n=i("446e");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var a=i("4f06").default;a("3036c582",n,!0,{sourceMap:!1,shadowMode:!1})},"408a":function(t,e,i){var n=i("c6b6");t.exports=function(t){if("number"!=typeof t&&"Number"!=n(t))throw TypeError("Incorrect invocation");return+t}},"441d":function(t,e,i){"use strict";i.r(e);var n=i("b377"),a=i("ff34");for(var o in a)"default"!==o&&function(t){i.d(e,t,(function(){return a[t]}))}(o);i("9a8d");var s,r=i("f0c5"),c=Object(r["a"])(a["default"],n["b"],n["c"],!1,null,"8f6570d6",null,!1,n["a"],s);e["default"]=c.exports},"446e":function(t,e,i){var n=i("24fb");e=n(!1),e.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\r\n/* 颜色变量 */\r\n/* 行为相关颜色 */\r\n/* 文字基本颜色 */\r\n/* 背景颜色 */\r\n/* 边框颜色 */\r\n/* 尺寸变量 */\r\n/* 文字尺寸 */\r\n/* 图片尺寸 */\r\n/* Border Radius */\r\n/* 水平间距 */\r\n/* 垂直间距 */\r\n/* 透明度 */\r\n/* 文章场景相关 */.flex[data-v-8f6570d6]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-direction:row;flex-direction:row}.uni-goods-nav[data-v-8f6570d6]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-flex:1;-webkit-flex:1;flex:1;-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-direction:row;flex-direction:row}.uni-tab__cart-box[data-v-8f6570d6]{-webkit-box-flex:1;-webkit-flex:1;flex:1;height:50px;background-color:#fff;z-index:900}.uni-tab__cart-sub-left[data-v-8f6570d6]{padding:0 5px}.uni-tab__cart-sub-right[data-v-8f6570d6]{-webkit-box-flex:1;-webkit-flex:1;flex:1}.uni-tab__right[data-v-8f6570d6]{margin:5px 0;margin-right:10px;border-radius:100px;overflow:hidden}.uni-tab__cart-button-left[data-v-8f6570d6]{display:-webkit-box;display:-webkit-flex;display:flex;position:relative;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;-webkit-box-align:center;-webkit-align-items:center;align-items:center;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;flex-direction:column;margin:0 10px}.uni-tab__icon[data-v-8f6570d6]{width:18px;height:18px}.image[data-v-8f6570d6]{width:18px;height:18px}.uni-tab__text[data-v-8f6570d6]{margin-top:3px;font-size:%?24?%;color:#646566}.uni-tab__cart-button-right[data-v-8f6570d6]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;flex-direction:column;-webkit-box-flex:1;-webkit-flex:1;flex:1;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;-webkit-box-align:center;-webkit-align-items:center;align-items:center}.uni-tab__cart-button-right-text[data-v-8f6570d6]{font-size:%?28?%;color:#fff}.uni-tab__cart-button-right[data-v-8f6570d6]:active{opacity:.7}.uni-tab__dot-box[data-v-8f6570d6]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;flex-direction:column;position:absolute;right:-2px;top:2px;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;-webkit-box-align:center;-webkit-align-items:center;align-items:center}.uni-tab__dot[data-v-8f6570d6]{padding:0 4px;line-height:15px;color:#fff;text-align:center;font-size:12px;background-color:red;border-radius:15px}.uni-tab__dots[data-v-8f6570d6]{padding:0 4px;border-radius:15px}.uni-tab__color-y[data-v-8f6570d6]{background-color:#ffa200}.uni-tab__color-r[data-v-8f6570d6]{background-color:red}',""]),t.exports=e},"52ee":function(t,e,i){"use strict";var n=i("213e"),a=i.n(n);a.a},"919a":function(t,e,i){"use strict";var n=i("ee27");Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var a=n(i("f3f3")),o=i("2f62"),s=n(i("10ec")),r={data:function(){return{csPhone:"",gz:"../../static/img/yz.png",shop:{shop_id:"",shop_name:"",plan_quantity:"",address:"",device_price:""},party_b_share:30,inputDisabled:!0,options:[{icon:"headphones",text:"联系客服"}],buttonGroup:[{text:"提交协议",backgroundColor:"#409EFF",color:"#fff"}],lineColor:"black",slideValue:50,handwriting:"",selectColor:"black",color:"",showimg:"",share_popup:!1}},computed:(0,a.default)({},(0,o.mapState)(["hasLogin","forcedLogin","userInfo","commonheader"])),onNavigationBarButtonTap:function(t){this.$common.actionSheetTap()},onLoad:function(t){this.shop.shop_id=t.shop_id,this.shop.shop_name=t.shop_name,this.shop.plan_quantity=t.device_count,this.shop.address=t.address,this.shop.device_price=t.total_price,this.shop.party_b_signature&&(this.party_b_share=100*this.shop.party_b_share,this.showimg=this.shop.party_b_signature,this.inputDisabled=!0),this.$nextTick((function(){this.handwriting=new s.default({lineColor:this.lineColor,slideValue:this.slideValue,canvasName:"handWriting"})}))},methods:{selectColorEvent:function(t){this.selectColor=t,"black"==t?this.color="#1A1A1A":"red"==t&&(this.color="#ca262a"),this.handwriting.selectColorEvent(this.color)},retDraw:function(){this.handwriting.retDraw(),this.showimg=""},updateValue:function(t){this.slideValue=t.detail.value,this.handwriting.selectSlideValue(this.slideValue)},uploadScaleStart:function(t){this.handwriting.uploadScaleStart(t)},uploadScaleMove:function(t){this.handwriting.uploadScaleMove(t)},uploadScaleEnd:function(t){this.handwriting.uploadScaleEnd(t)},subCanvas:function(){var t=this;this.handwriting.saveCanvas().then((function(e){t.showimg=e})).catch((function(t){}))},onClick:function(t){0==t.index&&this.callPhone(this.csPhone)},buttonClick:function(t){var e=this;if(""==this.showimg)return uni.showToast({icon:"none",title:"请签名后在提交",duration:2e3}),!1;0==t.index&&uni.showModal({title:"".concat(t.content.text),content:"阅读并确定签约合作。",success:function(t){t.confirm&&e.submitAgreement()}})},callPhone:function(t){uni.makePhoneCall({phoneNumber:t})},submitAgreement:function(){var t=this;uni.request({url:this.$serverUrl+"api/shop/"+this.shop.shop_id,data:{plan_quantity:this.shop.plan_quantity,device_price:this.shop.device_price,party_b_share:this.party_b_share,party_b_name:this.shop.user_name,party_b_signature:this.showimg},header:{commonheader:this.commonheader,"access-user-token":this.userInfo.token},method:"PUT",success:function(e){1==e.data.status?uni.showToast({icon:"none",title:"签署成功",success:function(t){uni.navigateBack()}}):uni.showModal({title:"提交失败",content:e.data.message,confirmText:"联系客服",success:function(e){e.confirm&&t.callPhone(t.csPhone)}})}})},getShop:function(){uni.request({url:this.$serverUrl+"api/shop/"+this.shop.shop_id,header:{commonheader:this.commonheader,"access-user-token":this.userInfo.token},method:"GET",success:function(t){1==t.data.status?this.shop=t.data.data:uni.showToast({icon:"none",title:t.data.message})}})}}};e.default=r},"9a8d":function(t,e,i){"use strict";var n=i("30dd"),a=i.n(n);a.a},"9d09":function(t,e,i){var n=i("24fb");e=n(!1),e.push([t.i,".title[data-v-10975684]{line-height:50px}.content[data-v-10975684]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-flow:row wrap;flex-flow:row wrap;text-align:left;margin-bottom:15px;line-height:30px}.content-text[data-v-10975684]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-flow:row wrap;flex-flow:row wrap;text-align:left}.content-left[data-v-10975684]{-webkit-flex-basis:60px;flex-basis:60px}.content-left-text[data-v-10975684]{-webkit-flex-basis:80px;flex-basis:80px}.content-right[data-v-10975684]{-webkit-box-flex:1;-webkit-flex:1;flex:1}.sign-border[data-v-10975684]{border-bottom:1px solid #333;text-align:center;font-size:14px}.sign-border-text[data-v-10975684]{border-bottom:1px solid #333;text-align:center;font-size:14px}.text-space[data-v-10975684]{text-indent:20px}.text-word[data-v-10975684]{display:inline-block;width:80px;border-bottom:1px solid #000;text-align:center;font-size:14px}.content-con[data-v-10975684]{margin-bottom:60px}.signname[data-v-10975684]{margin-top:10px}.sign-con[data-v-10975684]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-flow:row wrap;flex-flow:row wrap;text-align:center}.sign-con-item[data-v-10975684]{-webkit-box-flex:1;-webkit-flex:1;flex:1}.wrapper[data-v-10975684]{margin:%?30?% 0;overflow:hidden;display:-webkit-box;display:-webkit-flex;display:flex;-webkit-align-content:center;align-content:center;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;flex-direction:column;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;font-size:%?28?%}.handWriting[data-v-10975684]{background:#fff;width:100%;height:%?350?%}.handRight[data-v-10975684]{-webkit-box-align:center;-webkit-align-items:center;align-items:center}.handCenter[data-v-10975684]{border:%?4?% dashed #e9e9e9;-webkit-box-flex:5;-webkit-flex:5;flex:5;overflow:hidden;-webkit-box-sizing:border-box;box-sizing:border-box;width:90%;margin:0 auto}.handTitle[data-v-10975684]{-webkit-box-flex:1;-webkit-flex:1;flex:1;color:#666;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;font-size:%?30?%}.handBtn[data-v-10975684]{-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;flex-direction:column;padding:%?40?% %?20?%}.buttons[data-v-10975684]{width:100%;margin-top:%?20?%;-webkit-box-pack:justify;-webkit-justify-content:space-between;justify-content:space-between}.buttons>uni-button[data-v-10975684]{font-size:%?30?%;height:%?80?%;width:%?120?%}.delBtn[data-v-10975684]{color:#666}.color[data-v-10975684]{-webkit-box-align:center;-webkit-align-items:center;align-items:center}.color>uni-text[data-v-10975684]{margin-right:%?20?%}.subBtn[data-v-10975684]{background:#008ef6;color:#fff;text-align:center;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center}.black-select[data-v-10975684]{width:%?60?%;height:%?60?%}.black-select.color_select[data-v-10975684]{width:%?90?%;height:%?90?%}.red-select[data-v-10975684]{width:%?60?%;height:%?60?%}.red-select.color_select[data-v-10975684]{width:%?90?%;height:%?90?%}.slide-wrapper[data-v-10975684]{-webkit-box-align:center;-webkit-align-items:center;align-items:center;margin-bottom:%?20?%}.slider[data-v-10975684]{width:%?400?%;padding-left:%?20?%}.drop[data-v-10975684]{width:%?50?%;height:%?50?%;border-radius:50%;background:#fff;position:absolute;left:%?0?%;top:%?-10?%;-webkit-box-shadow:0 1px 5px #888;box-shadow:0 1px 5px #888}.slide[data-v-10975684]{width:%?250?%;height:%?30?%}.showimg[data-v-10975684]{border:%?0?% solid #e9e9e9;overflow:hidden;width:90%;margin:0 auto;background:none;height:%?350?%;margin-top:%?40?%;-webkit-box-align:center;-webkit-align-items:center;align-items:center;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center}.showimg>uni-image[data-v-10975684]{width:100%;height:100%}.showimg>uni-text[data-v-10975684]{font-size:%?40?%;color:#888}.fix-text[data-v-10975684]{border-bottom:1px solid #000;font-weight:700;width:30px;text-align:center;display:inline-block}",""]),t.exports=e},b377:function(t,e,i){"use strict";var n={"uni-icons":i("4509").default},a=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{staticClass:"uni-goods-nav"},[i("v-uni-view",{staticClass:"uni-tab__seat"}),i("v-uni-view",{staticClass:"uni-tab__cart-box flex"},[i("v-uni-view",{staticClass:"flex uni-tab__cart-sub-left"},t._l(t.options,(function(e,n){return i("v-uni-view",{key:n,staticClass:"flex uni-tab__cart-button-left uni-tab__shop-cart",on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.onClick(n,e)}}},[i("v-uni-view",{staticClass:"uni-tab__icon"},[i("uni-icons",{attrs:{type:e.icon,size:"20",color:"#646566"}})],1),i("v-uni-text",{staticClass:"uni-tab__text"},[t._v(t._s(e.text))]),i("v-uni-view",{staticClass:"flex uni-tab__dot-box"},[e.info?i("v-uni-text",{staticClass:"uni-tab__dot ",class:{"uni-tab__dots":e.info>9}},[t._v(t._s(e.info))]):t._e()],1)],1)})),1),i("v-uni-view",{staticClass:"flex uni-tab__cart-sub-right ",class:{"uni-tab__right":t.fill}},t._l(t.buttonGroup,(function(e,n){return i("v-uni-view",{key:n,staticClass:"flex uni-tab__cart-button-right",style:{backgroundColor:e.backgroundColor,color:e.color},on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.buttonClick(n,e)}}},[i("v-uni-text",{staticClass:"uni-tab__cart-button-right-text"},[t._v(t._s(e.text))])],1)})),1)],1)],1)},o=[];i.d(e,"b",(function(){return a})),i.d(e,"c",(function(){return o})),i.d(e,"a",(function(){return n}))},b680:function(t,e,i){"use strict";var n=i("23e7"),a=i("a691"),o=i("408a"),s=i("1148"),r=i("d039"),c=1..toFixed,l=Math.floor,u=function(t,e,i){return 0===e?i:e%2===1?u(t,e-1,i*t):u(t*t,e/2,i)},d=function(t){var e=0,i=t;while(i>=4096)e+=12,i/=4096;while(i>=2)e+=1,i/=2;return e},h=c&&("0.000"!==8e-5.toFixed(3)||"1"!==.9.toFixed(0)||"1.25"!==1.255.toFixed(2)||"1000000000000000128"!==(0xde0b6b3a7640080).toFixed(0))||!r((function(){c.call({})}));n({target:"Number",proto:!0,forced:h},{toFixed:function(t){var e,i,n,r,c=o(this),h=a(t),f=[0,0,0,0,0,0],x="",v="0",b=function(t,e){var i=-1,n=e;while(++i<6)n+=t*f[i],f[i]=n%1e7,n=l(n/1e7)},p=function(t){var e=6,i=0;while(--e>=0)i+=f[e],f[e]=l(i/t),i=i%t*1e7},w=function(){var t=6,e="";while(--t>=0)if(""!==e||0===t||0!==f[t]){var i=String(f[t]);e=""===e?i:e+s.call("0",7-i.length)+i}return e};if(h<0||h>20)throw RangeError("Incorrect fraction digits");if(c!=c)return"NaN";if(c<=-1e21||c>=1e21)return String(c);if(c<0&&(x="-",c=-c),c>1e-21)if(e=d(c*u(2,69,1))-69,i=e<0?c*u(2,-e,1):c/u(2,e,1),i*=4503599627370496,e=52-e,e>0){b(0,i),n=h;while(n>=7)b(1e7,0),n-=7;b(u(10,n,1),0),n=e-1;while(n>=23)p(1<<23),n-=23;p(1<<n),b(1,1),p(2),v=w()}else b(0,i),b(1<<-e,0),v=w()+s.call("0",h);return h>0?(r=v.length,v=x+(r<=h?"0."+s.call("0",h-r)+v:v.slice(0,r-h)+"."+v.slice(r-h))):v=x+v,v}})},cb29:function(t,e,i){var n=i("23e7"),a=i("81d5"),o=i("44d2");n({target:"Array",proto:!0},{fill:a}),o("fill")},ff34:function(t,e,i){"use strict";i.r(e);var n=i("0734"),a=i.n(n);for(var o in n)"default"!==o&&function(t){i.d(e,t,(function(){return n[t]}))}(o);e["default"]=a.a}}]);