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
    bad_input("user and password fields are empty");
}

$usr = $post['user'];
$pwd = $post['password'];

if ( !checkname($usr) || !checkpass($pwd) )
{
    unauthorized();
}
/*
check_existence returns false on failure (because pdostatement::fetch does), so
we not the assignment of $usr_account because the not-ed expression returns true
on failure. When the expression returns true, we echo out a failure message
*/
if ( ($usr_account = Account::check_existence($usr)) === null )
{
    unauthorized();
}


if ( !password_verify($pwd, $usr_account->password) )
{
    unauthorized();
}

save_user($usr_account->nick, $usr_account->id, $_SESSION);
