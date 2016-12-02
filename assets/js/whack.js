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
     * The keyboard generated in the view
     */

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
            if ( phrase.statement[0] === " " ) {
                this.fail = "_";
                phrase.shift();
            } else {
                this.fail = phrase.shift();
            }
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

        return (toRemove === "_")? " " : toRemove;
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
     * keypress event.
     *
     */
    FailBuffer.prototype.checkChar = function( event ) {
        // charcode for space
        var space = 32;
        var isSpace = event.charCode === space && this.fail === "_";
        var isSame = event.charCode === this.fail.charCodeAt(0) &&
            this.fail !== "_";

        return isSpace || isSame;
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

    /**
     * Filters out either the lowercase or uppercase portion of a key
     */
    whack.filter('case', function () {
        return function (inKey, isUpper) {
            var divider = /\^(.+)?/g;
            if ( isUpper ) {
                // the divider is a carrot character.
                return inKey.split(divider)[1] || "";
            }
            else {
                // the divider is a carrot character.
                return inKey.split(divider)[0];
            }
        }
    });

    whack.factory('PhraseGame', ['$http', '$location', 'caseFilter',
        function ($http, $location, caseFilter) {
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
            this.startTime = 0;
            this.characters = 0;
            this.finalWPM = 0;
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
         * values from a backend Phrase object.
         */
        PhraseGame.prototype.start = function () {
            var self = this;
            $http.get('/whack/phrases/get_phrase.php').then(function successCallback(res) {

                self.registerGameParams(res.data);
                self.startTime = Date.now()/1000;
                self.characters = 0;
                self.length = self.statement.length;

            }, function errorCallback(res) {

                console.error(res);

            });
        };

        /**
         * Checks whether the character code of a keypress event is equal to the
         * character code of the first character in the PhraseGame object
         * @param event
         * @returns {boolean}
         */
        PhraseGame.prototype.checkChar = function( event ) {
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

        /**
         * Checks if the complete string from the scope is of the same length as
         * this phrase's length. If it is calculate the finish and redirect to
         * the leader board.
         *
         * @param {string} complete
         */
        PhraseGame.prototype.checkCompletetion = function(complete) {
            if ( complete.length === this.length ) {
                this.finalWPM = this.wpm();
                $location.path('/leaderboard');
            }
        };

            /**
             * Calculates the total wpm from the time of the first press to now.
             * A word in this case would be considered every 5 characters.
             *
             * @returns {number} - the total wpm
             */
        PhraseGame.prototype.wpm = function () {
            // Date.now returns milliseconds so we need to convert to seconds
            var time = Date.now()/1000;
            // total time elasped in seconds
            var diff = Math.abs(time - this.startTime);

            // for every five characters calculate the words per minute
            return (this.characters/7)/(diff/60);
        };

        /**
         * Tells whether the character in key is the first character in the
         * phrase statement
         * @param {string} key - a key from the view
         * @returns {boolean}
         */
        PhraseGame.prototype.peek = function ( key ) {
            return this.statement[0].toLowerCase() === caseFilter(key, false) ||
                    this.statement[0] === caseFilter(key, true) ||
                    (this.statement.charCodeAt(0) === 32 && key === "Space");
        };

        return new PhraseGame();
    }]);

    /**
     * grabs the configuration of the user via external http requests
     */
    whack.service('Config', ['$http', '$log', function ($http) {
        this.keyboard = [];
        var self = this;

        $http.get('/whack/phrases/get_keyboard.php').then(function success( res ) {
            // two dimensional array showing the rows and keys of a keyboard
            self.keyboard = res.data;
        }, function fail( res ) {
            $log.error(res);
        });
    }]);


    whack.controller('mainController',
        ['Config', function ( Config ) {
        $scope.config = Config;
    }]);

    whack.controller('gameController',
        ['$scope', '$log', '$document', 'PhraseGame', 'Config',
            function( $scope, $log, $document, PhraseGame, Config ){
        // initial DOM value
        $scope.phrase = PhraseGame;
        $scope.failBuffer = new FailBuffer();
        $scope.complete = "";
        $scope.wpm = 0;
        PhraseGame.start();
        $scope.config = Config;

        $document.keypress(function (event) {

            $scope.$apply(function () {
                // if the characters match and we are not in a failing state
                if ( PhraseGame.checkChar(event) &&
                    !$scope.failBuffer.isFailing() ) {
                    // just add to the complete string
                    PhraseGame.characters++;
                    $scope.complete += PhraseGame.shift();
                }
                // if we are in a failing state, but are character matches the
                // failed character
                else if ( $scope.failBuffer.isFailing() &&
                    $scope.failBuffer.checkChar(event)) {
                    PhraseGame.unshift($scope.failBuffer.remove());
                    PhraseGame.characters++;
                    $scope.complete += PhraseGame.shift();
                }
                // otherwise we failed.
                else {
                    $scope.failBuffer.add(PhraseGame);
                }

                $scope.wpm = PhraseGame.wpm();
                PhraseGame.checkCompletetion($scope.complete);
            });
        });

    }]);

    whack.controller('leadController', ['$http', '$scope', '$document',
        '$location', 'PhraseGame',
        function( $http, $scope, $document, $location, PhraseGame ){
        $document.keypress(function ( event ) {
            if (event.keyCode === 32) {
                $scope.$apply(function () {
                    $location.path('/play');
                });
            }
        })
    }])
}();
