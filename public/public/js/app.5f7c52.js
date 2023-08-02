(self.webpackChunklaravue=self.webpackChunklaravue||[]).push([[6381],{87755:function(n){n.exports="/images/login_background.jpg?81f0d7831ea4c4bd55dbfd6f8cec5f03"},49148:function(n){n.exports="/images/logo.png?72526e1f7521ca26ad76924492e4b08f"},60107:function(n,t,e){"use strict";var i=e(1519),o=e.n(i)()((function(n){return n[1]}));o.push([n.id,".login-container .el-input{display:inline-block;height:47px;width:85%}.login-container .el-input input{-webkit-appearance:none;background:transparent;border:0;border-radius:0;color:#eee;height:47px;padding:12px 5px 12px 15px}.login-container .el-input input:-webkit-autofill{-webkit-text-colorfill-color:#080707!important;-webkit-box-shadow:inset 0 0 0 1000px #2d3a4b!important}.login-container .el-form-item{background:rgba(0,0,0,.1);border:1px solid hsla(0,0%,100%,.1);border-radius:5px;color:#454545}",""]),t.Z=o},98382:function(n,t,e){"use strict";var i=e(1519),o=e.n(i)()((function(n){return n[1]}));o.push([n.id,".login[data-v-769b62ac]{-webkit-box-align:center;-ms-flex-align:center;-webkit-box-pack:center;-ms-flex-pack:center;align-items:center;background-color:#054b5d;display:-webkit-box;display:-ms-flexbox;display:flex;height:100%;justify-content:center;overflow:auto;padding:20px;-webkit-transition:background-color .3s ease-in-out;transition:background-color .3s ease-in-out}.login .login-container[data-v-769b62ac]{background:#2d3a4b;display:grid;grid-template-columns:auto 480px;min-height:590px;-webkit-transform:scale(1);transform:scale(1);-webkit-transition:all .3s ease-in-out;transition:all .3s ease-in-out;width:1120px}.login .login-container .logo[data-v-769b62ac]{display:block;height:80px;margin-bottom:20px;width:80px}.login .login-container .login-image[data-v-769b62ac]{-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-box-pack:end;-ms-flex-pack:end;background-color:#303c4b;background-position:50%;background-size:cover;display:-webkit-box;display:-ms-flexbox;display:flex;-ms-flex-direction:row;flex-direction:row;justify-content:flex-end;opacity:1;overflow:hidden;-webkit-transition:opacity .3s ease-in-out,padding .2s ease-in-out;transition:opacity .3s ease-in-out,padding .2s ease-in-out}.login .login-container .login-image .photo-credit[data-v-769b62ac]{-webkit-box-pack:end;-ms-flex-pack:end;-ms-flex-item-align:end;align-self:flex-end;background-color:hsla(0,0%,100%,.8);justify-content:flex-end;margin:10px;padding:5px 8px}.login .login-container .login-image .photo-credit h4[data-v-769b62ac],.login .login-container .login-image .photo-credit span[data-v-769b62ac]{margin:0}.login .login-container .login-form[data-v-769b62ac]{min-width:320px;opacity:1;padding:130px 60px;position:relative;-webkit-transition:opacity .3s ease-in-out,padding .2s ease-in-out;transition:opacity .3s ease-in-out,padding .2s ease-in-out}.login .login-container .tips[data-v-769b62ac]{color:#fff;font-size:14px;margin-bottom:10px}.login .login-container .tips span[data-v-769b62ac]:first-of-type{margin-right:16px}.login .login-container .svg-container[data-v-769b62ac]{color:#889aa4;display:inline-block;padding:6px 5px 6px 15px;vertical-align:middle;width:30px}.login .login-container .title-wrap[data-v-769b62ac]{display:block;margin-bottom:15px}.login .login-container .title-wrap .title[data-v-769b62ac]{color:#eee;font-size:24px;font-weight:700;margin:0 auto 10px;text-align:left}.login .login-container .title-wrap .sub-heading[data-v-769b62ac]{color:#eee;font-size:14px;padding-bottom:15px}.login .login-container .show-pwd[data-v-769b62ac]{color:#889aa4;cursor:pointer;font-size:16px;position:absolute;right:10px;top:7px;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none}.login .login-container .set-language[data-v-769b62ac]{color:#eee;position:absolute;right:35px;top:40px}",""]),t.Z=o},26381:function(n,t,e){"use strict";e.r(t),e.d(t,{default:function(){return h}});var i=e(13167),o=e(86251),a=e(1808),r=e(49148),l=e.n(r),s=e(87755),c=e.n(s),d={name:"Login",components:{LangSelect:i.Z},data:function(){return{loginForm:{email:"admin@laravue.dev",password:"laravue"},loginRules:{email:[{required:!0,trigger:"blur",validator:function(n,t,e){(0,o.u$)(t)?e():e(new Error("Please enter the correct email"))}}],password:[{required:!0,trigger:"blur",validator:function(n,t,e){t.length<4?e(new Error("Password cannot be less than 4 digits")):e()}}]},loading:!1,pwdType:"password",redirect:void 0,otherQuery:{},logo:l(),loginBackground:c()}},watch:{$route:{handler:function(n){var t=n.query;t&&(this.redirect=t.redirect,this.otherQuery=this.getOtherQuery(t))},immediate:!0}},methods:{showPwd:function(){"password"===this.pwdType?this.pwdType="":this.pwdType="password"},handleLogin:function(){var n=this;this.$refs.loginForm.validate((function(t){if(!t)return console.log("error submit!!"),!1;n.loading=!0,(0,a.EN)().then((function(){n.$store.dispatch("user/login",n.loginForm).then((function(){n.$router.push({path:n.redirect||"/",query:n.otherQuery},(function(n){})),n.loading=!1})).catch((function(){n.loading=!1}))}))}))},getOtherQuery:function(n){return Object.keys(n).reduce((function(t,e){return"redirect"!==e&&(t[e]=n[e]),t}),{})},loginBackgroundImage:function(){return"url('"+this.loginBackground+"')"}}},g=e(93379),p=e.n(g),u=e(60107),m={insert:"head",singleton:!1},b=(p()(u.Z,m),u.Z.locals,e(98382)),f={insert:"head",singleton:!1},h=(p()(b.Z,f),b.Z.locals,(0,e(51900).Z)(d,(function(){var n=this,t=n.$createElement,e=n._self._c||t;return e("div",{staticClass:"login"},[e("div",{staticClass:"login-container"},[e("div",{staticClass:"login-image",style:{"background-image":"url("+n.loginBackground+")"}},[n._m(0)]),n._v(" "),e("div",{staticClass:"login-content"},[e("el-form",{ref:"loginForm",staticClass:"login-form",attrs:{model:n.loginForm,rules:n.loginRules,"auto-complete":"on","label-position":"left"}},[e("div",{staticClass:"title-wrap"},[e("img",{staticClass:"logo",attrs:{alt:"Laravue",src:n.logo}}),n._v(" "),e("h3",{staticClass:"title"},[n._v("\n            "+n._s(n.$t("login.title"))+"\n            "),e("lang-select",{staticClass:"set-language"})],1)]),n._v(" "),e("el-form-item",{attrs:{prop:"email"}},[e("span",{staticClass:"svg-container"},[e("svg-icon",{attrs:{"icon-class":"user"}})],1),n._v(" "),e("el-input",{attrs:{name:"email",type:"text","auto-complete":"on",placeholder:n.$t("login.email")},model:{value:n.loginForm.email,callback:function(t){n.$set(n.loginForm,"email",t)},expression:"loginForm.email"}})],1),n._v(" "),e("el-form-item",{attrs:{prop:"password"}},[e("span",{staticClass:"svg-container"},[e("svg-icon",{attrs:{"icon-class":"password"}})],1),n._v(" "),e("el-input",{attrs:{name:"password","auto-complete":"on",placeholder:"password",type:n.pwdType},nativeOn:{keyup:function(t){return!t.type.indexOf("key")&&n._k(t.keyCode,"enter",13,t.key,"Enter")?null:n.handleLogin(t)}},model:{value:n.loginForm.password,callback:function(t){n.$set(n.loginForm,"password",t)},expression:"loginForm.password"}}),n._v(" "),e("span",{staticClass:"show-pwd",on:{click:n.showPwd}},[e("svg-icon",{attrs:{"icon-class":"eye"}})],1)],1),n._v(" "),e("el-form-item",[e("el-button",{staticStyle:{width:"100%"},attrs:{loading:n.loading,type:"primary"},nativeOn:{click:function(t){return t.preventDefault(),n.handleLogin(t)}}},[n._v("\n            "+n._s(n.$t("login.logIn"))+"\n          ")])],1),n._v(" "),e("div",{staticClass:"tips"},[e("span",{staticStyle:{"margin-right":"20px"}},[n._v("Email: admin@laravue.dev")]),n._v(" "),e("span",[n._v("Password: laravue")])])],1)],1)])])}),[function(){var n=this,t=n.$createElement,e=n._self._c||t;return e("div",{staticClass:"photo-credit"},[e("h4",[n._v("Danang - Vietnam")]),n._v(" "),e("span",[n._v("Photo by Kiril Dobrev on Unsplash")])])}],!1,null,"769b62ac",null).exports)}}]);