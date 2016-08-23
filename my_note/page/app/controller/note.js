//
// Note Controller
//
app.controller('NoteCtrl', ['$rootScope', '$scope', '$location', 'noteService', 'GLOBAL',
    function($rootScope, $scope, $location, note, g) {
        //获取文本日志
        note.getTextList().then(function(response) {
            if (response.data.return_code == g.SUCCESS_CODE) {
                $scope.text_list = response.data.data;
            }
        });

        $scope.IMAGE_HOST = g.IMAGE_HOST;
        //获取图片日志
        note.getImageList().then(function(response) {
            if (response.data.return_code == g.SUCCESS_CODE) {
                $scope.image_list = response.data.data;
            }
        });

        //删除文本日志
        $scope.textDel = function(selected_note) {
            note.textDel(selected_note.key).then(function(response) {
                if (response.data.return_code == g.SUCCESS_CODE) {
                    $scope.text_list.splice($scope.text_list.indexOf(selected_note), 1);
                }
            });
        }

        //删除图片日志
        $scope.imageDel = function(selected_note) {
            note.imageDel(selected_note.key).then(function(response) {
                if (response.data.return_code == g.SUCCESS_CODE) {
                    $scope.text_list.splice($scope.text_list.indexOf(selected_note), 1);
                }
            });
        }

    }
]);
