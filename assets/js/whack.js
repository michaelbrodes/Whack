/**
 * Created by michael on 9/30/16.
 */
+function() { 'use strict';
    var TEMPLATE_DIR = "/assets/templates/";
    var whack = angular.module('whack', [ 'ngRoute' ]);

    /**
     * output the absolute path of the template
     *
     * @param {string} htmlFile - the name of the template file
     * @return {string} - TEMPLATE_DIR + htmlFile + ".html"
     */
    function template ( htmlFile ) {
        return TEMPLATE_DIR + htmlFile + ".html";
    }

    whack.config(['$routeProvider', '$locationProvider',
        function ( $routeProvider, $locationProvider ) {
            $routeProvider
                .when('/', {
                   templateUrl: template("main"),
                   controller: "mainController"
               })
                .when('/play', {
                    templateUrl: template("game"),
                    controller: "gameController"
                })
                .when('/leaderboard', {
                    templateUrl: template("leaderboard"),
                    controller: "leadController"
                })
                .otherwise({
                    templateUrl: template("main"),
                    controller: "mainController"
                })
            ;
            // remove the hash from the route
            $locationProvider.html5Mode(true);

    }]);

    whack.controller('mainController',
        ['$scope', function ( $scope ) {

    }]);

    whack.controller('gameController', ['$http', '$scope',
        function( $http, $scope ){

    }]);

    whack.controller('leadController', ['$http', '$scope',
        function( $http, $scope ){

    }])
}();
