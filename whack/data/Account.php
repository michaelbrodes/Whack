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
    public $name, $password, $nick, $id;

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
        $new_account = new static();
        # running on a RPi so cost is low due to hardware constraints
        $new_account->password = password_hash(
            $pwd,
            PASSWORD_BCRYPT,
            [ "cost" => 8 ]
        );

        $new_account->nick = $nick;
        $new_account->name = $usr;

        # store the object
        $pdo = WhackDB::getInstance()->getPDO();
        $store_sql = "INSERT INTO 
                      whack.Account(name, password, nick) 
                      VALUES (:name, :pwd, :nick)";
        $store_acc = $pdo->prepare($store_sql);

        $worked = $store_acc->execute([
            ':name' => $new_account->name,
            ':pwd'  => $new_account->password,
            ':nick' => $new_account->nick
        ]);

        $new_account->id = $pdo->lastInsertId();

        if ( !$worked )
        {
            $new_account = null;
        }

        WhackDB::getInstance()->freePDO($pdo);

        return $new_account;
    }

    /**
     * Checks if the user with the corresponding username exists in the database
     *
     * @param string $usr - the username we are checking
     * @return Account - if the account exists then the function returns the
     *                   account, otherwise, it returns false.
     */
    public static function check_existence (string $usr): Account
    {
        $db = WhackDB::getInstance()->getPDO();
        $exist_sql = "SELECT * FROM whack.Account WHERE name = :username";

        $exist_stmt = $db->prepare($exist_sql);
        $exist_stmt->execute(array( ":username" => $usr ));
        $exist_stmt->setFetchMode(PDO::FETCH_CLASS, static::class);
        // there can only be one instance of a username in the database. So I am
        // just fetching the one.
        $usr_match = $exist_stmt->fetch();

        # if fetch returned false
        if ( !$usr_match )
        {
            $usr_match = null;
        }

        return $usr_match;
    }
}