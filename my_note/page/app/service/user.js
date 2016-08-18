/**
 * User Servicve
 */
app.factory('userService', ['$http', function($http) {
    return {
        getMuYangAge: function() {
            return $http.get('../muyang/age');
        }
    };
}])
