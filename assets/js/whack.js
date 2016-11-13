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
        var inputChar = String.fromCharCode(e.which);

        if ( $scope.phrase[0] === inputChar && $scope.fail.length === 0 )
        {
            // add the beginning of phrase to suc' end.
            $scope.suc = $scope.suc + $scope.phrase[0];
            $scope.phrase = $scope.phrase.substr(1, $scope.phrase.length);
            $scope.fail = "";
        }
        else if ( inputChar === $scope.fail[0] && $scope.fail[0] !== "_" ||
            ($scope.fail[0] === "_" && inputChar === " ") )
        {
            // pop the failed character into the suc string
            $scope.suc = ($scope.fail === "_")? $scope.suc + " " : $scope.suc + $scope.fail[0];
            $scope.fail = "";
        }
        else if ( $scope.fail.length === 0 )
        {
            // we can't color code space
            $scope.fail = ($scope.phrase[0] === " ")? $scope.fail + "_": $scope.fail + $scope.phrase[0];
            $scope.phrase = $scope.phrase.substr(1, $scope.phrase.length);
            failShake();
        }
        else
        {
            failShake();
        }

    }

    /**
     * Shakes the fail span for 500ms at a time
     */
    function failShake () {
        angular.element("#fail").effect('shake', {
            direction: 'up',
            times: 2,
            distance: 5
        }, 100);
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
        $scope.suc = "";
        $scope.fail = "";
        $scope.phrase = "Hello World";

        var $doc = angular.element(document);
        var lengthToWin = $scope.phrase.length;

        $doc.keypress(function ( e ) {
            $scope.$apply(function() {
                registerPress(e, $scope);
            });

            if (lengthToWin === $scope.suc.length)
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
