<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 10/16/16
 * Time: 10:41 PM
 */

namespace whack\phrases;


/**
 * Class Image
 * @package whack\phrases
 * An image associated with the Images table in whack.db
 */
class Image
{
    private $id, $image_data, $content_type, $assoc_phrase;

    /**
     * Image constructor.
     * @param $id
     * @param $image_data
     * @param $content_type
     * @param $assoc_phrase
     */
    public function __construct($id, $image_data, $content_type, $assoc_phrase)
    {
        $this->id = $id;
        $this->image_data = $image_data;
        $this->content_type = $content_type;
        $this->assoc_phrase = $assoc_phrase;
    }

    /**
     * The id associated with this image in the WhackDB
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * The binary composing this image of the image, encoded w/ base64
     * @return string
     */
    public function getImageData()
    {
        return base64_encode($this->image_data);
    }

    /**
     * The content type of the image
     * @return string
     */
    public function getContentType()
    {
        return $this->content_type;
    }

    /**
     * The Phrase associated with this image.
     * @return string
     */
    public function getAssocPhrase()
    {
        return $this->assoc_phrase;
    }
}