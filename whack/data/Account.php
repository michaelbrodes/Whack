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
    public $name, $password, $nick, $id, $private_key, $admin;
    private $pdo;
    private static $TOKEN_SIZE = 128;

    public function __construct()
    {
        $this->pdo = WhackDB::getInstance()->getPDO();
        if ( isset($this->admin) )
        {
            $this->admin = (bool)$this->admin;
        }
    }

    /**
     * Creates a unique token for the user. Stored in the Token table. If there
     * is a chance that a user already has a token then removeToken should be 
     * called before this
     * @return string the token we just stored into the database
     */
    public function storeToken () : string
    {
        // if token is set to something other than empty string we remove it
        if ( $this->tokenExist() )
        {
            $this->removeToken();
        }

        $token = bin2hex (
            mcrypt_create_iv(static::$TOKEN_SIZE, MCRYPT_DEV_URANDOM)
        );

        $storesql = "INSERT INTO whack.Token(token, Account_id) VALUES (:token, :id)";
        $store_stmt = $this->pdo->prepare($storesql);
        $store_stmt->execute([ ":token" => $token, ":id" => $this->id ]);


        return $token;
    }

    /**
     * get a token that exists
     * @return string
     */
    public function getToken () : string
    {
        $token = $this->pdo->query(
            "SELECT token FROM whack.Token WHERE Account_id = $this->id"
        );

        if ( $result = $token->fetch(PDO::FETCH_ASSOC) )
        {
            return $result['token'];
        }
        else
        {
            # we didn't have a token stored so we should return an empty string
            return "";
        }
    }

    /**
     * Removes the old token entry for the user from the Token table. This
     * should only be used if the user's token has expired
     */
    public function removeToken ()
    {
        $removesql = "DELETE FROM whack.Token WHERE Account_id = :id";
        $rm_stmt = $this->pdo->prepare($removesql);
        $rm_stmt->execute(['id' => $this->id]);
    }

    /**
     * checks if a token exists for this object
     * @return bool
     */
    public function tokenExist () : bool
    {
        $token_sql = "SELECT * FROM whack.Token WHERE Account_id = $this->id";
        $token_stmt = $this->pdo->query($token_sql);
        return count($token_stmt->fetchAll(PDO::FETCH_NUM)) > 0;
    }

    /**
     * upload a new "remember" cookie for the user that is the id of the user, a
     * token associated with their account, and a hash of the previous two
     * fields as well as a private key of the user.
     *
     * @param string $token - the token we generated
     * @return string - the cookie we just generated.
     */
    public function genCookie ( string $token ) : string
    {
        # Thanks to the dude who decided to come up with this idea:
        # http://stackoverflow.com/a/17266448/2597280
        $cookie = $this->id . ":" . $token;
        # hash the public key the salt of the user's private key
        $public_key = hash_hmac('sha256', $cookie, $this->private_key);
        $cookie .= ':' . $public_key;

        return $cookie;
    }

    /**
     * Verifies if the stored cookie is legit
     *
     * @param string $cookie - the stored cookie, in the format:
     *                         "id:token:public_key"
     * @return bool whether the cookie is valid or not
     */
    public static function verifyCookie(string $cookie) : bool
    {
        list($id, $token, $public_key) = explode(':', $cookie);
        $secret_sql = "SELECT private_key FROM whack.Account WHERE id = :id";
        $pdo = WhackDB::getInstance()->getPDO();
        $sec_stmt = $pdo->prepare($secret_sql);
        $sec_stmt->execute([':id' => $id]);
        $private_key = $sec_stmt->fetch(PDO::FETCH_ASSOC)['private_key'];
        $user_hash = hash_hmac('sha256', $id . ":" . $token, $private_key);

        return hash_equals($public_key, $user_hash);
    }

    /**
     * Get the nickname for the user with the specified id
     * @param int $id - the user's id
     * @return string - the user's nickname
     */
    public static function nabNick ( int $id )
    {
        $nicksql = "SELECT nick FROM whack.Account WHERE id = :id";
        $pdo = WhackDB::getInstance()->getPDO();
        $nick_stmt  = $pdo->prepare($nicksql);
        $nick_stmt->execute([':id' => $id]);
        WhackDB::getInstance()->freePDO($pdo);

        return $nick_stmt->fetch(PDO::FETCH_ASSOC)['nick'];
    }

    /**
     * Takes in a username, password, and nickname and creates a new entry into
     * Account table. It returns the object version of the Account it just
     * created
     *
     * @param string $usr
     * @param string $pwd
     * @param string $nick
     * @param bool $admin - whether the account should be admin
     * @return Account
     */
    public static function create ( string $usr, string $pwd,
                                    string $nick = "", bool $admin = false)
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
        $new_account->admin = $admin;

        // create new private_key key for storing cookies
        $new_account->private_key = bin2hex(
            mcrypt_create_iv(static::$TOKEN_SIZE)
        );

        # store the object
        $pdo = WhackDB::getInstance()->getPDO();
        $store_sql = "INSERT INTO 
                      whack.Account(name, password, nick, private_key, admin) 
                      VALUES (:name, :pwd, :nick, :private_key, :admin)";
        $store_acc = $pdo->prepare($store_sql);

        $worked = $store_acc->execute([
            ':name' => $new_account->name,
            ':pwd'  => $new_account->password,
            ':nick' => $new_account->nick,
            ':private_key' => $new_account->private_key,
            ':admin' => (int)$new_account->admin
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
    public static function getUser (string $usr)
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

    /**
     * Get an Account object corresponding to the Account's id in the Account
     * table
     *
     * @param int $uid
     * @return Account
     */
    public static function getUserById ( int $uid ) : Account
    {
        $db = WhackDB::getInstance()->getPDO();

        $grab = $db->prepare("SELECT * FROM whack.Account WHERE id = :id");
        $grab->execute([ ':id' => $uid ]);
        $grab->setFetchMode(PDO::FETCH_CLASS, static::class);
        # only one id per account
        $match = $grab->fetch();

        return $match;
    }

}