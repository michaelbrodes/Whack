<?php
require("../phrases/index.php");
require("../vendor/autoload.php");
use whack\phrases;

/**
 * Created by PhpStorm.
 * User: michael
 * Date: 10/2/16
 * Time: 11:44 PM
 */
class PhraseTest extends PHPUnit_Framework_TestCase
{
    public function testCorrectIdRange()
    {
        $max_id = 7;
        $excluded = [1, 2, 5];
        $ids = phrases\id_range($max_id, $excluded);

        $this->assertEquals([3, 4, 6, 7], $ids);

        $max_id = 12;
        $excluded = [3, 4, 6, 7, 9, 10];
        $ids = phrases\id_range($max_id, $excluded);

        $this->assertEquals([1, 2, 5, 8, 11, 12], $ids);
    }

    public function testRandomPhrase()
    {
        $excluded = [1, 2, 3, 4, 5, 6, 8, 9, 10, 11];

        $phrase = phrases\get_random_phrase($excluded);

        $this->assertTrue(self::isJson($phrase), "No infinite loop, and is sending back json");
    }

}
