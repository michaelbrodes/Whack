<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12/16/16
 * Time: 5:20 PM
 */
namespace whack\management;
use whack\data\Account;
session_start();
require_once 'management.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

if ( $_SERVER["REQUEST_METHOD"] !== "POST" )
{
    header('Location: /');
    die();
}

# decoded the posted json into an associative array
$post = json_decode(file_get_contents("php://input"), true);

if ( !array_key_exists('user', $post) || !array_key_exists('password', $post))
{
    echo "bro I need a username and a password";
    die();
}

$usr = $post['user'];
$pwd = $post['password'];

if ( !checkname($usr) )
{
    echo "Your inputted username doesn't correspond to my constraints";
    die();
}
else if ( !checkpass($pwd) )
{
    echo "Your inputted password doesn't correspond to my constraints";
    die();
}

// TODO: just use this generic error rather than the debug messages
$generic_err = "Your credentials where not determined to be authentic";

/*
check_existence returns false on failure (because pdostatement::fetch does), so
we not the assignment of $usr_account because the not-ed expression returns true
on failure. When the expression returns true, we echo out a failure message
*/
if ( ($usr_account = Account::check_existence($usr)) === null )
{
    echo "Username doesn't match";
    echo $generic_err;
    die();
}


if ( !password_verify($pwd, $usr_account->password) )
{
    echo "Password doesn't match";
    echo $generic_err;
    die();
}

$_SESSION['usr-id'] = $usr_account->id;
$_SESSION['nick']   = $usr_account->nick;
header('Content-Type: application/json');
echo json_encode([
    "nick" => $usr_account->nick,
    "id"   => $usr_account->id
]);
