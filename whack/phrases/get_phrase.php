<?php
namespace whack\phrases;
require_once $_SERVER["DOCUMENT_ROOT"] . '/vendor/autoload.php';

use whack\data\Phrase;
use whack\data\WhackDB;
use \PDO;
session_start();

/**
 * Returns an associative array representing an phrase
 * @param Phrase $phrase
 * @return array
 */
function phrase_to_array( Phrase $phrase ) : array
{
    $image = isset($phrase->getAssocImages()[0])?
        $phrase->getAssocImages()[0]->getImagePath(): null;
    $audio = isset($phrase->getAssocAudio()[0])?
        $phrase->getAssocAudio()[0]['path']: null;

    $phrase_array = [
        "id" => $phrase->getId(),
        "statement" => $phrase->getStatement(),
        "author" => $phrase->getAuthor(),
        "origin" => $phrase->getOrigin(),
        "imagePath" => $image,
        "audioPath" => $audio
    ];

    return $phrase_array;
}

/**
 * Get the next phrase based on the previous phrase. If the previous id
 * is the default of -1 then just grab a random id.
 * @return array - an associative array for a phrase.
 */
function get_phrase() : array
{
    $exclude_ids = (isset($_SESSION['prev']))? $_SESSION['prev']: [];
    $phrase_sql = "";

    if ( empty($exclude_ids) )
    {
        # the sql statement in the else statement is invalid if sql_params is
        # empty
        $phrase_sql = "SELECT * FROM whack.Phrase";
    }
    else
    {
        $phrase_sql = "SELECT * FROM whack.Phrase WHERE id NOT IN (".
            implode($exclude_ids, ',').")";
    }

    $phrase_array = array();

    $db = WhackDB::getInstance();
    $pdo = $db->getPDO();
    $stmt = $pdo->prepare($phrase_sql);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_CLASS, Phrase::class);
    $fetched_phrases = $stmt->fetchAll();

    if ( count($fetched_phrases) > 0 )
    {
        $phrase = $fetched_phrases[array_rand($fetched_phrases)];
        $phrase_array = phrase_to_array($phrase);
        array_push($exclude_ids, $phrase->getId());
        $_SESSION['prev'] = $exclude_ids;
    }
    else
    {
        # we have cycled through the database finding no entries, lets restart
        $restart_sql = "SELECT * FROM Phrase";
        $stmt = $pdo->query($restart_sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Phrase::class);
        $fetched_phrases = $stmt->fetchAll();
        $phrase = $fetched_phrases[array_rand($fetched_phrases)];
        $phrase_array = phrase_to_array($phrase);
        $_SESSION['prev'] = array(0 => $phrase->getId());
    }

    return $phrase_array;
}

header('Content-Type: application/json');
echo json_encode(get_phrase());
