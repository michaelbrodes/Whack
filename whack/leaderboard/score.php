<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12/22/16
 * Time: 12:06 AM
 */
namespace whack\leaderboard;
require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";
require 'board.inc.php';

if ( $_SERVER['REQUEST_METHOD'] !== "POST" )
{
    http_response_code(404);
    header("Location: /#/error/file-not-found");
    die();
}

$post = json_decode(file_get_contents("php://input"), true);

if ( array_key_exists('identifier', $post) &&
     array_key_exists('Phrase_id', $post) &&
     array_key_exists('wpm', $post) &&
     array_key_exists('accuracy', $post) )
{
    reg_score(
        $post['Phrase_id'],
        $post['identifier'],
        $post['wpm'],
        $post['accuracy']
    );
}
else
{
    http_response_code(400);
    echo "problems posting score<br/>";
    var_dump($post);
}
