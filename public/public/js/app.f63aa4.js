"use strict";(self.webpackChunklaravue=self.webpackChunklaravue||[]).push([[4381],{34488:function(t,e,n){n.d(e,{V8:function(){return a},Xc:function(){return r},lE:function(){return o},mE:function(){return i},tu:function(){return u}});var l=n(51513);function i(t){return(0,l.Z)({url:"/articles",method:"get",params:t})}function a(t){return(0,l.Z)({url:"/articles/"+t,method:"get"})}function o(t){return(0,l.Z)({url:"/articles/"+t+"/pageviews",method:"get"})}function u(t){return(0,l.Z)({url:"/article/create",method:"post",data:t})}function r(t){return(0,l.Z)({url:"/article/update",method:"post",data:t})}},66459:function(t,e,n){var l=n(1519),i=n.n(l)()((function(t){return t[1]}));i.push([t.id,".radio-label{color:#606266;font-size:14px;line-height:40px;padding:0 12px 0 30px}",""]),e.Z=i},24381:function(t,e,n){n.r(e),n.d(e,{default:function(){return h}});var l=n(34488),i=n(55245),a={props:{value:{type:String,default:""}},computed:{filename:{get:function(){return this.value},set:function(t){this.$emit("input",t)}}}},o=n(51900),u=(0,o.Z)(a,(function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticStyle:{display:"inline-block"}},[n("label",{staticClass:"radio-label",staticStyle:{"padding-left":"0"}},[t._v("Filename: ")]),t._v(" "),n("el-input",{staticStyle:{width:"340px"},attrs:{placeholder:t.$t("excel.placeholder"),"prefix-icon":"el-icon-document"},model:{value:t.filename,callback:function(e){t.filename=e},expression:"filename"}})],1)}),[],!1,null,null,null).exports,r=(0,o.Z)({},undefined,undefined,!1,null,null,null).exports,s={props:{value:{type:String,default:"xlsx"}},data:function(){return{options:["xlsx","csv","txt"]}},computed:{bookType:{get:function(){return this.value},set:function(t){this.$emit("input",t)}}}},c={name:"ExportExcel",components:{FilenameOption:u,AutoWidthOption:r,BookTypeOption:(0,o.Z)(s,(function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticStyle:{display:"inline-block"}},[n("label",{staticClass:"radio-label"},[t._v("Book Type: ")]),t._v(" "),n("el-select",{staticStyle:{width:"120px"},model:{value:t.bookType,callback:function(e){t.bookType=e},expression:"bookType"}},t._l(t.options,(function(t){return n("el-option",{key:t,attrs:{label:t,value:t}})})),1)],1)}),[],!1,null,null,null).exports},data:function(){return{list:null,listLoading:!0,downloadLoading:!1,filename:"",autoWidth:!0,bookType:"xlsx"}},created:function(){this.fetchData()},methods:{fetchData:function(){var t=this;this.listLoading=!0,(0,l.mE)().then((function(e){t.list=e.data.items,t.listLoading=!1}))},handleDownload:function(){var t=this;this.downloadLoading=!0,Promise.all([n.e(8898),n.e(8516)]).then(n.bind(n,89442)).then((function(e){var n=t.list,l=t.formatJson(["id","title","author","pageviews","display_time"],n);e.export_json_to_excel({header:["Id","Title","Author","Readings","Date"],data:l,filename:t.filename,autoWidth:t.autoWidth,bookType:t.bookType}),t.downloadLoading=!1}))},formatJson:function(t,e){return e.map((function(e){return t.map((function(t){return"timestamp"===t?(0,i.TD)(e[t]):e[t]}))}))}}},d=n(93379),p=n.n(d),f=n(66459),m={insert:"head",singleton:!1},h=(p()(f.Z,m),f.Z.locals,(0,o.Z)(c,(function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"app-container"},[n("div",[n("FilenameOption",{model:{value:t.filename,callback:function(e){t.filename=e},expression:"filename"}}),t._v(" "),n("AutoWidthOption",{model:{value:t.autoWidth,callback:function(e){t.autoWidth=e},expression:"autoWidth"}}),t._v(" "),n("BookTypeOption",{model:{value:t.bookType,callback:function(e){t.bookType=e},expression:"bookType"}}),t._v(" "),n("el-button",{staticStyle:{margin:"0 0 20px 20px"},attrs:{loading:t.downloadLoading,type:"primary",icon:"document"},on:{click:t.handleDownload}},[t._v("\n      "+t._s(t.$t("excel.export"))+" Excel\n    ")]),t._v(" "),n("a",{staticStyle:{"margin-left":"15px"},attrs:{href:"https://panjiachen.github.io/vue-element-admin-site/feature/component/excel.html",target:"_blank"}},[n("el-tag",{attrs:{type:"info"}},[t._v("Documentation")])],1)],1),t._v(" "),n("el-table",{directives:[{name:"loading",rawName:"v-loading",value:t.listLoading,expression:"listLoading"}],attrs:{data:t.list,"element-loading-text":"拼命加载中",border:"",fit:"","highlight-current-row":""}},[n("el-table-column",{attrs:{align:"center",label:"Id",width:"95"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v("\n        "+t._s(e.$index)+"\n      ")]}}])}),t._v(" "),n("el-table-column",{attrs:{label:"Title"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v("\n        "+t._s(e.row.title)+"\n      ")]}}])}),t._v(" "),n("el-table-column",{attrs:{label:"Author",width:"110",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[n("el-tag",[t._v(t._s(e.row.author))])]}}])}),t._v(" "),n("el-table-column",{attrs:{label:"Readings",width:"115",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v("\n        "+t._s(e.row.pageviews)+"\n      ")]}}])}),t._v(" "),n("el-table-column",{attrs:{align:"center",label:"Date",width:"220"},scopedSlots:t._u([{key:"default",fn:function(e){return[n("i",{staticClass:"el-icon-time"}),t._v(" "),n("span",[t._v(t._s(t._f("parseTime")(e.row.timestamp,"{y}-{m}-{d} {h}:{i}")))])]}}])})],1)],1)}),[],!1,null,null,null).exports)}}]);