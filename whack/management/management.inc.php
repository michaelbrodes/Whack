<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12/19/16
 * Time: 10:20 PM
 */
namespace whack\management;
use whack\data\Account;

/**
 * checks if the name supplied follows our username guidelines (this is just a
 * copy of the front-end work since we can't always trust users)
 * @param string $name - the username provided
 * @return bool
 */
function checkname ( string $name ) : bool
{
    # constraints
    $space = "/\s/";
    $unicode = "/^([\x01-\x7F]|([\xC2-\xDF]|\xE0[\xA0-\xBF]|\xED[\x80-\x9F]|(|[\xE1-\xEC]|[\xEE-\xEF]|\xF0[\x90-\xBF]|\xF4[\x80-\x8F]|[\xF1-\xF3][\x80-\xBF])[\x80-\xBF])[\x80-\xBF])*$/";
    $maxLength = 30;

    $nospace = !(bool)preg_match($space, $name);
    $only_uni = (bool)preg_match($unicode, $name);

    return strlen($name) <= $maxLength && $nospace && $only_uni;

}

/**
 * Checks if the $pwd conforms to the constraints we provided, and that the
 * $conf matches it.
 * @param string $pwd
 * @param string [$conf] - optional confirmed password
 * @return bool
 */
function checkpass ( string $pwd, string $conf = null ) : bool
{
    $min_length = 8;
    $unicode = "/^([\x01-\x7F]|([\xC2-\xDF]|\xE0[\xA0-\xBF]|\xED[\x80-\x9F]|(|[\xE1-\xEC]|[\xEE-\xEF]|\xF0[\x90-\xBF]|\xF4[\x80-\x8F]|[\xF1-\xF3][\x80-\xBF])[\x80-\xBF])[\x80-\xBF])*$/";

    $only_uni = (bool)preg_match($unicode, $pwd);
    # test if the confirmed password matches the actual password only if the
    # $conf is defined.
    $password_match = ($conf === null)? true: $conf === $pwd;

    return $password_match && $only_uni && strlen($pwd) >= $min_length;
}

/**
 * Sends back a 401 response code if the user failed to login correctly
 *
 * @param string [$message] - the message to send back with response
 */
function unauthorized( string $message =
                       "Your credentials where not determined to be authentic")
{
    http_response_code(401);
    header('Content-Type: text/plain');
    echo $message;
    die();
}

/**
 * The user inputted bad information into a form and now we have to send a 400
 * error telling them what happend.
 * @param string $message - the message we want to send back with the response
 */
function bad_input ( string $message )
{
    http_response_code(400);
    header('Content-Type: text/plain');
    echo $message;
    die();
}

/**
 * If the user does a get request on a post only script, we throw a file not
 * found response code.
 */
function invalid_request ()
{
    http_response_code(404);
    header('Location: /#/error/file-not-found');
    die();
}

/**
 * Save the user into session storage, because they just logged on
 *
 * @param Account $account - the account to store
 * @param array $session - the reference to the script's $_SESSION array
 * @param bool $to_rem - whether we should remember the user via a cookie or not
 */
function save_user ( Account $account, array &$session, bool $to_rem )
{
    # store user into cookie
    $token = $account->storeToken();
    $identifier = $account->genCookie($token);
    if ( $to_rem )
    {
        // make the cookie expire a month from now
        $to_expire = mktime(0, 0, 0, date('M') + 1);
        setcookie("remember", $identifier, $to_expire);
    }
    # store user into session
    $session['usr-id'] = $identifier;
    $session['nick']   = $account->nick;

    header('Content-Type: application/json');
    echo json_encode([
        "nick" => $account->nick,
        "id"   => $identifier
    ]);
}
