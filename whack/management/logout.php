<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12/19/16
 * Time: 11:33 PM
 *
 * Unsets all the session storage about the user.
 */
session_start();

if ( array_key_exists('usr-id', $_SESSION) &&
     array_key_exists('nick', $_SESSION) )
{
    unset($_SESSION['usr-id']);
    unset($_SESSION['nick']);
}

if ( isset($_COOKIE['remember']))
{
    // empties out the cookie
    setcookie('remember');
}

header("Location: /");
