<?php

/**
 * Created by PhpStorm.
 * User: michael
 * Date: 10/2/16
 * Time: 11:38 PM
 */
namespace whack\phrases;
require_once ("../data/WhackDB.class.php");
require("Image.class.php");

use whack\data;
use \PDO;

/**
 * Class Phrase
 *
 * Represents a phrase from the database.
 */
class Phrase
{
    # attributes provided by PDO::FETCH_CLASS
    private $id, $statement, $author, $char_count, $origin;
    # pdo object acquired from WhackDB
    private $pdo;
    private $db;

    public function __construct($id, $statement, $author, $char_count, $origin)
    {
        # initialize columns from database.
        $this->id = $id;
        $this->statement = $statement;
        $this->author = $author;
        $this->char_count = $char_count;
        $this->origin = $origin;

        # get the single instance of the WhackDB object
        $this->db = data\WhackDB::getInstance();
        # get reference to the pdo object
        $this->pdo = $this->db->getPDO();
    }

    /**
     * The id of the database entry
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Who made the phrase
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * How long the phrase is
     * @return integer
     */
    public function getCharCount()
    {
        return $this->char_count;
    }

    /**
     * Where the phrase came from
     * @return string
     */
    public function getOrigin()
    {
        return $this->origin;
    }


    /**
     * The statement people are going to type.
     * @return string
     */
    public function getStatement()
    {
        return $this->statement;
    }

    /**
     * Looks into the whack database for the image associated with this Phrase
     *
     * @return array - the image(s) associated with this phrase (instantiated
     *                 as Image objects)
     */
    public function get_assoc_image()
    {
        $query = $this->pdo->prepare(
            "SELECT image_data, content_type, phrase_id FROM "
            . $this->db->image_table ." where phase_id = :id");

        $image_class = Image::class;
        $ctor_args = array('image_data', 'content_type', 'assoc_phrase');

        $query->bindParam(":id", $this->id);
        $query->setFetchMode(
            PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, $image_class, $ctor_args
        );
        $query->execute();

        return $query->fetchAll();
    }

    /**
     * Upload an image attached to this phrase to the Images database.
     *
     * @param array $image_array - the associative array from $_FILES super
     *                             global
     * @param int $phrase_id - the id of the phrase that the image will be
     *                         attached to. Defaults to an invalid index for
     *                         this object's id.
     */
    public function upload_image($image_array, $phrase_id = -1)
    {
        $query = $this->pdo->prepare(
            "INSERT INTO ". $this->db->image_table .
            " (image_data, content_type, phrase_id) VALUES (:image, :mime, :id)"
        );
        # get the binary content of the image that was put in the server's temp
        $imageBlob = file_get_contents($image_array['tmp_name']);
        $content_type = $image_array['type'];
        $phrase_id = ($phrase_id !== -1) ? $phrase_id : $this->id;

        # the $phrase_id is either the default -1 or it isn't set in this object
        if ( is_nan($phrase_id) )
        {
            throw new \PDOException('The $phrase_id given is invalid');
        }
        # the size limit is 30,000 bytes
        else if ( $image_array["size"] > 100000 )
        {
            throw new \PDOException('The image given was too large');
        }

        $query->bindParam(":image", $imageBlob, PDO::PARAM_LOB);
        $query->bindParam(":mime", $content_type, PDO::PARAM_STR);
        $query->bindParam(":id", $phrase_id, PDO::PARAM_INT);
        $query->execute();
    }

}