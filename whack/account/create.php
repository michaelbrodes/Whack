<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12/16/16
 * Time: 5:20 PM
 */
namespace whack\account;
use whack\data\WhackDB;
use whack\data\Account;

if ( $_SERVER["REQUEST_METHOD"] !== "POST" )
{
    header("Location: /");
    die();
}

