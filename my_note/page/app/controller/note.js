//
// Note Controller
//
app.controller('NoteCtrl', ['$rootScope', '$scope', '$location', 'noteService', 'GLOBAL',
    function($rootScope, $scope, $location, note, g) {
        var openid = $location.search()['openid'];
        //获取文本日志
        note.getTextList(openid).then(function(response) {
            if (response.data.return_code == g.SUCCESS_CODE) {
                $scope.text_list = response.data.data;
            }
        });

        //删除文本日志
        $scope.textDel = function(selected_note) {
            note.textDel(openid, selected_note.key).then(function(response) {
                if (response.data.return_code == g.SUCCESS_CODE) {
                    $scope.text_list.splice($scope.text_list.indexOf(selected_note), 1);
                }
            });
        }

    }
]);
