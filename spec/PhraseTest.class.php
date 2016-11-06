<?php
require("../phrases/Phrase.class.php");
require("../vendor/autoload.php");
use \whack\phrases;
use whack\data\WhackDB;
use whack\phrases\Phrase;

/**
 * Created by PhpStorm.
 * User: michael
 * Date: 10/2/16
 * Time: 11:44 PM
 */
class PhraseTest extends PHPUnit_Framework_TestCase
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

    public function testPhraseCreate()
    {
        $ctor_args = array('id', 'statement', 'author', 'char_count', 'origin');
        $db = WhackDB::getInstance()->getPDO();

        # fetch q-tip's phrase from the database
        $query = $db->query("SELECT * FROM Phrase WHERE id = 7");
//        $q_tip = $query->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, Phrase::class, $ctor_args)[0];
//        var_dump($q_tip);
//
//        # what 7 should be
//        $q_tip_expected = new Phrase(
//            7,
//            "Shorty let me tell you about my only vice, It has to do with lots of loving and it ain't nothing nice",
//            "Q-tip",
//            101,
//            "Electric Relaxation"
//        );
//
//        $this->assertInstanceOf(Phrase::class, $q_tip);
//        $this->assertEquals($q_tip_expected, $q_tip, "The q_tip's are not the same");
    }

    public function testImageUpload()
    {

    }

}
