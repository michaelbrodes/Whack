<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12/16/16
 * Time: 5:20 PM
 */
namespace whack\management;
use whack\data\WhackDB;
use whack\data\Account;
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once 'management.inc.php';

if ( $_SERVER["REQUEST_METHOD"] !== "POST" )
{
    header("Location: /");
    die();
}

# decode the json string sent by angular into an associative array
$post = json_decode(file_get_contents("php://input"), true);

# these three keys are all that are needed to preform a creation action
if ( !array_key_exists('new-usr', $post) ||
    !array_key_exists('new-pass', $post) ||
    !array_key_exists('conf-pass', $post) )
{
    # TODO: give the user an error
    echo "I need a valid password and username, homie";
    die();
}

$usr = $post['new-usr'];
$pwd = $post['new-pass'];

# test if the new password is the same as the confirmed password, and throw an
# error if it isn't

# nicks are not needed so we can default to an empty string if not supplied
$nick = isset($post['nick'])? $post["nick"] : "";

# checking user input
#TODO give the user an error
if ( !checkname($usr) )
{
    echo "I need a valid username " . $usr;
    die();
}
else if ( !checkpass($pwd, $post['conf-pass']) )
{
    echo "I need a valid password " . $pwd . " " . $post['conf-pass'];
    die();
}
else if ( Account::check_existence($usr) !== null )
{
    echo "I have a duplicate name buddy!";
    die();
}

$new_account = Account::create($usr, $pwd, $nick);

if ( $new_account === null )
{
    echo "I had an error inserting the management you just created into the database";
    die();
}

# send back id as a handler for the management.
$_SESSION['usr-id'] = $new_account->id;
$_SESSION['nick'] = $new_account->nick;
header('Content-Type: application/json');
echo json_encode([
    'id' => $new_account->id,
    'nick' => $new_account->nick
]);
