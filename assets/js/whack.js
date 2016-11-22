/**
 * Created by michael on 9/30/16.
 */
+function() { 'use strict';
    /**
     * output the absolute path of the template
     *
     * @param {string} htmlFile - the name of the template file
     * @return {string} - TEMPLATE_DIR + htmlFile + ".html"
     */
    function template ( htmlFile ) {
        return TEMPLATE_DIR + htmlFile + ".html";
    }

    function FailBuffer () {
        this.fail = "";
        this.buzzer = angular.element("#buzzer");
    }

    /**
     * Add a single character from the PhraseGame object into the fail buffer.
     * If there is already a character in the fail buffer do nothing. When
     * adding it deletes 1 character off the statement attribute of PhraseGame
     *
     * @param {PhraseGame} phrase
     * @param $scope
     */
    FailBuffer.prototype.add = function ( phrase, $scope ) {
        if ( this.fail.length < 1 && phrase.statement.length > 0 ) {
            this.fail = phrase.shift();
        }

        $scope.fail = this.fail;
    };

    FailBuffer.prototype.shake = function () {
        angular.element("#fail").effect("shake", {
            direction: "up",
            distance: 20,
            times: 1
        }, 200);
    };

    var TEMPLATE_DIR = "/assets/templates/";
    var whack = angular.module('whack', [ 'ngRoute' ]);

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

    whack.factory('PhraseGame', ['$http', function ($http) {
        /**
         * Angular Service representing a phrase object from the backend.
         * @constructor
         */
        function PhraseGame () {
            // init values
            this.statement = "";
            this.imagePath = "";
            this.author = "";
            this.origin = "";
            // -1 for a the check of whether the complete buffer is equal to the
            // length of the phrase buffer.
            this.length = -1;
            this.timer = 0;
        }

        /**
         * Register an object that represents phrase in the view to this object.
         *
         * @param {{phrase: *, path: *, author: *, origin: *}} phraseArray
         */
        PhraseGame.prototype.registerGameParams = function(phraseArray) {
            this.statement = phraseArray['statement'];
            this.imagePath = phraseArray['imagePath'];
            this.author = phraseArray['author'];
            this.origin = phraseArray['origin'];
        };

        /**
         * Return the object representing a phrase in the view of the application.
         * it should be attached to $scope.
         *
         * @returns {{phrase: *, path: *, author: *, origin: *}}
         */
        PhraseGame.prototype.getGameParams = function () {
            return {
                statement: this.statement,
                imagePath: this.imagePath,
                author: this.author,
                origin: this.origin
            };
        };

        /**
         * Initializes the game by setting all the phrase DOM elements to the
         * values from a backend Phrase object, and it starts the timer.
         *
         * @param $scope: the scope of a controller
         */
        PhraseGame.prototype.start = function ( $scope ) {
            var self = this;
            $http.get('/whack/phrases/get_phrase.php').then(function successCallback(res) {

                self.registerGameParams(res.data);
                self.timer = Date.now();
                self.length = self.statement.length;
                $scope.phrase = self.getGameParams();

            }, function errorCallback(res) {

                $log.error(res);

            });
        };

        /**
         * Shifts one character off the statement string.
         *
         * @return {String} the first character from statement
         */
        PhraseGame.prototype.shift = function () {
            // splits the statement into an array of individual characters
            var parts = this.statement.split();
            var char = parts.shift();
            this.statement = parts;

            return char;
        };

        /**
         * Adds one character back onto the string.
         */
        PhraseGame.prototype.unshift = function (character) {

        };

        return new PhraseGame();
    }]);

    whack.controller('mainController',
        ['$scope', function ( $scope ) {

    }]);

    whack.controller('gameController',
        ['$scope', '$location', '$log', '$document', 'PhraseGame',
            function( $scope, $location, $log, $document, PhraseGame ){
        // initial DOM value
        $scope.phrase = PhraseGame.getGameParams();
        $scope.failBuffer = new FailBuffer();
        PhraseGame.start($scope);


        $document.keypress(function (charCode) {
        });

    }]);

    whack.controller('leadController', ['$http', '$scope', 'PhraseGame',
        function( $http, $scope, PhraseGame ){

    }])
}();
