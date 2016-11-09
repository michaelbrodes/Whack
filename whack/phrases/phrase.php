<?php
/**
 *
 */
namespace whack\phrases;
require_once('../www/WhackDB.php');
require("Phrase.php");

use whack\data;
use \PDO;

/**
 * Get a random phrase from the Phrase table in whack.db.
 *
 * @param array $excluded_ids - phrases we don't want returned from this function.
 * @return string - the json encoded phrase.
 */
function get_random_phrase ( $excluded_ids = [] )
{
    $pdo = www\get_pdo();
    # this will be the max value of the range
    $max_id = $pdo->query("SELECT MAX(id) from Phrase")->fetch()[0];
    # the sql statement we will eventually execute after computation
    $prep = $pdo->prepare("SELECT * from Phrase where id != :id");
    # the sliced up range of ids we can choose from
    $id_range = id_range($max_id, $excluded_ids);
    # what we will eventually return
    $phrase_json = "";

    if ($id_range === [])
    {
        # we reset the game since we have no more new ids to use
        $id_range = range(1, $max_id);
    }

    $picked_id = array_rand($id_range);
    $prep->bindParam(":id", $picked_id);

    if($prep->execute() && $prep->rowCount() > 0)
    {
        $phrase = $prep->fetch(PDO::FETCH_CLASS, "Phrase");
        $phrase_json = json_encode($phrase);
    }
    else
    {
        array_push($excluded_ids, $picked_id);
        echo "I may end up in an infinite loop";
        $phrase_json = get_random_phrase($excluded_ids);

    }

    return $phrase_json;
}

/**
 * Create a range of ids from 1 to max_id with $excluded_ids removed
 *
 * @param int $max_id - the max id allowed in the range
 * @param array $excluded_ids - all the ids not allowed in the resulting range
 * @return array - the resulting sliced up range of ids
 */
function id_range( $max_id, $excluded_ids = [] )
{
    # needed to create the ranges of ids that can be used.
    sort($excluded_ids);
    # we want at least 1 and nothing more than max_id
    array_unshift($excluded_ids, 0);
    $length = array_push($excluded_ids, $max_id + 1);
    print_r($excluded_ids);
    $included_ids = [];


    for ($i = 0; $i < $length - 1; $i++)
    {
        # if false: skip since there is no integer between the two elements
        if ($excluded_ids[$i + 1] - $excluded_ids[$i] !== 1)
        {
            $id_range = range($excluded_ids[$i] + 1, $excluded_ids[$i+1] - 1);
            $included_ids = array_merge($included_ids, $id_range);
            print_r($included_ids);
        }
    }

    return $included_ids;
}
