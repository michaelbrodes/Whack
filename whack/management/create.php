<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12/16/16
 * Time: 5:20 PM
 */
namespace whack\management;
use whack\data\WhackDB;
use whack\data\Account;
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

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
 * @param string $conf
 * @return bool
 */
function checkpass ( string $pwd, string $conf ) : bool
{
    $min_length = 8;
    $unicode = "/^([\x01-\x7F]|([\xC2-\xDF]|\xE0[\xA0-\xBF]|\xED[\x80-\x9F]|(|[\xE1-\xEC]|[\xEE-\xEF]|\xF0[\x90-\xBF]|\xF4[\x80-\x8F]|[\xF1-\xF3][\x80-\xBF])[\x80-\xBF])[\x80-\xBF])*$/";

    $only_uni = (bool)preg_match($unicode, $pwd);
    return  $pwd === $conf && $only_uni && strlen($pwd) >= $min_length;
}

if ( $_SERVER["REQUEST_METHOD"] !== "POST" )
{
    header("Location: /");
    die();
}

# decode the json string sent by angular into an associative array
$post = json_decode(file_get_contents("php://input"), true);

# these three keys are all that are needed to preform a creation action
if ( !array_key_exists('new-usr', $post) ||
    !array_key_exists('new-pass', $post) ||
    !array_key_exists('conf-pass', $post) )
{
    var_dump($post);
    # TODO: give the user an error
    echo "I need a valid password and username, homie";
    die();
}

$usr = $post['new-usr'];
$pwd = $post['new-pass'];

# test if the new password is the same as the confirmed password, and throw an
# error if it isn't

# nicks are not needed so we can default to an empty string if not supplied
$nick = isset($post['nick'])? $post["nick"] : "";

# checking user input
#TODO give the user an error
if ( !checkname($usr) )
{
    echo "I need a valid username " . $usr;
    die();
}
else if ( !checkpass($pwd, $post['conf-pass']) )
{
    echo "I need a valid password " . $pwd . " " . $post['conf-pass'];
    die();
}
else if ( Account::check_existence($usr) )
{
    echo "I have a duplicate name buddy!";
    die();
}

$new_account = Account::create($usr, $pwd, $nick);

if ( $new_account === null )
{
    echo "I had an error inserting the management you just created into the database";
    die();
}

# send back id as a handler for the management.
$_SESSION['usr-id'] = $new_account->id;
header('Content-Type: application/json');
echo json_encode(['id' => $new_account->id]);
