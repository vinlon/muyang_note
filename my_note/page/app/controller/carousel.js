//
// Carousel Controller
//
app.controller('CarouselCtrl', ['$rootScope', '$scope', '$location', 'noteService', 'GLOBAL',
    function($rootScope, $scope, $location, note, g) {
        $scope.IMAGE_HOST = g.IMAGE_HOST;
        //获取图片日志
        note.getImageList().then(function(response) {
            if (response.data.return_code == g.SUCCESS_CODE) {
                $scope.image_list = response.data.data;
            }
        });
    }
]);
