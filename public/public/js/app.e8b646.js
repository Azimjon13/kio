"use strict";(self.webpackChunklaravue=self.webpackChunklaravue||[]).push([[2048],{23701:function(e,t,n){var i=n(1519),o=n.n(i)()((function(e){return e[1]}));o.push([e.id,'.main-article{background:#fff;display:block;margin:0 auto;padding:20px;width:740px}.article__heading{overflow:hidden;padding:0 0 20px;position:relative}.article__heading__title{-webkit-box-orient:vertical;-webkit-line-clamp:2;line-clamp:2;word-wrap:break-word;color:#333;display:block;display:-webkit-box;font-size:32px;font-weight:600;line-height:48px;overflow:hidden;overflow-wrap:break-word}.node-article-content{color:#333;font-family:medium-content-serif-font,Georgia,Cambria,Times New Roman,Times,serif;font-size:16px;letter-spacing:.5px;line-height:28px;margin:20px 0 30px}.node-article-content:after,.node-article-content:before{clear:both;content:"";display:table}.node-article-content>:last-child{margin-bottom:0}.node-article-content b,.node-article-content strong{font-weight:inherit;font-weight:bolder}.node-article-content img{display:block;margin:0 auto;max-width:100%}.node-article-content p{font-size:21px;font-style:normal;font-weight:400;letter-spacing:-.003em;line-height:1.58}.node-article-content ul{margin-bottom:30px}.node-article-content li{--x-height-multiplier:0.375;--baseline-multiplier:0.17;font-size:21px;font-style:normal;font-weight:400;letter-spacing:.01rem;letter-spacing:-.003em;line-height:1.58;margin-bottom:14px;margin-left:30px}.node-article-content a{background-image:-webkit-gradient(linear,left top,right top,color-stop(100%,rgba(0,0,0,.84)),color-stop(0,transparent));background-image:linear-gradient(90deg,rgba(0,0,0,.84) 100%,transparent 0);background-position:0 calc(1em + 1px);background-repeat:repeat-x;background-size:1px 1px;padding:0 6px}.node-article-content code{background:rgba(0,0,0,.05);display:inline-block;font-size:16px;margin:0 2px;padding:3px 4px}.node-article-content img{-ms-interpolation-mode:bicubic;border:0}.node-article-content blockquote{--x-height-multiplier:0.375;--baseline-multiplier:0.17;border-left:3px solid rgba(0,0,0,.84);font-family:medium-content-serif-font,Georgia,Cambria,Times New Roman,Times,serif;font-size:21px;font-style:italic;font-weight:400;letter-spacing:.01rem;letter-spacing:-.003em;line-height:1.58;margin-left:-23px;padding-bottom:2px;padding-left:20px}.node-article-content a{text-decoration:none}.node-article-content h2,.node-article-content h3,.node-article-content h4{font-size:34px;letter-spacing:-.015em;line-height:1.15;margin:53px 0 0}.node-article-content h4{font-size:26px}',""]),t.Z=o},12048:function(e,t,n){n.r(t),n.d(t,{default:function(){return c}});var i={data:function(){return{article:"",fullscreenLoading:!0}},mounted:function(){this.fetchData()},methods:{fetchData:function(){var e=this;n.e(7035).then(n.bind(n,57035)).then((function(t){var n=t.default.title;document.title=n,e.article=t.default,setTimeout((function(){e.fullscreenLoading=!1,e.$nextTick((function(){window.print()}))}),3e3)}))}}},o=n(93379),a=n.n(o),r=n(23701),l={insert:"head",singleton:!1},c=(a()(r.Z,l),r.Z.locals,(0,n(51900).Z)(i,(function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{directives:[{name:"loading",rawName:"v-loading.fullscreen.lock",value:e.fullscreenLoading,expression:"fullscreenLoading",modifiers:{fullscreen:!0,lock:!0}}],staticClass:"main-article",attrs:{"element-loading-text":"Efforts to generate PDF"}},[n("div",{staticClass:"article__heading"},[n("div",{staticClass:"article__heading__title"},[e._v("\n      "+e._s(e.article.title)+"\n    ")])]),e._v(" "),e._m(0),e._v(" "),n("div",{ref:"content",staticClass:"node-article-content",domProps:{innerHTML:e._s(e.article.content)}})])}),[function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticStyle:{color:"#ccc"}},[e._v("\n    This article is from Evan You on\n    "),n("a",{attrs:{target:"_blank",href:"https://medium.com/the-vue-point/plans-for-the-next-iteration-of-vue-js-777ffea6fabf"}},[e._v("medium")])])}],!1,null,null,null).exports)}}]);