(self.webpackChunklaravue=self.webpackChunklaravue||[]).push([[5008],{95888:function(e,t,n){"use strict";var r=n(1519),i=n.n(r)()((function(e){return e[1]}));i.push([e.id,".components-container[data-v-4608d3cf]{height:100vh;position:relative}.left-container[data-v-4608d3cf]{background-color:#f38181;height:100%}.right-container[data-v-4608d3cf]{background-color:#fce38a;height:200px}.top-container[data-v-4608d3cf]{background-color:#fce38a;height:100%;width:100%}.bottom-container[data-v-4608d3cf]{background-color:#95e1d3;height:100%;width:100%}",""]),t.Z=i},95008:function(e,t,n){"use strict";n.r(t),n.d(t,{default:function(){return l}});var r=n(8530),i={name:"SplitpaneDemo",components:{splitPane:n.n(r)()},methods:{resize:function(){console.log("resize")}}},s=n(93379),a=n.n(s),o=n(95888),c={insert:"head",singleton:!1},l=(a()(o.Z,c),o.Z.locals,(0,n(51900).Z)(i,(function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"components-container"},[n("split-pane",{attrs:{split:"vertical"},on:{resize:e.resize}},[n("template",{slot:"paneL"},[n("div",{staticClass:"left-container"})]),e._v(" "),n("template",{slot:"paneR"},[n("split-pane",{attrs:{split:"horizontal"}},[n("template",{slot:"paneL"},[n("div",{staticClass:"top-container"})]),e._v(" "),n("template",{slot:"paneR"},[n("div",{staticClass:"bottom-container"})])],2)],1)],2)],1)}),[],!1,null,"4608d3cf",null).exports)},8530:function(e){e.exports=function(e){function t(r){if(n[r])return n[r].exports;var i=n[r]={i:r,l:!1,exports:{}};return e[r].call(i.exports,i,i.exports,t),i.l=!0,i.exports}var n={};return t.m=e,t.c=n,t.i=function(e){return e},t.d=function(e,n,r){t.o(e,n)||Object.defineProperty(e,n,{configurable:!1,enumerable:!0,get:r})},t.n=function(e){var n=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(n,"a",n),n},t.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},t.p="/dist/",t(t.s=7)}([function(e,t){e.exports=function(){var e=[];return e.toString=function(){for(var e=[],t=0;t<this.length;t++){var n=this[t];n[2]?e.push("@media "+n[2]+"{"+n[1]+"}"):e.push(n[1])}return e.join("")},e.i=function(t,n){"string"==typeof t&&(t=[[null,t,""]]);for(var r={},i=0;i<this.length;i++){var s=this[i][0];"number"==typeof s&&(r[s]=!0)}for(i=0;i<t.length;i++){var a=t[i];"number"==typeof a[0]&&r[a[0]]||(n&&!a[2]?a[2]=n:n&&(a[2]="("+a[2]+") and ("+n+")"),e.push(a))}},e}},function(e,t){e.exports=function(e,t,n,r){var i,s=e=e||{},a=typeof e.default;"object"!==a&&"function"!==a||(i=e,s=e.default);var o="function"==typeof s?s.options:s;if(t&&(o.render=t.render,o.staticRenderFns=t.staticRenderFns),n&&(o._scopeId=n),r){var c=Object.create(o.computed||null);Object.keys(r).forEach((function(e){var t=r[e];c[e]=function(){return t}})),o.computed=c}return{esModule:i,exports:s,options:o}}},function(e,t,n){function r(e){for(var t=0;t<e.length;t++){var n=e[t],r=u[n.id];if(r){r.refs++;for(var i=0;i<r.parts.length;i++)r.parts[i](n.parts[i]);for(;i<n.parts.length;i++)r.parts.push(s(n.parts[i]));r.parts.length>n.parts.length&&(r.parts.length=n.parts.length)}else{var a=[];for(i=0;i<n.parts.length;i++)a.push(s(n.parts[i]));u[n.id]={id:n.id,refs:1,parts:a}}}}function i(){var e=document.createElement("style");return e.type="text/css",p.appendChild(e),e}function s(e){var t,n,r=document.querySelector('style[data-vue-ssr-id~="'+e.id+'"]');if(r){if(h)return v;r.parentNode.removeChild(r)}if(m){var s=f++;r=d||(d=i()),t=a.bind(null,r,s,!1),n=a.bind(null,r,s,!0)}else r=i(),t=o.bind(null,r),n=function(){r.parentNode.removeChild(r)};return t(e),function(r){if(r){if(r.css===e.css&&r.media===e.media&&r.sourceMap===e.sourceMap)return;t(e=r)}else n()}}function a(e,t,n,r){var i=n?"":r.css;if(e.styleSheet)e.styleSheet.cssText=g(t,i);else{var s=document.createTextNode(i),a=e.childNodes;a[t]&&e.removeChild(a[t]),a.length?e.insertBefore(s,a[t]):e.appendChild(s)}}function o(e,t){var n=t.css,r=t.media,i=t.sourceMap;if(r&&e.setAttribute("media",r),i&&(n+="\n/*# sourceURL="+i.sources[0]+" */",n+="\n/*# sourceMappingURL=data:application/json;base64,"+btoa(unescape(encodeURIComponent(JSON.stringify(i))))+" */"),e.styleSheet)e.styleSheet.cssText=n;else{for(;e.firstChild;)e.removeChild(e.firstChild);e.appendChild(document.createTextNode(n))}}var c="undefined"!=typeof document;if("undefined"!=typeof DEBUG&&DEBUG&&!c)throw new Error("vue-style-loader cannot be used in a non-browser environment. Use { target: 'node' } in your Webpack config to indicate a server-rendering environment.");var l=n(19),u={},p=c&&(document.head||document.getElementsByTagName("head")[0]),d=null,f=0,h=!1,v=function(){},m="undefined"!=typeof navigator&&/msie [6-9]\b/.test(navigator.userAgent.toLowerCase());e.exports=function(e,t,n){h=n;var i=l(e,t);return r(i),function(t){for(var n=[],s=0;s<i.length;s++){var a=i[s];(o=u[a.id]).refs--,n.push(o)}for(t?r(i=l(e,t)):i=[],s=0;s<n.length;s++){var o;if(0===(o=n[s]).refs){for(var c=0;c<o.parts.length;c++)o.parts[c]();delete u[o.id]}}}};var g=function(){var e=[];return function(t,n){return e[t]=n,e.filter(Boolean).join("\n")}}()},function(e,t,n){n(17);var r=n(1)(n(4),n(14),"data-v-566a42b8",null);e.exports=r.exports},function(e,t,n){"use strict";function r(e){return e&&e.__esModule?e:{default:e}}Object.defineProperty(t,"__esModule",{value:!0});var i=r(n(12)),s=r(n(11));t.default={name:"splitPane",components:{Resizer:i.default,Pane:s.default},props:{minPercent:{type:Number,default:10},defaultPercent:{type:Number,default:50},split:{validator:function(e){return["vertical","horizontal"].indexOf(e)>=0},required:!0},className:String},computed:{userSelect:function(){return this.active?"none":""},cursor:function(){return this.active?"vertical"===this.split?"col-resize":"row-resize":""}},watch:{defaultPercent:function(e,t){this.percent=e}},data:function(){return{active:!1,hasMoved:!1,height:null,percent:this.defaultPercent,type:"vertical"===this.split?"width":"height",resizeType:"vertical"===this.split?"left":"top"}},methods:{onClick:function(){this.hasMoved||(this.percent=50,this.$emit("resize",this.percent))},onMouseDown:function(){this.active=!0,this.hasMoved=!1},onMouseUp:function(){this.active=!1},onMouseMove:function(e){if(0!==e.buttons&&0!==e.which||(this.active=!1),this.active){var t=0,n=e.currentTarget;if("vertical"===this.split)for(;n;)t+=n.offsetLeft,n=n.offsetParent;else for(;n;)t+=n.offsetTop,n=n.offsetParent;var r="vertical"===this.split?e.pageX:e.pageY,i="vertical"===this.split?e.currentTarget.offsetWidth:e.currentTarget.offsetHeight,s=Math.floor((r-t)/i*1e4)/100;s>this.minPercent&&s<100-this.minPercent&&(this.percent=s),this.$emit("resize",this.percent),this.hasMoved=!0}}}}},function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default={name:"Pane",props:{className:String},data:function(){return{classes:[this.$parent.split,this.className].join(" "),percent:50}}}},function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default={props:{split:{validator:function(e){return["vertical","horizontal"].indexOf(e)>=0},required:!0},className:String},computed:{classes:function(){return["splitter-pane-resizer",this.split,this.className].join(" ")}}}},function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var r=function(e){return e&&e.__esModule?e:{default:e}}(n(3));t.default=r.default,"undefined"!=typeof window&&window.Vue&&window.Vue.component("split-pane",r.default)},function(e,t,n){(e.exports=n(0)()).push([e.i,".splitter-pane-resizer[data-v-212fa2a4]{box-sizing:border-box;background:#000;position:absolute;opacity:.2;z-index:1;background-clip:padding-box}.splitter-pane-resizer.horizontal[data-v-212fa2a4]{height:11px;margin:-5px 0;border-top:5px solid hsla(0,0%,100%,0);border-bottom:5px solid hsla(0,0%,100%,0);cursor:row-resize;width:100%}.splitter-pane-resizer.vertical[data-v-212fa2a4]{width:11px;height:100%;margin-left:-5px;border-left:5px solid hsla(0,0%,100%,0);border-right:5px solid hsla(0,0%,100%,0);cursor:col-resize}",""])},function(e,t,n){(e.exports=n(0)()).push([e.i,'.clearfix[data-v-566a42b8]:after{visibility:hidden;display:block;font-size:0;content:" ";clear:both;height:0}.vue-splitter-container[data-v-566a42b8]{height:100%;position:relative}.vue-splitter-container-mask[data-v-566a42b8]{z-index:9999;width:100%;height:100%;position:absolute;top:0;left:0}',""])},function(e,t,n){(e.exports=n(0)()).push([e.i,".splitter-pane.vertical.splitter-paneL[data-v-815c801c]{position:absolute;left:0;height:100%;padding-right:3px}.splitter-pane.vertical.splitter-paneR[data-v-815c801c]{position:absolute;right:0;height:100%;padding-left:3px}.splitter-pane.horizontal.splitter-paneL[data-v-815c801c]{position:absolute;top:0;width:100%}.splitter-pane.horizontal.splitter-paneR[data-v-815c801c]{position:absolute;bottom:0;width:100%;padding-top:3px}",""])},function(e,t,n){n(18);var r=n(1)(n(5),n(15),"data-v-815c801c",null);e.exports=r.exports},function(e,t,n){n(16);var r=n(1)(n(6),n(13),"data-v-212fa2a4",null);e.exports=r.exports},function(e,t){e.exports={render:function(){var e=this,t=e.$createElement;return(e._self._c||t)("div",{class:e.classes})},staticRenderFns:[]}},function(e,t){e.exports={render:function(){var e,t,n,r=this,i=r.$createElement,s=r._self._c||i;return s("div",{staticClass:"vue-splitter-container clearfix",style:{cursor:r.cursor,userSelect:r.userSelect},on:{mouseup:r.onMouseUp,mousemove:r.onMouseMove}},[s("pane",{staticClass:"splitter-pane splitter-paneL",style:(e={},e[r.type]=r.percent+"%",e),attrs:{split:r.split}},[r._t("paneL")],2),r._v(" "),s("resizer",{style:(t={},t[r.resizeType]=r.percent+"%",t),attrs:{className:r.className,split:r.split},nativeOn:{mousedown:function(e){return r.onMouseDown(e)},click:function(e){return r.onClick(e)}}}),r._v(" "),s("pane",{staticClass:"splitter-pane splitter-paneR",style:(n={},n[r.type]=100-r.percent+"%",n),attrs:{split:r.split}},[r._t("paneR")],2),r._v(" "),r.active?s("div",{staticClass:"vue-splitter-container-mask"}):r._e()],1)},staticRenderFns:[]}},function(e,t){e.exports={render:function(){var e=this,t=e.$createElement;return(e._self._c||t)("div",{class:e.classes},[e._t("default")],2)},staticRenderFns:[]}},function(e,t,n){var r=n(8);"string"==typeof r&&(r=[[e.i,r,""]]),r.locals&&(e.exports=r.locals),n(2)("a82a4610",r,!0)},function(e,t,n){var r=n(9);"string"==typeof r&&(r=[[e.i,r,""]]),r.locals&&(e.exports=r.locals),n(2)("033d59ad",r,!0)},function(e,t,n){var r=n(10);"string"==typeof r&&(r=[[e.i,r,""]]),r.locals&&(e.exports=r.locals),n(2)("6816c93c",r,!0)},function(e,t){e.exports=function(e,t){for(var n=[],r={},i=0;i<t.length;i++){var s=t[i],a=s[0],o={id:e+":"+i,css:s[1],media:s[2],sourceMap:s[3]};r[a]?r[a].parts.push(o):n.push(r[a]={id:a,parts:[o]})}return n}}])}}]);