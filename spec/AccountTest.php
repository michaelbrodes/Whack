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

    }
}
