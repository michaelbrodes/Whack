<?php
require_once '../vendor/autoload.php';
use PHPUnit\Framework\TestCase;
use whack\data\WhackDB;
use whack\data\Account;

/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12/18/16
 * Time: 9:53 PM
 */
class AccountTest extends PHPUnit_Framework_TestCase
{
    private $id;
    private $user = "michael";
    private $pwd = "password";
    private $nick = "michael rhodes";

    public function testCreate()
    {

        $newdude = Account::create($this->user, $this->pwd, $this->nick);
        $this->assertInstanceOf(Account::class, $newdude);
        $this->assertEquals($this->user, $newdude->name);
        $this->assertNotEquals($this->pwd, $newdude->password,
            "Making sure the hashed password doesn't match the inputtted password");

        $password_works = password_verify($this->pwd, $newdude->password);
        $this->assertTrue($password_works,
            "checking if the hash can be verified with password_verify");

        $pdo = WhackDB::getInstance()->getPDO();
        $max = $pdo->query("SELECT MAX(id) FROM whack.Account");
        $max->setFetchMode(PDO::FETCH_ASSOC);
        $max_id = (int)$max->fetch()["MAX(id)"];

        $this->assertEquals($newdude->id, $max_id, "Checking if inserted");

        $max_cols = $pdo->query("SELECT * FROM whack.Account WHERE id = " .
            $max_id);
        $max_cols->setFetchMode(PDO::FETCH_CLASS, Account::class);
        $max_acc = $max_cols->fetch();

        $this->assertEquals($max_acc, $newdude,
            "Checking if the insertion is right");

        $password_works = password_verify($this->pwd, $max_acc->password);
        $this->assertTrue($password_works,
            "Checking if the inserted password can be verified.");

        $this->assertEquals(256, strlen($max_acc->private_key));

    }

    public function testUserExistence()
    {
        $user1 = "michael";
        $michael = Account::getUser($user1);
        $this->assertInstanceOf(Account::class, $michael);
        $user2 = "zach";
        $zach = Account::getUser($user2);
        $this->assertNull($zach, $user2 . " doesn't exist");
    }


    /**
     * tests the storeUserToken method
     */
    public function testStoreUserToken ()
    {
        $michael = Account::getUser($this->user);
        $token = $michael->storeToken();
        $token_size = strlen($token);
        $this->assertEquals($token_size, 256, "Token size should be 2*128 bytes");

        $pdo = WhackDB::getInstance()->getPDO();
        $this->id = $michael->id;
        $token_stmt = $pdo->query("SELECT * FROM whack.Token WHERE Account_id = $this->id");
        $stored = $token_stmt->fetch(PDO::FETCH_ASSOC)['token'];
        $this->assertEquals($token, $stored,
            "The token in the data base should be the same as the one that we created on the server");

        WhackDB::getInstance()->freePDO($pdo);
    }

    /**
     * tests the track user by cookie method and the
     */
    public function testGenCookie()
    {
        $user = Account::getUser("michael");
        $token = $user->storeToken();
        $cookie = $user->genCookie($token);
        $this->assertNotNull($cookie, "Cookie need not be null");
        list( $id, $public, $mac ) = explode(':', $cookie);
        $this->assertEquals($user->id, $id, "Ids are the same");
        $this->assertEquals($token, $public, "Tokens are the same");

        // now we need to verify the user using the $mac
        $pdo = WhackDB::getInstance()->getPDO();
        $secret_sql = "SELECT private_key FROM whack.Account WHERE id = $id";
        $secret_stmt = $pdo->query($secret_sql);
        $private = $secret_stmt->fetch(PDO::FETCH_ASSOC)['private_key'];
        $user_hash = hash_hmac('sha256', $id . ":" . $token, $private);
        $this->assertTrue(hash_equals($mac, $user_hash));
    }

    public function testCookieVerify()
    {
        $user = Account::getUser("michael");
        $token = $user->storeToken();
        $cookie = $user->genCookie($token);
        $this->assertTrue(Account::verifyCookie($cookie), "can verify user via cookie");
    }

//    /**
//     * tests the create.php script to make sure that it is right
//     */
//    public function testCreatePHP ()
//    {
//        $validusr = "something";
//        $invalidusr = "no thing";
//
//        $isValid = management\checkname($validusr);
//        $this->assertTrue($isValid, "Testing valid username");
//
//        $isValid = management\checkname($invalidusr);
//        $this->assertFalse($invalidusr, "Testing invalid username");
//
//        $validpwd = "password";
//        $invalidpwd = " 1034";
//
//        $isValid = management\checkpass($invalidpwd, "password");
//        $this->assertFalse($isValid, "Testing invalid password");
//
//        $isValid = management\checkpass($validpwd, "password");
//        $this->assertTrue($isValid, "Testing valid password");
//
//        $isValid = management\checkpass($validpwd, "not password");
//        $this->assertFalse($isValid, "Testing valid password that doesn't match confirmation");
//    }
}
