(self.webpackChunklaravue=self.webpackChunklaravue||[]).push([[1881],{65354:function(e,t,a){"use strict";var n=a(1519),r=a.n(n)()((function(e){return e[1]}));r.push([e.id,".excel-upload-input[data-v-84b37832]{display:none;z-index:-9999}.drop[data-v-84b37832]{border:2px dashed #bbb;border-radius:5px;color:#bbb;font-size:24px;height:160px;line-height:160px;margin:0 auto;position:relative;text-align:center;width:600px}",""]),t.Z=r},23205:function(e,t,a){"use strict";a.r(t),a.d(t,{default:function(){return p}});var n=a(7869),r=a.n(n),l={props:{beforeUpload:Function,onSuccess:Function},data:function(){return{loading:!1,excelData:{header:null,results:null}}},methods:{generateData:function(e){var t=e.header,a=e.results;this.excelData.header=t,this.excelData.results=a,this.onSuccess&&this.onSuccess(this.excelData)},handleDrop:function(e){if(e.stopPropagation(),e.preventDefault(),!this.loading){var t=e.dataTransfer.files;if(1===t.length){var a=t[0];if(!this.isExcel(a))return this.$message.error("Only supports upload .xlsx, .xls, .csv suffix files"),!1;this.upload(a),e.stopPropagation(),e.preventDefault()}else this.$message.error("Only support uploading one file!")}},handleDragover:function(e){e.stopPropagation(),e.preventDefault(),e.dataTransfer.dropEffect="copy"},handleUpload:function(){this.$refs["excel-upload-input"].click()},handleClick:function(e){var t=e.target.files[0];t&&this.upload(t)},upload:function(e){(this.$refs["excel-upload-input"].value=null,this.beforeUpload)?this.beforeUpload(e)&&this.readerData(e):this.readerData(e)},readerData:function(e){var t=this;return this.loading=!0,new Promise((function(a,n){var l=new FileReader;l.onload=function(e){var n=e.target.result,l=r().read(n,{type:"array"}),s=l.SheetNames[0],o=l.Sheets[s],i=t.getHeaderRow(o),u=r().utils.sheet_to_json(o);t.generateData({header:i,results:u}),t.loading=!1,a()},l.readAsArrayBuffer(e)}))},getHeaderRow:function(e){var t,a=[],n=r().utils.decode_range(e["!ref"]),l=n.s.r;for(t=n.s.c;t<=n.e.c;++t){var s=e[r().utils.encode_cell({c:t,r:l})],o="UNKNOWN "+t;s&&s.t&&(o=r().utils.format_cell(s)),a.push(o)}return a},isExcel:function(e){return/\.(xlsx|xls|csv)$/.test(e.name)}}},s=a(93379),o=a.n(s),i=a(65354),u={insert:"head",singleton:!1},c=(o()(i.Z,u),i.Z.locals,a(51900)),d={name:"UploadExcel",components:{UploadExcelComponent:(0,c.Z)(l,(function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("div",[a("input",{ref:"excel-upload-input",staticClass:"excel-upload-input",attrs:{type:"file",accept:".xlsx, .xls"},on:{change:e.handleClick}}),e._v(" "),a("div",{staticClass:"drop",on:{drop:e.handleDrop,dragover:e.handleDragover,dragenter:e.handleDragover}},[e._v("\n    Drop excel file here or\n    "),a("el-button",{staticStyle:{"margin-left":"16px"},attrs:{loading:e.loading,size:"mini",type:"primary"},on:{click:e.handleUpload}},[e._v("\n      Browse\n    ")])],1)])}),[],!1,null,"84b37832",null).exports},data:function(){return{tableData:[],tableHeader:[]}},methods:{beforeUpload:function(e){return e.size/1024/1024<1||(this.$message({message:"Please do not upload files larger than 1m in size.",type:"warning"}),!1)},handleSuccess:function(e){var t=e.results,a=e.header;this.tableData=t,this.tableHeader=a}}},p=(0,c.Z)(d,(function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("div",{staticClass:"app-container"},[a("upload-excel-component",{attrs:{"on-success":e.handleSuccess,"before-upload":e.beforeUpload}}),e._v(" "),a("el-table",{staticStyle:{width:"100%","margin-top":"20px"},attrs:{data:e.tableData,border:"","highlight-current-row":""}},e._l(e.tableHeader,(function(e){return a("el-table-column",{key:e,attrs:{prop:e,label:e}})})),1)],1)}),[],!1,null,null,null).exports},18685:function(){},20067:function(){},55382:function(){},72095:function(){},91298:function(){}}]);