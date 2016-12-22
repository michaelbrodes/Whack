<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12/19/16
 * Time: 3:35 PM
 */
/**
 * let's the application know that a user is logged in.
 */
namespace whack\management;
use whack\data\Account;
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
session_start();

// default for an invalid user
$account_info = [
    "nick" => "",
    "id" => -1
];

// account exists on server, so we can just echo what's stored
if ( array_key_exists('nick', $_SESSION) &&
     array_key_exists('usr-id', $_SESSION) )
{
    $account_info['nick'] = $_SESSION['nick'];
    $account_info['id']   = $_SESSION['usr-id'];
}
// the account is stored client side so we need to check if it's legit
else if ( isset($_COOKIE['remember']) &&
    Account::verifyCookie($_COOKIE['remember']))
{
    $id = explode(':', $_COOKIE['remember'])[0];
    $account_info['id'] = $_SESSION['usr-id'] = $id;
    $account_info['nick'] = $_SESSION['nick'] = Account::nabNick($id);
}

header('Content-Type": application/json');
echo json_encode($account_info);
