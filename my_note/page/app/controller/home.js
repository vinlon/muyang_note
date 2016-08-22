//
// Home Controller
//
app.controller('HomeCtrl', ['$rootScope', '$scope', 'userService', 'noteService', 'GLOBAL', function($rootScope, $scope, user, note, g) {
    //获取年纪信息
    user.getMuYangAge().then(function(response) {
        if (response.data.return_code == g.SUCCESS_CODE) {
            $scope.age = response.data.data;
        }
    });

    //获取最新动态
    $scope.IMAGE_HOST = g.IMAGE_HOST;
    note.getLatest().then(function(response){
        if (response.data.return_code == g.SUCCESS_CODE) {
            $scope.latest = response.data.data;
        }
    });

}]);
