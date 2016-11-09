<?php

/**
 * Created by PhpStorm.
 * User: michael
 * Date: 10/2/16
 * Time: 11:38 PM
 */
namespace whack\data;
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
    private $assoc_images;
    # pdo object acquired from WhackDB
    private $db;

    public function __construct()
    {
        # get the single instance of the WhackDB object
        $this->db = WhackDB::getInstance();
        # if this was called by a pdo statement get it's images
        if ( $this->id )
        {
            $this->assoc_images = $this->fetch_imgs();
        }
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
    public function getAuthor() : string
    {
        return $this->author;
    }

    /**
     * How long the phrase is
     * @return integer
     */
    public function getCharCount() : int
    {
        return $this->char_count;
    }

    /**
     * Where the phrase came from
     * @return string
     */
    public function getOrigin() : string
    {
        return $this->origin;
    }


    /**
     * The statement people are going to type.
     * @return string
     */
    public function getStatement() : string
    {
        return $this->statement;
    }


    /**
     * Upload an image attached to this phrase to the Images database.
     *
     * @param array $image_array - the associative array from $_FILES super
     *                             global
     * @return bool whether the upload succeeded
     */
    public function upload_image(array $image_array) : bool
    {
        # the image is associated with a phrase so we can use it's tmp_name
        $name = $image_array['tmp_name'];
        $dest = $_SERVER['DOCUMENT_ROOT'] . "/assets/images/" . basename($name);
        $pdo = $this->db->getPDO();
        $size = $image_array['size'];
        # check to make sure mime is jpg or png
        $mime = mime_content_type($name);
        $valid_types = array('image/jpeg', 'image/png');
        $valid_mime = in_array($mime, $valid_types);
        # can't be larger than 1 MiB
        $fail = $size > 1049000 || isset($image_array['error']) || $valid_mime;

        if ( $fail )
        {
            return $fail;
        }

        # if the move doesn't work we don't want the sql insertion to happen
        move_uploaded_file($name, $dest);

        $img_sql =
            "INSERT INTO Image(image_path, content_type) VALUES (:path, :type)";
        $img_stmt = $pdo->prepare($img_sql);
        # whether the insertion failed or not
        $img_stmt->execute(array (
            ":type" => $mime,
            ":path" => $dest
        ));
        $image_id = (int)$pdo->lastInsertId();

        $join_sql =
            "INSERT INTO Image_Phrase(Phrase_id, Image_id) VALUES (:phrase, :image)";
        $join_stmt = $pdo->prepare($join_sql);
        # image_id won't be set to -1 if fail is false
        $join_stmt->execute(array(
            ":phrase" => $this->id,
            ":image"  => $image_id
        ));

        $this->assoc_images = $this->fetch_imgs();

        $this->db->freePDO($pdo);
        return $fail;
    }

    /**
     * Looks into the whack database for the image associated with this Phrase
     *
     * @return array - the image(s) associated with this phrase (instantiated
     *                 as Image objects)
     */
    private function fetch_imgs() : array
    {
        $pdo = $this->db->getPDO();
        $id_stmt = $pdo->prepare("SELECT Image_id FROM Image_Phrase WHERE Phrase_id = :id");

        $id_stmt->bindParam(":id", $this->id);
        $id_stmt->setFetchMode(PDO::FETCH_ASSOC);
        $id_stmt->execute();
        $image_ids = $id_stmt->fetchAll();
        # A string with count($image_ids)'s worth of "?", All joined with a ","
        $sql_params = implode(",", array_fill(0, count($image_ids), "?"));
        $image_sql = "SELECT * FROM Image WHERE id IN (". $sql_params." )";
        $img_stmt = $pdo->prepare($image_sql);

        foreach( $image_ids as $i => $id)
        {
            # PDOStatement is 1-indexed
            $img_stmt->bindValue(($i+1), $id["Image_id"]);
        }

        # fetch all the images into instances of Image by calling the ctor
        $img_stmt->setFetchMode(
            PDO::FETCH_CLASS,
            Image::class
        );
        $img_stmt->execute();

        $images = $img_stmt->fetchAll();
        $this->db->freePDO($pdo);

        return $images;
    }

    /**
     * @return array
     */
    public function getAssocImages(): array
    {
        return $this->assoc_images;
    }

}