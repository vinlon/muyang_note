/**
 * Note Servicve
 */
app.factory('noteService', ['$http', function($http) {
    return {
        getLatest: function() {
            return $http.get('../note/latest');
        },
        getTextList: function(openid) {
            var data = { openid: openid };
            return $http.post('../note/textList', data)
        },
        textDel: function(openid, key) {
            var data = {
                'openid': openid,
                'type': 'text',
                'key': key
            };
            return $http.post('../note/delete', data);
        }
    };
}])
