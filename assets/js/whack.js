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

    /**
     * A Single character buffer to check whether a person failed the recent
     * character in the game
     * @constructor
     */
    function FailBuffer () {
        this.fail = "";
        // TODO: get apache to make this thing work
        this.buzzer = angular.element("#buzzer");
    }

    /**
     * Add a single character from the PhraseGame object into the fail buffer.
     * If there is already a character in the fail buffer do nothing. When
     * adding it deletes 1 character off the statement attribute of PhraseGame
     *
     * @param {PhraseGame} phrase
     */
    FailBuffer.prototype.add = function ( phrase ) {
        if ( this.fail.length < 1 && phrase.statement.length > 0 ) {
            this.fail = phrase.shift();
        }

        this.shake();
    };

    /**
     * Removes the character from the buffer and returns it
     * @returns {string} the character we just removed
     */
    FailBuffer.prototype.remove = function () {
        var toRemove = this.fail;
        this.fail = "";

        return toRemove;
    };
    /**
     * Whether the buffer is full
     * @returns {boolean}
     */
    FailBuffer.prototype.isFailing = function () {
        return this.fail.length === 1;
    };

    /**
     * checks if the character in the buffer is equal to the one found in the
     * keypress event
     *
     */
    FailBuffer.prototype.checkChar = function( event ) {
        return event.charCode === this.fail.charCodeAt(0);
    };

    /**
     * Shakes the text that represents the FailBuffer in the DOM
     */
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
         */
        PhraseGame.prototype.start = function () {
            var self = this;
            $http.get('/whack/phrases/get_phrase.php').then(function successCallback(res) {

                self.registerGameParams(res.data);
                self.timer = Date.now();
                self.length = self.statement.length;

            }, function errorCallback(res) {

                $log.error(res);

            });
        };

        /**
         * Checks whether the character code of a keypress event is equal to the
         * character code of the first character in the PhraseGame object
         * @param event
         * @returns {boolean}
         */
        PhraseGame.prototype.checkChar = function( event ) {
            console.log(event.charCode);
            console.log(this.statement.charCodeAt(0));
            return event.charCode === this.statement.charCodeAt(0);
        };

        /**
         * Shifts one character off the statement string.
         *
         * @return {String} the first character from statement
         */
        PhraseGame.prototype.shift = function () {
            // splits the statement into an array of individual characters
            var parts = this.statement.split('');
            var char = parts.shift();
            this.statement = parts.join("");

            return char;
        };

        /**
         * Adds one character back onto the string.
         */
        PhraseGame.prototype.unshift = function (character) {
            var parts = this.statement.split('');
            parts.unshift(character);
            this.statement = parts.join('');
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
        $scope.phrase = PhraseGame;
        $scope.failBuffer = new FailBuffer();
        PhraseGame.start();


        $document.keypress(function (event) {
            $scope.$apply(function () {
                if ( PhraseGame.checkChar(event) &&
                    !$scope.failBuffer.isFailing() ) {
                    PhraseGame.shift();
                }
                else if ( $scope.failBuffer.isFailing() &&
                    $scope.failBuffer.checkChar(event)) {
                    PhraseGame.unshift($scope.failBuffer.remove());
                }
                else {
                    $scope.failBuffer.add(PhraseGame);
                }
            });
        });

    }]);

    whack.controller('leadController', ['$http', '$scope', 'PhraseGame',
        function( $http, $scope, PhraseGame ){

    }])
}();
