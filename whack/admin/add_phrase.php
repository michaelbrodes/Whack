<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12/23/16
 * Time: 12:39 AM
 */
if ( $_SERVER['REQUEST_METHOD'] !== "POST" )
{
    header('Location: /#/error/file-not-found');
}


