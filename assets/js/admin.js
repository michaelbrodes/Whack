/**
 * Created by michael on 12/23/16.
 * The admin module of the website
 */

+function () {
    var admin = angular.module('admin', [ 'account' ]);

    admin.controller('mainController', ['$http', 'Account',
        function ($http, Account) {
    }]);
}();
