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
    bad_input("I need all the fields filled out");
}

$usr = $post['new-usr'];
$pwd = $post['new-pass'];

# nicks are not needed so we can default to an empty string if not supplied
$nick = isset($post['nick'])? $post["nick"] : "";

# checking user input
if ( !checkname($usr) )
{
    bad_input("The username you inputted is invalid.");
}
else if ( !checkpass($pwd, $post['conf-pass']) )
{
    bad_input("The password you inputted is invalid.");
}
else if ( Account::check_existence($usr) !== null )
{
    bad_input("The username inputted already exists.");
}

$new_account = Account::create($usr, $pwd, $nick);

if ( $new_account === null )
{
    http_response_code(500);
    header("Content-Type: text/plain");
    echo "There is a problem with inserting your account into the database. Please try again.";
    die();
}

save_user($new_account->nick, $new_account->id, $_SESSION);
