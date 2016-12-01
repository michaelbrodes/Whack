<?php
require_once '../vendor/autoload.php';
require_once '../whack/phrases/get_keyboard.php';
use PHPUnit\Framework\TestCase;
use whack\data\WhackDB;
use whack\data\Phrase;
use whack\data\Image;
use whack\phrases;

/**
 * Created by PhpStorm.
 * User: michael
 * Date: 10/2/16
 * Time: 11:44 PM
 */
class PhraseTest extends TestCase
{
//    public function testCorrectIdRange()
//    {
//        $max_id = 7;
//        $excluded = [1, 2, 5];
//        $ids = phrases\id_range($max_id, $excluded);
//
//        $this->assertEquals([3, 4, 6, 7], $ids);
//
//        $max_id = 12;
//        $excluded = [3, 4, 6, 7, 9, 10];
//        $ids = phrases\id_range($max_id, $excluded);
//
//        $this->assertEquals([1, 2, 5, 8, 11, 12], $ids);
//    }
//
//    public function testRandomPhrase()
//    {
//        $excluded = [1, 2, 3, 4, 5, 6, 8, 9, 10, 11];
//
//        $phrase = phrases\get_random_phrase($excluded);
//
//        $this->assertTrue(self::isJson($phrase), "No infinite loop, and is sending back json");
//    }

    public function testDatabaseSingleton()
    {
        $database = WhackDB::getInstance();
        $this->assertInstanceOf(WhackDB::class, $database, "returning a WhackDB object");
        $pdo = $database->getPDO();
        $this->assertInstanceOf(PDO::class, $pdo, "getPDO is returning a pdo before free");
        $this->assertEquals($database->getPDO(), $pdo, "getPDO is returning a reference");
        $database->freePDO($pdo);
        $this->assertNull($pdo, '$pdo is null after freePDO');
        $pdo = $database->getPDO();
        $statement = $pdo->query("SELECT * FROM Phrase");
        $this->assertInstanceOf(PDO::class, $pdo, "getPDO is returning a pdo after free");
        $this->assertInstanceOf(PDOStatement::class, $statement);
        $database->freePDO($pdo);
    }

    public function testPhraseCreation() : Phrase
    {
        $database = WhackDB::getInstance();
        $pdo = $database->getPDO();
        $statement = $pdo->query("SELECT * FROM Phrase WHERE id = 6");
        $result = $statement->fetchAll(
            PDO::FETCH_CLASS,
            Phrase::class
        );

        foreach ( $result as $phrase )
        {
            $this->assertInstanceOf(Phrase::class, $phrase);
            if (isset($phrase)) {
                $this->assertTrue(is_string($phrase->getOrigin()));
            }
        }

        return $result[0];
    }

    /**
     * @depends testPhraseCreation
     * @param Phrase $phrase
     */
    public function testAssocImages(Phrase $phrase)
    {
        $assoc = $phrase->getAssocImages();

        foreach( $assoc as $image )
        {
            $this->assertInstanceOf(
                Image::class, $image,
                "The example image isn't of type Image"
            );
        }
    }

    public function testGetKeyboard()
    {
        $current_ctry = phrases\grab_config('currentCountry');
        $this->assertJson($current_ctry);
        $this->assertEquals("us", json_decode($current_ctry));
        $board_str = phrases\get_board();

        $this->assertJson($board_str);
        $board_arr = json_decode($board_str, true);

        $this->assertNotNull($board_arr);
        $this->assertContains("4^$", $board_arr[0]);
    }

//    /**
//     * @depends testPhraseCreation
//     * @param Phrase $phrase
//     */
//    public function testImageUpload(PhraseGame $phrase)
//    {
//    }

}
