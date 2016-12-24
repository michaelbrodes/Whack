/**
 * Created by michael on 12/23/16.
 */
+function () {
    var account = angular.module('account', [ ]);

    /**
     * Uses angular.element (alias to JQuery) to display information, that
     * should only be displayed to users on, the navbar
     * @param {string} nick - the user's nickname
     */
    function displayUserInfo ( nick ) {
        angular.element(".account-modal").modal("hide");
        angular.element("#play-button > a").text('Play');
        angular.element('#logout > a').text('Logout');
        angular.element('#login > span.center').text("Hello " + nick);

        angular.element('#links > li').each(function () {
            angular.element(this).css('display', 'inline-block');
        });
    }

    /**
     * The management of the current user.
     */
    account.factory('Account', ['$http', '$log', function ( $http ) {
        /**
         * Angular service with management capabilities brought from the backend
         * @constructor
         */
        function Account () {
            // initial values of attributes ( the user is not considered logged
            // in)
            this.logged = false;
            // invalid database id
            this.INVALID_ID = -1;
            this.id = this.INVALID_ID;
            this.nick = "";
            this.loading = false;
            this.isAdmin = false;
            // an error coming from the backend; placed during ajax call
            this.backendErr = "";
        }

        /**
         * load in a account of the user into the provided Account object. This
         * is done using the response object coming from the backend. This is
         * usually provided as a callback to a promise's fulfillment method
         *
         * @param {object} res - the backend's response
         * @param {Account} account - The account we are loading into
         */
        Account.prototype.loadAccount = function ( res, account ) {
            account.id = res.data.id;
            account.isAdmin = res.data.admin;
            account.logged = true;
            account.loading = false;
            account.nick = res.data.nick;

            displayUserInfo(account.nick);
        };

        /**
         * Logs in the user with the specified username and password.
         *
         * @param {string} usr
         * @param {string} pass
         * @param {boolean} toRem
         */
        Account.prototype.login = function ( usr, pass, toRem ) {
            this.loading = true;
            this.backendErr = "";
            var self = this;

            $http.post('/whack/management/login.php', {
                "user": usr,
                "password": pass,
                "to-rem": toRem
            }).then(function ( res ) {
                self.loadAccount(res, self);
            }, function fail ( res ) {
                self.backendErr += res.data + " [" + res.status + "]";
                self.loading = false;
            });
        };

        /**
         * creates a user management using the backend
         * @param usr - new username
         * @param pass - new password
         * @param conf - confirmation of that new password
         * @param nick - nickname for the user
         * @param {boolean} toRem - whether the the user should remembered via
         *                          cookie
         */
        Account.prototype.create = function ( usr, pass, conf, nick, toRem ) {
            this.loading = true;
            this.backendErr = "";
            var self = this;

            $http.post('/whack/management/create.php', {
                "new-usr": usr,
                "new-pass": pass,
                nick: nick,
                "conf-pass": conf,
                "to-rem": toRem
            }).then(function ( res ) {
                self.loadAccount(res, self);
            }, function fail ( res )  {
                self.backendErr += res.data + " [" + res.status + "]";
                self.loading = false;
            });
        };

        /**
         * Tests whether the username has no spaces, has only unicode
         * characters, and is no longer than 30 characters in length.
         *
         * @param {string} usr
         * @returns {boolean}
         */
        Account.prototype.validName = function ( usr ) {
            // right now I am only testing for spaces
            var notUser = /\s/g;
            // matches only valid utf8 bytes
            var unicode = /^([\x00-\x7F]|([\xC2-\xDF]|\xE0[\xA0-\xBF]|\xED[\x80-\x9F]|(|[\xE1-\xEC]|[\xEE-\xEF]|\xF0[\x90-\xBF]|\xF4[\x80-\x8F]|[\xF1-\xF3][\x80-\xBF])[\x80-\xBF])[\x80-\xBF])*$/g;
            // needs to be less than 30 bytes
            var maxLength = 30;

            return !notUser.test(usr) && unicode.test(usr) &&
                usr.length <= maxLength;
        };

        /**
         * Checks whether the the password is greater than or equal to 8
         * characters in length and is valid unicode.
         * @param {string} pass
         * @returns {boolean}
         */
        Account.prototype.validPass = function ( pass ) {
            var minLength = 8;
            var unicode = /^([\x00-\x7F]|([\xC2-\xDF]|\xE0[\xA0-\xBF]|\xED[\x80-\x9F]|(|[\xE1-\xEC]|[\xEE-\xEF]|\xF0[\x90-\xBF]|\xF4[\x80-\x8F]|[\xF1-\xF3][\x80-\xBF])[\x80-\xBF])[\x80-\xBF])*$/g;

            return pass.length >= minLength && unicode.test(pass);
        };

        /**
         * produces error message based on how the user input is invalid
         * @param {string} usr - the username provided by the user
         * @param {string} pwd - the password provided by the user
         * @param {string} [confPass] - optional param to be checked against pwd
         */
        Account.prototype.errMessage = function ( usr, pwd, confPass ) {
            var maxPass = 8;
            var maxUser = 30;
            var message = "";
            // the default value of confPass is an empty string.
            confPass = confPass || "";

            // don't annoy the user if their password is empty
            if ( usr === '' && pwd === '')
            {
                return message;
            }
            else
            {
                // actual error checks
                if ( pwd.length < maxPass )
                {
                    message += "Password needs to be more than 7 chars long. ";
                }

                if ( usr.length > maxUser )
                {
                    message += "User name needs to be less than 30 chars long. ";
                }

                if ( /\s/g.test(usr) )
                {
                    message += "There can be no spaces in your username. ";
                }

                // if confPass is an empty string then we may be on log in.
                if ( confPass !== pwd && confPass !== "" )
                {
                    message += "The password fields don't match."
                }

                if ( this.backendErr )
                {
                    message += this.backendErr;
                }
            }

            return message;
        };

        return new Account();
    }]);
}();
