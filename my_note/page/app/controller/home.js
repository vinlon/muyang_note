//
// Home Controller
//
app.controller('HomeController', ['$rootScope', '$scope', 'userService', 'GLOBAL', function($rootScope, $scope, user, g) {
    //获取年纪信息
    user.getMuYangAge().then(function(response) {
        if (response.data.return_code == g.SUCCESS_CODE) {
            $scope.age = response.data.data;
        }
    });

}]);
