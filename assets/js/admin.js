/**
 * Created by michael on 12/23/16.
 * The admin module of the website
 */

+function () {
    var admin = angular.module('admin', [ 'account' ]);

    admin.controller('mainController', ['$scope', '$http', 'Account',
        function ($scope, $http, Account) {
        $scope.adminForm = {
            admin: "",
            pwd: ""
        };

        $scope.phraseForm = {
            phrase: "",
            origin: "",
            author: ""
        };
    }]);
}();
