/**
 * Created by michael on 9/30/16.
 */
+function() {
    var TEMPLATE_DIR = "assets/templates/";
    var whack = angular.module('whack', [ 'ngRoute' ]);

    whack.config(function ( $routeProvider ) {
       $routeProvider
           .when('/', {
               templateUrl: TEMPLATE_DIR + "main.html",
               controller: "mainController"
           });
    });

    whack.controller('mainController',
        ['$scope', function ( $scope ) {

    }]);
}();
