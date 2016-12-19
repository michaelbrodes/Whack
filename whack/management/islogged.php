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
session_start();

$account_info = [
    "nick" => "",
    "id" => -1
];

if ( array_key_exists('nick', $_SESSION) &&
     array_key_exists('usr-id', $_SESSION) )
{
    $account_info['nick'] = $_SESSION['nick'];
    $account_info['id']   = $_SESSION['usr-id'];
}

header('Content-Type": application/json');
echo json_encode($account_info);
