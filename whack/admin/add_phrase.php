<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12/23/16
 * Time: 12:39 AM
 */
namespace whack\admin;
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require 'admin.inc.php';
use whack\data\Phrase;
session_start();

if ( $_SERVER['REQUEST_METHOD'] !== "POST" )
{
    header('Location: /#/error/file-not-found');
}


if (isset($_POST['nonce']) && verify_nonce($_POST['nonce'], $_SESSION))
{
    $new = Phrase::create(
        $_POST['author'],
        $_POST['phrase-content'],
        $_POST['origin']
    );

    if ( !$new )
    {
        header('Location: /#/error/bad-response');
    }

    if ( $_FILES['image']['tmp_name'] )
    {
        $new->upload_image($_FILES['image']);
    }

    header('Location: /whack/admin/');
}
else
{
    header('Location: /#/error/unauthorized');
}
