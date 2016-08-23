/**
 * Note Servicve
 */
app.factory('noteService', ['$http', function($http) {
    return {
        getLatest: function() {
            return $http.get('../note/latest');
        },
        getTextList: function() {
            return $http.post('../note/textList', {})
        },
        getImageList: function() {
            return $http.post('../note/imageList', {})
        },
        textDel: function(key) {
            var data = {
                'type': 'text',
                'key': key
            };
            return $http.post('../note/delete', data);
        },
        imageDel: function(key) {
            var data = {
                'type': 'image',
                'key': key
            };
            return $http.post('../note/delete', data);
        }
    };
}])
