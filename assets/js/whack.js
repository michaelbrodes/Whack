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

    /**
     * Jquery callback for registering a keypress on document
     * @param e - the jquery event object for keypress
     * @param $scope - the angular scope object
     */
    function registerPress( e, $scope ) {
        // shorthand names
        var phrase = $scope.phrase;
        var suc = $scope.success;
        var fail = $scope.failure;
        var inputChar = String.fromCharCode(e.which);

        if ( phrase[0] === inputChar && fail.length === 0 )
        {
            // add the beginning of phrase to success' end.
            suc = suc.concat(phrase[0]);
            phrase = phrase.substr(1, phrase.length);
            fail = "";
        }
        else if ( inputChar === fail[0] )
        {
            // pop the failed character into the success string
            suc = suc.concat(fail[0]);
            fail = "";
        }
        else if ( fail.length === 0 )
        {
            // take the end of phrase and append to fail
            fail = fail.concat(phrase[0]);
            phrase = phrase.substr(1, phrase.length);
        }

        $scope.phrase = phrase;
        $scope.success = suc;
        $scope.failure = fail;
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

    }]);

    whack.controller('mainController',
        ['$scope', function ( $scope ) {

    }]);

    whack.controller('gameController', ['$http', '$scope', '$location', function(
        $http, $scope, $location
    ){
        // initial DOM values
        $scope.success = "";
        $scope.failure = "";
        $scope.phrase = "Hello World";

        var $doc = angular.element(document);
        var lengthToWin = $scope.phrase.length;

        $doc.keypress(function ( e ) {
            $scope.$apply(function() {
                registerPress(e, $scope);
            });

            if (lengthToWin === $scope.success.length)
            {
                $scope.$apply(function () {
                    $location.path("/leaderboard");
                });
            }
        });
    }]);

    whack.controller('leadController', ['$http', '$scope',
        function( $http, $scope ){

    }])
}();
