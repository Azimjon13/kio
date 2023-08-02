/*! For license information please see app.08dee7.js.LICENSE.txt */
"use strict";(self.webpackChunklaravue=self.webpackChunklaravue||[]).push([[3207],{7080:function(t,e,r){r.d(e,{AA:function(){return o},BW:function(){return c},Dv:function(){return l},UE:function(){return a},Yo:function(){return i}});var n=r(51513);function a(t){return(0,n.Z)({url:"/placesbymanager/"+t,method:"GET"})}function o(){return(0,n.Z)({url:"/managers",method:"get"})}function i(t){return(0,n.Z)({url:"/places",method:"POST",data:t})}function l(t,e){return(0,n.Z)({url:"/places/"+t,method:"PATCH",data:e})}function c(t){return(0,n.Z)({url:"/places/"+t,method:"DELETE"})}},23207:function(t,e,r){r.r(e),r.d(e,{default:function(){return s}});var n=r(7080);function a(t){return a="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},a(t)}function o(){o=function(){return t};var t={},e=Object.prototype,r=e.hasOwnProperty,n="function"==typeof Symbol?Symbol:{},i=n.iterator||"@@iterator",l=n.asyncIterator||"@@asyncIterator",c=n.toStringTag||"@@toStringTag";function s(t,e,r){return Object.defineProperty(t,e,{value:r,enumerable:!0,configurable:!0,writable:!0}),t[e]}try{s({},"")}catch(t){s=function(t,e,r){return t[e]=r}}function u(t,e,r,n){var a=e&&e.prototype instanceof h?e:h,o=Object.create(a.prototype),i=new E(n||[]);return o._invoke=function(t,e,r){var n="suspendedStart";return function(a,o){if("executing"===n)throw new Error("Generator is already running");if("completed"===n){if("throw"===a)throw o;return S()}for(r.method=a,r.arg=o;;){var i=r.delegate;if(i){var l=_(i,r);if(l){if(l===f)continue;return l}}if("next"===r.method)r.sent=r._sent=r.arg;else if("throw"===r.method){if("suspendedStart"===n)throw n="completed",r.arg;r.dispatchException(r.arg)}else"return"===r.method&&r.abrupt("return",r.arg);n="executing";var c=d(t,e,r);if("normal"===c.type){if(n=r.done?"completed":"suspendedYield",c.arg===f)continue;return{value:c.arg,done:r.done}}"throw"===c.type&&(n="completed",r.method="throw",r.arg=c.arg)}}}(t,r,i),o}function d(t,e,r){try{return{type:"normal",arg:t.call(e,r)}}catch(t){return{type:"throw",arg:t}}}t.wrap=u;var f={};function h(){}function p(){}function v(){}var m={};s(m,i,(function(){return this}));var y=Object.getPrototypeOf,g=y&&y(y(k([])));g&&g!==e&&r.call(g,i)&&(m=g);var w=v.prototype=h.prototype=Object.create(m);function b(t){["next","throw","return"].forEach((function(e){s(t,e,(function(t){return this._invoke(e,t)}))}))}function P(t,e){function n(o,i,l,c){var s=d(t[o],t,i);if("throw"!==s.type){var u=s.arg,f=u.value;return f&&"object"==a(f)&&r.call(f,"__await")?e.resolve(f.__await).then((function(t){n("next",t,l,c)}),(function(t){n("throw",t,l,c)})):e.resolve(f).then((function(t){u.value=t,l(u)}),(function(t){return n("throw",t,l,c)}))}c(s.arg)}var o;this._invoke=function(t,r){function a(){return new e((function(e,a){n(t,r,e,a)}))}return o=o?o.then(a,a):a()}}function _(t,e){var r=t.iterator[e.method];if(void 0===r){if(e.delegate=null,"throw"===e.method){if(t.iterator.return&&(e.method="return",e.arg=void 0,_(t,e),"throw"===e.method))return f;e.method="throw",e.arg=new TypeError("The iterator does not provide a 'throw' method")}return f}var n=d(r,t.iterator,e.arg);if("throw"===n.type)return e.method="throw",e.arg=n.arg,e.delegate=null,f;var a=n.arg;return a?a.done?(e[t.resultName]=a.value,e.next=t.nextLoc,"return"!==e.method&&(e.method="next",e.arg=void 0),e.delegate=null,f):a:(e.method="throw",e.arg=new TypeError("iterator result is not an object"),e.delegate=null,f)}function x(t){var e={tryLoc:t[0]};1 in t&&(e.catchLoc=t[1]),2 in t&&(e.finallyLoc=t[2],e.afterLoc=t[3]),this.tryEntries.push(e)}function L(t){var e=t.completion||{};e.type="normal",delete e.arg,t.completion=e}function E(t){this.tryEntries=[{tryLoc:"root"}],t.forEach(x,this),this.reset(!0)}function k(t){if(t){var e=t[i];if(e)return e.call(t);if("function"==typeof t.next)return t;if(!isNaN(t.length)){var n=-1,a=function e(){for(;++n<t.length;)if(r.call(t,n))return e.value=t[n],e.done=!1,e;return e.value=void 0,e.done=!0,e};return a.next=a}}return{next:S}}function S(){return{value:void 0,done:!0}}return p.prototype=v,s(w,"constructor",v),s(v,"constructor",p),p.displayName=s(v,c,"GeneratorFunction"),t.isGeneratorFunction=function(t){var e="function"==typeof t&&t.constructor;return!!e&&(e===p||"GeneratorFunction"===(e.displayName||e.name))},t.mark=function(t){return Object.setPrototypeOf?Object.setPrototypeOf(t,v):(t.__proto__=v,s(t,c,"GeneratorFunction")),t.prototype=Object.create(w),t},t.awrap=function(t){return{__await:t}},b(P.prototype),s(P.prototype,l,(function(){return this})),t.AsyncIterator=P,t.async=function(e,r,n,a,o){void 0===o&&(o=Promise);var i=new P(u(e,r,n,a),o);return t.isGeneratorFunction(r)?i:i.next().then((function(t){return t.done?t.value:i.next()}))},b(w),s(w,c,"Generator"),s(w,i,(function(){return this})),s(w,"toString",(function(){return"[object Generator]"})),t.keys=function(t){var e=[];for(var r in t)e.push(r);return e.reverse(),function r(){for(;e.length;){var n=e.pop();if(n in t)return r.value=n,r.done=!1,r}return r.done=!0,r}},t.values=k,E.prototype={constructor:E,reset:function(t){if(this.prev=0,this.next=0,this.sent=this._sent=void 0,this.done=!1,this.delegate=null,this.method="next",this.arg=void 0,this.tryEntries.forEach(L),!t)for(var e in this)"t"===e.charAt(0)&&r.call(this,e)&&!isNaN(+e.slice(1))&&(this[e]=void 0)},stop:function(){this.done=!0;var t=this.tryEntries[0].completion;if("throw"===t.type)throw t.arg;return this.rval},dispatchException:function(t){if(this.done)throw t;var e=this;function n(r,n){return i.type="throw",i.arg=t,e.next=r,n&&(e.method="next",e.arg=void 0),!!n}for(var a=this.tryEntries.length-1;a>=0;--a){var o=this.tryEntries[a],i=o.completion;if("root"===o.tryLoc)return n("end");if(o.tryLoc<=this.prev){var l=r.call(o,"catchLoc"),c=r.call(o,"finallyLoc");if(l&&c){if(this.prev<o.catchLoc)return n(o.catchLoc,!0);if(this.prev<o.finallyLoc)return n(o.finallyLoc)}else if(l){if(this.prev<o.catchLoc)return n(o.catchLoc,!0)}else{if(!c)throw new Error("try statement without catch or finally");if(this.prev<o.finallyLoc)return n(o.finallyLoc)}}}},abrupt:function(t,e){for(var n=this.tryEntries.length-1;n>=0;--n){var a=this.tryEntries[n];if(a.tryLoc<=this.prev&&r.call(a,"finallyLoc")&&this.prev<a.finallyLoc){var o=a;break}}o&&("break"===t||"continue"===t)&&o.tryLoc<=e&&e<=o.finallyLoc&&(o=null);var i=o?o.completion:{};return i.type=t,i.arg=e,o?(this.method="next",this.next=o.finallyLoc,f):this.complete(i)},complete:function(t,e){if("throw"===t.type)throw t.arg;return"break"===t.type||"continue"===t.type?this.next=t.arg:"return"===t.type?(this.rval=this.arg=t.arg,this.method="return",this.next="end"):"normal"===t.type&&e&&(this.next=e),f},finish:function(t){for(var e=this.tryEntries.length-1;e>=0;--e){var r=this.tryEntries[e];if(r.finallyLoc===t)return this.complete(r.completion,r.afterLoc),L(r),f}},catch:function(t){for(var e=this.tryEntries.length-1;e>=0;--e){var r=this.tryEntries[e];if(r.tryLoc===t){var n=r.completion;if("throw"===n.type){var a=n.arg;L(r)}return a}}throw new Error("illegal catch attempt")},delegateYield:function(t,e,r){return this.delegate={iterator:k(t),resultName:e,nextLoc:r},"next"===this.method&&(this.arg=void 0),f}},t}function i(t,e,r,n,a,o,i){try{var l=t[o](i),c=l.value}catch(t){return void r(t)}l.done?e(c):Promise.resolve(c).then(n,a)}function l(t){return function(){var e=this,r=arguments;return new Promise((function(n,a){var o=t.apply(e,r);function l(t){i(o,n,a,l,c,"next",t)}function c(t){i(o,n,a,l,c,"throw",t)}l(void 0)}))}}var c={created:function(){this.loadPlaces()},data:function(){return{showAddPlace:!1,placesInManagerList:[],addPlace:{id_manager:null,title:"",sort:1},showEditPlace:!1,editPlace:{id_manager:null,title:"",sort:1},placesList:[]}},methods:{loadPlaces:function(){var t=this;return l(o().mark((function e(){var r,a;return o().wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,(0,n.AA)();case 2:r=e.sent,a=r.data,console.log(a),t.placesList=a;case 6:case"end":return e.stop()}}),e)})))()},handleShowAddPlace:function(t){this.showAddPlace=!0,this.placesInManagerList=this.placesList.find((function(e){return e.id==t})).places,this.addPlace={id_manager:t,title:"",sort:1}},handleCloseAddPlace:function(){this.showAddPlace=!1,this.placesInManagerList=[],this.addPlace={id_manager:null,title:"",sort:1}},addPlaceHandler:function(){var t=this;return l(o().mark((function e(){var r;return o().wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,(0,n.Yo)(t.addPlace);case 2:if((r=e.sent).errors||!r.status){e.next=8;break}return t.$message({message:"Площадка успешно добавлена!",type:"success",duration:5e3}),t.handleCloseAddPlace(),t.loadPlaces(),e.abrupt("return");case 8:t.$message({message:r.message,type:"error",duration:5e3});case 9:case"end":return e.stop()}}),e)})))()},handleShowAddChildPlace:function(t){this.showAddPlace=!0,this.placesInManagerList=this.placesList.flatMap((function(t){return t.places})).find((function(e){return e==t})).places,this.addPlace={id_manager:t.id_manager,title:"",id_parent:t.id,sort:1}},handleShowEditPlace:function(t){console.log(t),t.id_parent?this.placesInManagerList=this.placesList.find((function(e){return e.id==t.id_manager})).places.find((function(e){return e.id==t.id_parent})).places.filter((function(e){return e.sort!=t.sort})):this.placesInManagerList=this.placesList.find((function(e){return e.id==t.id_manager})).places.filter((function(e){return e.sort!=t.sort})),this.editPlace=t,this.showEditPlace=!0},handleCloseEditPlace:function(){this.placesInManagerList=[],this.editPlace={id_manager:null,title:"",sort:1},this.showEditPlace=!1},editPlaceHandler:function(){var t=this;return l(o().mark((function e(){var r;return o().wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,(0,n.Dv)(t.editPlace.id,t.editPlace);case 2:if((r=e.sent).errors||!r.status){e.next=8;break}return t.$message({message:"Площадка успешно обновлена!",type:"success",duration:5e3}),t.handleCloseEditPlace(),t.loadPlaces(),e.abrupt("return");case 8:t.$message({message:r.message,type:"error",duration:5e3});case 9:case"end":return e.stop()}}),e)})))()},removePlaceHandler:function(t){var e=this;return l(o().mark((function r(){var a;return o().wrap((function(r){for(;;)switch(r.prev=r.next){case 0:return r.prev=0,r.next=3,e.$confirm("Вы точно хотите удалить эту площадку? \n                                                Все связанные данные с этой площадкой \n                                                могут быть утеряны.","Внимание!",{type:"warning"});case 3:if("confirm"==r.sent){r.next=6;break}return r.abrupt("return");case 6:r.next=12;break;case 8:return r.prev=8,r.t0=r.catch(0),console.log(r.t0),r.abrupt("return");case 12:return r.next=14,(0,n.BW)(t);case 14:if((a=r.sent).errors||!a.status){r.next=19;break}return e.$message({message:"Площадка успешно удалена!",type:"success",duration:5e3}),e.loadPlaces(),r.abrupt("return");case 19:e.$message({message:a.message,type:"error",duration:5e3});case 20:case"end":return r.stop()}}),r,null,[[0,8]])})))()}}},s=(0,r(51900).Z)(c,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("div",{staticClass:"app-container"},[t._v("\n    Справочник Площадок\n    "),r("el-table",{ref:"divTable",attrs:{"row-key":"id","tree-props":{children:"places",hasChildren:"hasChildren"},data:t.placesList,fit:"","highlight-current-row":"",border:""}},[r("el-table-column",{attrs:{"min-width":5}}),t._v(" "),r("el-table-column",{attrs:{label:"ID","min-width":5,prop:"id"}}),t._v(" "),r("el-table-column",{attrs:{label:"Наименование","min-width":75,prop:"title"}}),t._v(" "),r("el-table-column",{attrs:{label:"Доп. ф-ии","min-width":15},scopedSlots:t._u([{key:"default",fn:function(e){return[r("div",{staticStyle:{"text-align":"center"}},[e.row.id_manager?e.row.id_parent?t._e():r("el-button",{attrs:{type:"success",icon:"el-icon-plus",circle:""},on:{click:function(r){return t.handleShowAddChildPlace(e.row)}}}):r("el-button",{attrs:{type:"success",icon:"el-icon-plus",circle:""},on:{click:function(r){return t.handleShowAddPlace(e.row.id)}}}),t._v(" "),e.row.id_manager?r("el-button",{attrs:{type:"info",icon:"el-icon-edit",circle:""},on:{click:function(r){return t.handleShowEditPlace(e.row)}}}):t._e(),t._v(" "),e.row.id_manager?r("el-button",{attrs:{type:"danger",icon:"el-icon-delete",circle:""},on:{click:function(r){return t.removePlaceHandler(e.row.id)}}}):t._e()],1)]}}])})],1),t._v(" "),r("el-dialog",{attrs:{title:"Добавление площадки",visible:t.showAddPlace,width:"60%;","before-close":t.handleCloseAddPlace},on:{"update:visible":function(e){t.showAddPlace=e}}},[r("div",{staticClass:"form-container"},[r("el-form",{ref:"addPlaceForm",attrs:{model:t.addPlace,"label-position":"top"}},[r("el-row",{attrs:{gutter:20}},[r("el-col",{attrs:{span:12}},[r("el-form-item",{attrs:{label:"Наименование площадки"}},[r("el-input",{model:{value:t.addPlace.title,callback:function(e){t.$set(t.addPlace,"title",e)},expression:"addPlace.title"}})],1)],1),t._v(" "),r("el-col",{attrs:{span:12}},[r("el-form-item",{attrs:{label:"Порядок сортировки"}},[r("el-select",{staticStyle:{width:"100%"},model:{value:t.addPlace.sort,callback:function(e){t.$set(t.addPlace,"sort",e)},expression:"addPlace.sort"}},[r("el-option",{attrs:{value:1,label:"Первым элементом"}}),t._v(" "),t._l(t.placesInManagerList,(function(t){return r("el-option",{key:t.id,attrs:{label:"После "+t.title,value:t.sort+1}})}))],2)],1)],1)],1)],1),t._v(" "),r("span",{staticClass:"dialog-footer",staticStyle:{float:"right"},attrs:{slot:"footer"},slot:"footer"},[r("el-button",{attrs:{type:"primary"},on:{click:function(e){return t.addPlaceHandler()}}},[t._v("\n                    Сохранить\n                ")]),t._v(" "),r("el-button",{attrs:{type:"danger"},on:{click:function(e){return t.handleCloseAddPlace()}}},[t._v("\n                    "+t._s(t.$t("table.cancel"))+"\n                ")])],1),t._v(" "),r("br"),r("br")],1)]),t._v(" "),r("el-dialog",{attrs:{title:"Редактирование площадки",visible:t.showEditPlace,width:"60%;","before-close":t.handleCloseEditPlace},on:{"update:visible":function(e){t.showEditPlace=e}}},[r("div",{staticClass:"form-container"},[r("el-form",{ref:"editPlaceForm",attrs:{model:t.editPlace,"label-position":"top"}},[r("el-row",{attrs:{gutter:20}},[r("el-col",{attrs:{span:12}},[r("el-form-item",{attrs:{label:"Наименование площадки"}},[r("el-input",{model:{value:t.editPlace.title,callback:function(e){t.$set(t.editPlace,"title",e)},expression:"editPlace.title"}})],1)],1),t._v(" "),r("el-col",{attrs:{span:12}},[r("el-form-item",{attrs:{label:"Порядок сортировки"}},[r("el-select",{staticStyle:{width:"100%"},model:{value:t.editPlace.sort,callback:function(e){t.$set(t.editPlace,"sort",e)},expression:"editPlace.sort"}},[r("el-option",{attrs:{value:1,label:"Первым элементом"}}),t._v(" "),t._l(t.placesInManagerList,(function(t){return r("el-option",{key:t.sort+1,attrs:{label:"После "+t.title,value:t.sort+1}})}))],2)],1)],1)],1)],1),t._v(" "),r("span",{staticClass:"dialog-footer",staticStyle:{float:"right"},attrs:{slot:"footer"},slot:"footer"},[r("el-button",{attrs:{type:"primary"},on:{click:function(e){return t.editPlaceHandler()}}},[t._v("\n                    Сохранить\n                ")]),t._v(" "),r("el-button",{attrs:{type:"danger"},on:{click:function(e){return t.handleCloseEditPlace()}}},[t._v("\n                    "+t._s(t.$t("table.cancel"))+"\n                ")])],1),t._v(" "),r("br"),r("br")],1)])],1)}),[],!1,null,null,null).exports}}]);