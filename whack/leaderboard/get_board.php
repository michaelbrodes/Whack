<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12/22/16
 * Time: 12:06 AM
 */
namespace whack\leaderboard;
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require 'board.inc.php';


if ( $_SERVER['REQUEST_METHOD'] !== "GET" )
{
    // can't do anything besides get
    http_response_code('404');
    die();
}

if ( isset($_GET['phrase']) )
{
    header('Content-Type: application/json');
    echo json_encode(load_leaders((int)$_GET['phrase']));
}

