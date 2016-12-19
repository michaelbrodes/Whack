<?php
require_once '../vendor/autoload.php';
require_once '../whack/management/create.php';
use PHPUnit\Framework\TestCase;
use whack\data\WhackDB;
use whack\data\Account;
use whack\management;

function something ()
{

}

/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12/18/16
 * Time: 9:53 PM
 */
class AccountTest extends PHPUnit_Framework_TestCase
{
    public function testUserExistence()
    {
        $user1 = "michael";
        $doesExist = Account::check_existence($user1);
        $this->assertTrue($doesExist, $user1 . " does exist");
        $user2 = "zach";
        $doesExist = Account::check_existence($user2);
        $this->assertFalse($doesExist, $user2 . " doesn't exist");
    }

    public function testCreate()
    {
        $user = "michael";
        # obviously not a good idea in real life, but I am doing it here
        $pwd = "password";
        $nick = "michael rhodes";

        $newdude = Account::create($user, $pwd, $nick);
        $this->assertInstanceOf(Account::class, $newdude);
        $this->assertEquals($user, $newdude->name);
        $this->assertNotEquals($pwd, $newdude->password,
            "Making sure the hashed password doesn't match the inputtted password");

        $password_works = password_verify($pwd, $newdude->password);
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

        $password_works = password_verify($pwd, $max_acc->password);
        $this->assertTrue($password_works,
            "Checking if the inserted password can be verified.");

    }

    /**
     * tests the create.php script to make sure that it is right
     */
    public function testCreatePHP ()
    {
        $validusr = "something";
        $invalidusr = "no thing";

        $isValid = management\checkname($validusr);
        $this->assertTrue($isValid, "Testing valid username");

        $isValid = management\checkname($invalidusr);
        $this->assertFalse($invalidusr, "Testing invalid username");

        $validpwd = "password";
        $invalidpwd = " 1034";

        $isValid = management\checkpass($invalidpwd, "password");
        $this->assertFalse($isValid, "Testing invalid password");

        $isValid = management\checkpass($validpwd, "password");
        $this->assertTrue($isValid, "Testing valid password");

        $isValid = management\checkpass($validpwd, "not password");
        $this->assertFalse($isValid, "Testing valid password that doesn't match confirmation");
    }
}
