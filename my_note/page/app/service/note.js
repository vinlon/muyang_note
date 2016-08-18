/**
 * Note Servicve
 */
app.factory('noteService', ['$http', function($http) {
    return {
        getLatest: function() {
            return $http.get('../note/latest');
        }
    };
}])
