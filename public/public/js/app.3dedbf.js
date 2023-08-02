/*! For license information please see app.3dedbf.js.LICENSE.txt */
"use strict";(self.webpackChunklaravue=self.webpackChunklaravue||[]).push([[8468],{95195:function(e,t,r){r.d(t,{Z:function(){return o}});var n=r(51513);function i(e,t){for(var r=0;r<t.length;r++){var n=t[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}var o=function(){function e(t){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e),this.uri=t}var t,r,o;return t=e,(r=[{key:"list",value:function(e){return(0,n.Z)({url:"/"+this.uri,method:"get",params:e})}},{key:"get",value:function(e){return(0,n.Z)({url:"/"+this.uri+"/"+e,method:"get"})}},{key:"store",value:function(e){return(0,n.Z)({url:"/"+this.uri,method:"post",data:e})}},{key:"update",value:function(e,t){return(0,n.Z)({url:"/"+this.uri+"/"+e,method:"put",data:t})}},{key:"destroy",value:function(e){return(0,n.Z)({url:"/"+this.uri+"/"+e,method:"delete"})}}])&&i(t.prototype,r),o&&i(t,o),Object.defineProperty(t,"prototype",{writable:!1}),e}()},46361:function(e,t,r){r.d(t,{Z:function(){return u}});var n=r(51513);function i(e){return i="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},i(e)}function o(e,t){for(var r=0;r<t.length;r++){var n=t[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}function a(e,t){return a=Object.setPrototypeOf?Object.setPrototypeOf.bind():function(e,t){return e.__proto__=t,e},a(e,t)}function s(e){var t=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Boolean.prototype.valueOf.call(Reflect.construct(Boolean,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,n=c(e);if(t){var i=c(this).constructor;r=Reflect.construct(n,arguments,i)}else r=n.apply(this,arguments);return l(this,r)}}function l(e,t){if(t&&("object"===i(t)||"function"==typeof t))return t;if(void 0!==t)throw new TypeError("Derived constructors may only return object or undefined");return function(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}(e)}function c(e){return c=Object.setPrototypeOf?Object.getPrototypeOf.bind():function(e){return e.__proto__||Object.getPrototypeOf(e)},c(e)}var u=function(e){!function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),Object.defineProperty(e,"prototype",{writable:!1}),t&&a(e,t)}(c,e);var t,r,i,l=s(c);function c(){return function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,c),l.call(this,"roles")}return t=c,(r=[{key:"permissions",value:function(e){return(0,n.Z)({url:"/"+this.uri+"/"+e+"/permissions",method:"get"})}}])&&o(t.prototype,r),i&&o(t,i),Object.defineProperty(t,"prototype",{writable:!1}),c}(r(95195).Z)},67376:function(e,t,r){r.d(t,{Z:function(){return a}});var n=r(43202),i={inserted:function(e,t,r){var i=t.value,o=n.Z.getters&&n.Z.getters.permissions;if(!(i&&i instanceof Array&&i.length>0))throw new Error("Permissions are required! Example: v-permission=\"['manage user','manage permission']\"");var a=i,s=o.some((function(e){return a.includes(e)}));s||e.parentNode&&e.parentNode.removeChild(e)}},o=function(e){e.directive("permission",i)};window.Vue&&(window.permission=i,Vue.use(o)),i.install=o;var a=i},59667:function(e,t,r){r.d(t,{Z:function(){return i}});var n=r(43202);function i(e){if(e&&e instanceof Array&&e.length>0){var t=n.Z.getters&&n.Z.getters.permissions,r=e;return t.some((function(e){return r.includes(e)}))}return console.error("Need permissions! Like v-permission=\"['manage permission','edit article']\""),!1}},75497:function(e,t,r){var n=r(1519),i=r.n(n)()((function(e){return e[1]}));i.push([e.id,".app-container[data-v-28cd323e]  .permission-alert{background-color:#f0f9eb;border-radius:4px;color:#67c23a;display:inline-block;margin-top:15px;padding:8px 16px;width:600px}.app-container[data-v-28cd323e]  .permission-sourceCode{margin-left:15px}.app-container[data-v-28cd323e]  .permission-tag{background-color:#ecf5ff}.app-container .description[data-v-28cd323e]{margin-bottom:15px}",""]),t.Z=i},98468:function(e,t,r){r.r(t),r.d(t,{default:function(){return m}});var n=r(67376),i=r(43202),o={inserted:function(e,t,r){var n=t.value,o=i.Z.getters&&i.Z.getters.roles;if(!(n&&n instanceof Array&&n.length>0))throw new Error("Roles are required! Example: v-role=\"['admin','editor']\"");var a=n,s=o.some((function(e){return a.includes(e)}));s||e.parentNode&&e.parentNode.removeChild(e)}},a=function(e){e.directive("role",o)};window.Vue&&(window.role=o,Vue.use(a)),o.install=a;var s=o,l=r(59667);var c={name:"DirectivePermission",components:{SwitchRoles:r(85183).Z},directives:{permission:n.Z,role:s},data:function(){return{key:1}},methods:{checkPermission:l.Z,checkRole:function(e){if(e&&e instanceof Array&&e.length>0){var t=i.Z.getters&&i.Z.getters.roles,r=e,n=t.some((function(e){return r.includes(e)}));return n}return console.error("Need roles! Like v-role=\"['admin','editor']\""),!1},handleRolesChange:function(){this.key++}}},u=c,v=r(93379),p=r.n(v),f=r(75497),d={insert:"head",singleton:!1},m=(p()(f.Z,d),f.Z.locals,(0,r(51900).Z)(u,(function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("div",{staticClass:"app-container"},[r("div",{staticClass:"description"},[e._v("\n    Laravue provides two directives "),r("el-tag",{staticClass:"permission-tag",attrs:{size:"small"}},[e._v("\n      v-role\n    ")]),e._v(" and "),r("el-tag",{staticClass:"permission-tag",attrs:{size:"small"}},[e._v("\n      v-permission\n    ")]),e._v(". Please see the belw example here for details\n  ")],1),e._v(" "),r("switch-roles",{on:{change:e.handleRolesChange}}),e._v(" "),r("div",{key:e.key,staticStyle:{"margin-top":"30px"}},[r("div",[r("span",{directives:[{name:"role",rawName:"v-role",value:["admin"],expression:"['admin']"}],staticClass:"permission-alert"},[e._v("\n        Only\n        "),r("el-tag",{staticClass:"permission-tag",attrs:{size:"small"}},[e._v("admin")]),e._v(" can see this\n      ")],1),e._v(" "),r("el-tag",{directives:[{name:"role",rawName:"v-role",value:["admin"],expression:"['admin']"}],staticClass:"permission-sourceCode",attrs:{type:"info"}},[e._v("\n        v-role=\"['admin']\"\n      ")])],1),e._v(" "),r("div",[r("span",{directives:[{name:"role",rawName:"v-role",value:["manager"],expression:"['manager']"}],staticClass:"permission-alert"},[e._v("\n        Only\n        "),r("el-tag",{staticClass:"permission-tag",attrs:{size:"small"}},[e._v("manager")]),e._v(" can see this\n      ")],1),e._v(" "),r("el-tag",{directives:[{name:"role",rawName:"v-role",value:["manager"],expression:"['manager']"}],staticClass:"permission-sourceCode",attrs:{type:"info"}},[e._v("\n        v-role=\"['manager']\"\n      ")])],1),e._v(" "),r("div",[r("span",{directives:[{name:"role",rawName:"v-role",value:["editor"],expression:"['editor']"}],staticClass:"permission-alert"},[e._v("\n        Only\n        "),r("el-tag",{staticClass:"permission-tag",attrs:{size:"small"}},[e._v("editor")]),e._v(" can see this\n      ")],1),e._v(" "),r("el-tag",{directives:[{name:"role",rawName:"v-role",value:["editor"],expression:"['editor']"}],staticClass:"permission-sourceCode",attrs:{type:"info"}},[e._v("\n        v-role=\"['editor']\"\n      ")])],1),e._v(" "),r("div",[r("span",{directives:[{name:"role",rawName:"v-role",value:["user"],expression:"['user']"}],staticClass:"permission-alert"},[e._v("\n        Only\n        "),r("el-tag",{staticClass:"permission-tag",attrs:{size:"small"}},[e._v("user")]),e._v(" can see this\n      ")],1),e._v(" "),r("el-tag",{directives:[{name:"role",rawName:"v-role",value:["user"],expression:"['user']"}],staticClass:"permission-sourceCode",attrs:{type:"info"}},[e._v("\n        v-role=\"['user']\"\n      ")])],1),e._v(" "),r("div",[r("span",{directives:[{name:"role",rawName:"v-role",value:["visitor"],expression:"['visitor']"}],staticClass:"permission-alert"},[e._v("\n        Only\n        "),r("el-tag",{staticClass:"permission-tag",attrs:{size:"small"}},[e._v("visitor")]),e._v(" can see this\n      ")],1),e._v(" "),r("el-tag",{directives:[{name:"role",rawName:"v-role",value:["visitor"],expression:"['visitor']"}],staticClass:"permission-sourceCode",attrs:{type:"info"}},[e._v("\n        v-role=\"['visitor']\"\n      ")])],1),e._v(" "),r("div",[r("span",{directives:[{name:"role",rawName:"v-role",value:["admin","editor"],expression:"['admin','editor']"}],staticClass:"permission-alert"},[e._v("\n        Both\n        "),r("el-tag",{staticClass:"permission-tag",attrs:{size:"small"}},[e._v("admin")]),e._v(" and\n        "),r("el-tag",{staticClass:"permission-tag",attrs:{size:"small"}},[e._v("editor")]),e._v(" can see this\n      ")],1),e._v(" "),r("el-tag",{directives:[{name:"role",rawName:"v-role",value:["admin","editor"],expression:"['admin','editor']"}],staticClass:"permission-sourceCode",attrs:{type:"info"}},[e._v("\n        v-role=\"['admin','editor']\"\n      ")])],1),e._v(" "),r("div",[r("span",{directives:[{name:"permission",rawName:"v-permission",value:["manage user"],expression:"['manage user']"}],staticClass:"permission-alert"},[e._v("\n        You can see this if you have "),r("el-tag",{staticClass:"permission-tag",attrs:{size:"small"}},[e._v("manage user")]),e._v(" permission\n      ")],1),e._v(" "),r("el-tag",{directives:[{name:"permission",rawName:"v-permission",value:["manage user"],expression:"['manage user']"}],staticClass:"permission-sourceCode",attrs:{type:"info"}},[e._v("\n        v-permission=\"['manage user']\"\n      ")])],1),e._v(" "),r("div",[r("span",{directives:[{name:"permission",rawName:"v-permission",value:["manage user"],expression:"['manage user']"},{name:"role",rawName:"v-role",value:["admin"],expression:"['admin']"}],staticClass:"permission-alert"},[e._v("\n        You can see this if you are "),r("el-tag",{staticClass:"permission-tag",attrs:{size:"small"}},[e._v("admin")]),e._v(" "),r("strong",[e._v("AND")]),e._v(" you have "),r("el-tag",{staticClass:"permission-tag",attrs:{size:"small"}},[e._v("manage user")]),e._v(" permission\n      ")],1),e._v(" "),r("el-tag",{directives:[{name:"permission",rawName:"v-permission",value:["manage user"],expression:"['manage user']"}],staticClass:"permission-sourceCode",attrs:{type:"info"}},[e._v("\n        v-permission=\"['manage user']\" v-role=\"['admin']\"\n      ")])],1)]),e._v(" "),r("div",{key:"checkPermission"+e.key,staticStyle:{"margin-top":"60px"}},[r("code",[e._v("\n      "+e._s(e.$t("permission.tips"))+"\n      "),r("br"),e._v(" e.g.\n    ")]),e._v(" "),r("el-tabs",{staticStyle:{width:"550px"},attrs:{type:"border-card"}},[e.checkRole(["admin"])?r("el-tab-pane",{attrs:{label:"Admin"}},[e._v("\n        Admin can see this\n        "),r("el-tag",{staticClass:"permission-sourceCode",attrs:{type:"info"}},[e._v("\n          v-if=\"checkRole(['admin'])\"\n        ")])],1):e._e(),e._v(" "),e.checkRole(["manager"])?r("el-tab-pane",{attrs:{label:"Manager"}},[e._v("\n        Manager can see this\n        "),r("el-tag",{staticClass:"permission-sourceCode",attrs:{type:"info"}},[e._v("\n          v-if=\"checkRole(['manager'])\"\n        ")])],1):e._e(),e._v(" "),e.checkRole(["editor"])?r("el-tab-pane",{attrs:{label:"Editor"}},[e._v("\n        Editor can see this\n        "),r("el-tag",{staticClass:"permission-sourceCode",attrs:{type:"info"}},[e._v("\n          v-if=\"checkRole(['editor'])\"\n        ")])],1):e._e(),e._v(" "),e.checkRole(["user"])?r("el-tab-pane",{attrs:{label:"User"}},[e._v("\n        User can see this\n        "),r("el-tag",{staticClass:"permission-sourceCode",attrs:{type:"info"}},[e._v("\n          v-if=\"checkRole(['user'])\"\n        ")])],1):e._e(),e._v(" "),e.checkRole(["visitor"])?r("el-tab-pane",{attrs:{label:"Visitor"}},[e._v("\n        Visitor can see this\n        "),r("el-tag",{staticClass:"permission-sourceCode",attrs:{type:"info"}},[e._v("\n          v-if=\"checkRole(['visitor'])\"\n        ")])],1):e._e(),e._v(" "),e.checkRole(["admin","editor"])?r("el-tab-pane",{attrs:{label:"Admin-OR-Editor"}},[e._v("\n        Both admin or editor can see this\n        "),r("el-tag",{staticClass:"permission-sourceCode",attrs:{type:"info"}},[e._v("\n          v-if=\"checkRole(['admin','editor'])\"\n        ")])],1):e._e()],1)],1)],1)}),[],!1,null,"28cd323e",null).exports)},85183:function(e,t,r){r.d(t,{Z:function(){return c}});function n(e){return n="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},n(e)}function i(){i=function(){return e};var e={},t=Object.prototype,r=t.hasOwnProperty,o="function"==typeof Symbol?Symbol:{},a=o.iterator||"@@iterator",s=o.asyncIterator||"@@asyncIterator",l=o.toStringTag||"@@toStringTag";function c(e,t,r){return Object.defineProperty(e,t,{value:r,enumerable:!0,configurable:!0,writable:!0}),e[t]}try{c({},"")}catch(e){c=function(e,t,r){return e[t]=r}}function u(e,t,r,n){var i=t&&t.prototype instanceof f?t:f,o=Object.create(i.prototype),a=new E(n||[]);return o._invoke=function(e,t,r){var n="suspendedStart";return function(i,o){if("executing"===n)throw new Error("Generator is already running");if("completed"===n){if("throw"===i)throw o;return N()}for(r.method=i,r.arg=o;;){var a=r.delegate;if(a){var s=x(a,r);if(s){if(s===p)continue;return s}}if("next"===r.method)r.sent=r._sent=r.arg;else if("throw"===r.method){if("suspendedStart"===n)throw n="completed",r.arg;r.dispatchException(r.arg)}else"return"===r.method&&r.abrupt("return",r.arg);n="executing";var l=v(e,t,r);if("normal"===l.type){if(n=r.done?"completed":"suspendedYield",l.arg===p)continue;return{value:l.arg,done:r.done}}"throw"===l.type&&(n="completed",r.method="throw",r.arg=l.arg)}}}(e,r,a),o}function v(e,t,r){try{return{type:"normal",arg:e.call(t,r)}}catch(e){return{type:"throw",arg:e}}}e.wrap=u;var p={};function f(){}function d(){}function m(){}var h={};c(h,a,(function(){return this}));var y=Object.getPrototypeOf,g=y&&y(y(O([])));g&&g!==t&&r.call(g,a)&&(h=g);var _=m.prototype=f.prototype=Object.create(h);function w(e){["next","throw","return"].forEach((function(t){c(e,t,(function(e){return this._invoke(t,e)}))}))}function b(e,t){function i(o,a,s,l){var c=v(e[o],e,a);if("throw"!==c.type){var u=c.arg,p=u.value;return p&&"object"==n(p)&&r.call(p,"__await")?t.resolve(p.__await).then((function(e){i("next",e,s,l)}),(function(e){i("throw",e,s,l)})):t.resolve(p).then((function(e){u.value=e,s(u)}),(function(e){return i("throw",e,s,l)}))}l(c.arg)}var o;this._invoke=function(e,r){function n(){return new t((function(t,n){i(e,r,t,n)}))}return o=o?o.then(n,n):n()}}function x(e,t){var r=e.iterator[t.method];if(void 0===r){if(t.delegate=null,"throw"===t.method){if(e.iterator.return&&(t.method="return",t.arg=void 0,x(e,t),"throw"===t.method))return p;t.method="throw",t.arg=new TypeError("The iterator does not provide a 'throw' method")}return p}var n=v(r,e.iterator,t.arg);if("throw"===n.type)return t.method="throw",t.arg=n.arg,t.delegate=null,p;var i=n.arg;return i?i.done?(t[e.resultName]=i.value,t.next=e.nextLoc,"return"!==t.method&&(t.method="next",t.arg=void 0),t.delegate=null,p):i:(t.method="throw",t.arg=new TypeError("iterator result is not an object"),t.delegate=null,p)}function C(e){var t={tryLoc:e[0]};1 in e&&(t.catchLoc=e[1]),2 in e&&(t.finallyLoc=e[2],t.afterLoc=e[3]),this.tryEntries.push(t)}function k(e){var t=e.completion||{};t.type="normal",delete t.arg,e.completion=t}function E(e){this.tryEntries=[{tryLoc:"root"}],e.forEach(C,this),this.reset(!0)}function O(e){if(e){var t=e[a];if(t)return t.call(e);if("function"==typeof e.next)return e;if(!isNaN(e.length)){var n=-1,i=function t(){for(;++n<e.length;)if(r.call(e,n))return t.value=e[n],t.done=!1,t;return t.value=void 0,t.done=!0,t};return i.next=i}}return{next:N}}function N(){return{value:void 0,done:!0}}return d.prototype=m,c(_,"constructor",m),c(m,"constructor",d),d.displayName=c(m,l,"GeneratorFunction"),e.isGeneratorFunction=function(e){var t="function"==typeof e&&e.constructor;return!!t&&(t===d||"GeneratorFunction"===(t.displayName||t.name))},e.mark=function(e){return Object.setPrototypeOf?Object.setPrototypeOf(e,m):(e.__proto__=m,c(e,l,"GeneratorFunction")),e.prototype=Object.create(_),e},e.awrap=function(e){return{__await:e}},w(b.prototype),c(b.prototype,s,(function(){return this})),e.AsyncIterator=b,e.async=function(t,r,n,i,o){void 0===o&&(o=Promise);var a=new b(u(t,r,n,i),o);return e.isGeneratorFunction(r)?a:a.next().then((function(e){return e.done?e.value:a.next()}))},w(_),c(_,l,"Generator"),c(_,a,(function(){return this})),c(_,"toString",(function(){return"[object Generator]"})),e.keys=function(e){var t=[];for(var r in e)t.push(r);return t.reverse(),function r(){for(;t.length;){var n=t.pop();if(n in e)return r.value=n,r.done=!1,r}return r.done=!0,r}},e.values=O,E.prototype={constructor:E,reset:function(e){if(this.prev=0,this.next=0,this.sent=this._sent=void 0,this.done=!1,this.delegate=null,this.method="next",this.arg=void 0,this.tryEntries.forEach(k),!e)for(var t in this)"t"===t.charAt(0)&&r.call(this,t)&&!isNaN(+t.slice(1))&&(this[t]=void 0)},stop:function(){this.done=!0;var e=this.tryEntries[0].completion;if("throw"===e.type)throw e.arg;return this.rval},dispatchException:function(e){if(this.done)throw e;var t=this;function n(r,n){return a.type="throw",a.arg=e,t.next=r,n&&(t.method="next",t.arg=void 0),!!n}for(var i=this.tryEntries.length-1;i>=0;--i){var o=this.tryEntries[i],a=o.completion;if("root"===o.tryLoc)return n("end");if(o.tryLoc<=this.prev){var s=r.call(o,"catchLoc"),l=r.call(o,"finallyLoc");if(s&&l){if(this.prev<o.catchLoc)return n(o.catchLoc,!0);if(this.prev<o.finallyLoc)return n(o.finallyLoc)}else if(s){if(this.prev<o.catchLoc)return n(o.catchLoc,!0)}else{if(!l)throw new Error("try statement without catch or finally");if(this.prev<o.finallyLoc)return n(o.finallyLoc)}}}},abrupt:function(e,t){for(var n=this.tryEntries.length-1;n>=0;--n){var i=this.tryEntries[n];if(i.tryLoc<=this.prev&&r.call(i,"finallyLoc")&&this.prev<i.finallyLoc){var o=i;break}}o&&("break"===e||"continue"===e)&&o.tryLoc<=t&&t<=o.finallyLoc&&(o=null);var a=o?o.completion:{};return a.type=e,a.arg=t,o?(this.method="next",this.next=o.finallyLoc,p):this.complete(a)},complete:function(e,t){if("throw"===e.type)throw e.arg;return"break"===e.type||"continue"===e.type?this.next=e.arg:"return"===e.type?(this.rval=this.arg=e.arg,this.method="return",this.next="end"):"normal"===e.type&&t&&(this.next=t),p},finish:function(e){for(var t=this.tryEntries.length-1;t>=0;--t){var r=this.tryEntries[t];if(r.finallyLoc===e)return this.complete(r.completion,r.afterLoc),k(r),p}},catch:function(e){for(var t=this.tryEntries.length-1;t>=0;--t){var r=this.tryEntries[t];if(r.tryLoc===e){var n=r.completion;if("throw"===n.type){var i=n.arg;k(r)}return i}}throw new Error("illegal catch attempt")},delegateYield:function(e,t,r){return this.delegate={iterator:O(e),resultName:t,nextLoc:r},"next"===this.method&&(this.arg=void 0),p}},e}function o(e,t,r,n,i,o,a){try{var s=e[o](a),l=s.value}catch(e){return void r(e)}s.done?t(l):Promise.resolve(l).then(n,i)}var a=new(r(46361).Z),s={data:function(){return{avaiableRoles:[],loading:!1,list:[]}},computed:{roles:function(){return this.$store.getters.roles},switchRoles:{get:function(){return this.roles[0]},set:function(e){var t=this,r=this.list.find((function(t){return t.name===e}));this.$store.dispatch("user/changeRoles",r).then((function(){t.$emit("change")}))}}},created:function(){this.getRoles()},methods:{getRoles:function(){var e,t=this;return(e=i().mark((function e(){var r,n;return i().wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return t.loading=!0,e.next=3,a.list({});case 3:r=e.sent,n=r.data,t.list=n,t.list.forEach((function(e,r){e.index=r+1,e.description=t.$t("roles.description."+e.name)})),t.loading=!1;case 8:case"end":return e.stop()}}),e)})),function(){var t=this,r=arguments;return new Promise((function(n,i){var a=e.apply(t,r);function s(e){o(a,n,i,s,l,"next",e)}function l(e){o(a,n,i,s,l,"throw",e)}s(void 0)}))})()}}},l=s,c=(0,r(51900).Z)(l,(function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("div",[r("div",{staticStyle:{"margin-bottom":"15px"}},[e._v("\n    "+e._s(e.$t("permission.roles"))+": "+e._s(e.roles)+"\n  ")]),e._v(" "),r("div",{staticStyle:{"margin-bottom":"15px"}},[e._v("\n    "+e._s(e.$t("permission.switchRoles"))+":\n  ")]),e._v(" "),r("div",[r("el-radio-group",{directives:[{name:"loading",rawName:"v-loading",value:e.loading,expression:"loading"}],model:{value:e.switchRoles,callback:function(t){e.switchRoles=t},expression:"switchRoles"}},[r("el-radio-button",{attrs:{label:"admin"}}),e._v(" "),r("el-radio-button",{attrs:{label:"manager"}}),e._v(" "),r("el-radio-button",{attrs:{label:"editor"}}),e._v(" "),r("el-radio-button",{attrs:{label:"user"}}),e._v(" "),r("el-radio-button",{attrs:{label:"visitor"}})],1)],1)])}),[],!1,null,null,null).exports}}]);