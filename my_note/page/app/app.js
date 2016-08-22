// 
// Here is how to define your module 
// has dependent on mobile-angular-ui
// 
var app = angular.module('MobileAngularUiExamples', [
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

.run(function($transform) {
  window.$transform = $transform;
})

//定义全局变量
.constant('GLOBAL', {
    'API_HOST': '../',
    'SUCCESS_CODE': 200,
    'IMAGE_HOST': '../'
})

//httpProvider默认设置
.run(function($http) {
    //设置接口请求需要的ticket
    $http.defaults.headers.common = { 'ticket': 'limuyang' };
    //指定Post数据格式
    $http.defaults.headers.post = { 'Content-Type': 'application/json' };
})