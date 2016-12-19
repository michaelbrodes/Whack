<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12/18/16
 * Time: 9:30 PM
 */

namespace whack\data;
use \PDO;

class Account
{
    # private fields corresponding to the columns in the database
    private $name, $password, $nick, $id;

    /**
     * Takes in a username, password, and nickname and creates a new entry into
     * Account table. It returns the object version of the Account it just
     * created
     *
     * @param string $usr
     * @param string $pwd
     * @param string $nick
     * @return Account
     */
    public static function create ( string $usr, string $pwd, string $nick = "" ) : Account
    {
        return null;
    }

    /**
     * Checks if the user with the corresponding username exists in the database
     *
     * @param string $usr - the username we are checking
     * @return bool - whether the user exists or not
     */
    public static function check_existence (string $usr): bool
    {
        $db = WhackDB::getInstance()->getPDO();
        $exist_sql = "SELECT name FROM Account WHERE name = :username";

        $exist_stmt = $db->prepare($exist_sql);
        $exist_stmt->execute(array( ":username" => $usr ));
        $usr_matches = $exist_stmt->fetchAll(PDO::FETCH_ASSOC);
        $exist = count($usr_matches) > 0;

        return $exist;
    }
}