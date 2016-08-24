// 
// Here is how to define your module 
// has dependent on mobile-angular-ui
// 
var app = angular.module('MuYangNote', [
  'ngRoute',
  'mobile-angular-ui',
  
  // touch/drag feature: this is from 'mobile-angular-ui.gestures.js'
  // it is at a very beginning stage, so please be careful if you like to use
  // in production. This is intended to provide a flexible, integrated and and 
  // easy to use alternative to other 3rd party libs like hammer.js, with the
  // final pourpose to integrate gestures into default ui interactions like 
  // opening sidebars, turning switches on/off ..
  'mobile-angular-ui.gestures'
])

.run(['$transform', '$location', function($transform, $location) {
  window.$transform = $transform;

  //从GET参数中获取身份标识
  var openid = $location.search()['openid'];
  if(openid != true && openid != undefined){
    localStorage.openid = openid;
  }
  $location.url($location.path());
}])

//定义全局变量
.constant('GLOBAL', {
    'API_HOST': '../',
    'SUCCESS_CODE': 200,
    'IMAGE_HOST': '../'
})

//httpProvider默认设置
.run(function($http) {
    //设置接口请求需要的ticket
    $http.defaults.headers.common = { 'ticket': localStorage.openid };
    //指定Post数据格式
    $http.defaults.headers.post = { 'Content-Type': 'application/json' };
})