<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12/23/16
 * Time: 12:58 AM
 *
 * Create a new admin
 */
namespace whack\admin;
use whack\data\Account;

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require 'admin.inc.php';
session_start();

if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
{
    header('Location: /#/error/file-not-found');
}

if (isset($_POST['nonce']) && verify_nonce($_POST['nonce'], $_SESSION))
{
    # the id of the admin making the other admin is stored at the front of the
    # nonce
    $aid = explode(':', $_POST['nonce'])[0];
    $admin = Account::getUserById($aid);

    $new = Account::getUser($_POST['admin']);

    if ( password_verify($_POST['password'], $admin->password) &&
         $admin->admin )
    {
        $new->makeAdmin();
        header('Location: /whack/admin/');
    }
    else
    {
        header('Location: /#/error/forbidden');
    }

}
else
{
    header('Location: /#/error/forbidden');
}
