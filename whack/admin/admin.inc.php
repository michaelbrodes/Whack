<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12/23/16
 * Time: 1:27 AM
 */
namespace whack\admin;
use whack\data\WhackDB;
use whack\data\Account;
use \PDO;

/**
 * Returns an array of usernames that correspond to user's who don't have admin
 * privileges
 * @return array
 */
function list_non_admins(): array
{
    $pdo = WhackDB::getInstance()->getPDO();
    # by default results are passed as an associated array with name as key
    $users = $pdo->query(
        "SELECT name FROM whack.Account WHERE admin = 0"
    );
    # array to remove the associative array
    $names = [];

    while ( $r = $users->fetch(PDO::FETCH_ASSOC) )
    {
        array_push($names, $r['name']);
    }

    return $names;
}

/**
 * Generate a one time nonce in order to validate that a user request isn't
 * a CSRF
 * @param Account $user
 * @param $session
 * @return string
 */
function gen_nonce ( Account $user, array &$session) : string
{
    $token = $user->storeToken();
    if ( empty($session['nonces']) )
    {
        $session['nonces'] = [];
    }
    array_push($session['nonces'], $token);
    return $token;
}

/**
 * Verify that the nonce is correct. If it is we remove the nonce from the
 * database and session.
 * @param string $nonce - the nonce given by the client
 * @param int $uid - the id of the user that submitted the form
 * @param array $session -
 * @return bool
 */
function verify_nonce ( string $nonce, int $uid, array &$session ) : bool
{
    $user = Account::getUserById($uid);

    if ( isset($session['nonces']) )
    {
        $found = array_search($nonce, $session['nonces']);
        unset($session['nonces'][$found]);

        # array_search returns false when it doesn't find something otherwise it
        # returns an index, if an index is returned then we're a valid nonce
        $valid = is_bool($found)? $found: true;
    }
    else
    {
        $token = $user->getToken();

        # if a token was returned we set valid to whether it is equal to the
        # nonce
        $valid = ($token)? $token === $nonce: false;
    }

    # nonces are one time use only. If the user's nonce is failed then still
    # want their token to exist.
    if ( $valid ) $user->removeToken();

    return $valid;
}
