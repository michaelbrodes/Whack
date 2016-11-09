<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 10/16/16
 * Time: 10:41 PM
 */

namespace whack\data;


/**
 * Class Image
 * @package whack\phrases
 * An image associated with the Images table in whack.db
 */
class Image
{
    private $id, $image_path, $content_type;

    private $db;

    /**
     * Image constructor.
     */
    public function __construct()
    {
        $this->db = WhackDB::getInstance();
    }

    /**
     * The id associated with this image in the WhackDB
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * The content type of the image
     * @return string
     */
    public function getContentType() : string
    {
        return $this->content_type;
    }

    /**
     * @return string
     */
    public function getImagePath() : string
    {
        return $this->image_path;
    }
}