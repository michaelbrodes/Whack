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
 * Class PhraseGame
 *
 * Represents a phrase from the database.
 */
class Phrase
{
    # attributes provided by PDO::FETCH_CLASS
    private $id, $statement, $author, $char_count, $origin;
    # attributes provided by the constructor
    private $assoc_images;
    private $assoc_audio;
    # pdo object acquired from WhackDB
    private $db;
    # 1 MiB
    const MAX_IMAGE = 1049000;
    # 11 MiB
    const MAX_AUDIO = 11530000;

    public function __construct()
    {
        # get the single instance of the WhackDB object
        $this->db = WhackDB::getInstance();
        # if this was called by a pdo statement get it's images
        if ( $this->id )
        {
            $this->assoc_images = $this->fetch_imgs();
            $this->assoc_audio  = $this->fetch_adio();
        }
    }

    /**
     * The id of the database entry
     * @return integer
     */
    public function getId() : int
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
     * Take in an entry from the $_FILES array, move it's contents to
     * /assets/audio/ and record it in the Audio and Audio_Phrase tables
     *
     * @param array $audio_arr - an entry from the $_FILES array
     * @return bool
     */
    public function upload_audio ( array $audio_arr ) : bool
    {
        $src = $audio_arr['tmp_name'];
        $pdo = $this->db->getPDO();
        $valid_types = [
            'audio/mp4',
            'video/mp4',
            'audio/mpeg',
            'audio/wav',
            'audio/wave',
            'audio/mpeg3',
            'audio/x-wav',
            'audio/x-mpeg',
            'audio/x-mpeg-3',
            'audio/ogg'
        ];

        $dest = "/assets/audio/" . basename($src);
        $size = $audio_arr['size'];
        $mime = mime_content_type($src);

        # checking file validity
        $size_check = $size > static::MAX_AUDIO;
        $invalid_mime = !in_array( $mime, $valid_types );
        $fail = $size_check || $invalid_mime || isset($audio_arr['error']);
        if ( $fail ) {
            return !$fail;
        }

        # move audio from upload dir to assets - return false on fail
        if ( !move_uploaded_file($src, $dest) ) return false;

        #insert into Audio table
        $audio = $pdo->prepare(
            "INSERT INTO whack.Audio(path, content_type) VALUES (:path, :mime)"
        );

        if( !$audio->execute([':path' => $dest, ':mime' => $mime]) )
        {
            # failed insertion into audio table
            return false;
        }

        $aid = $pdo->lastInsertId();
        $join = $pdo->prepare(
            "INSERT INTO whack.Audio_Phrase (Phrase_id, Audio_id) 
             VALUES (:pid, :aid)"
        );
        $join_suc = $join->execute([':aid' => $aid, ':pid' => $this->id]);
        $this->db->freePDO($pdo);
        $this->assoc_audio = $this->fetch_adio();

        return $join_suc;
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
        $dest = $_SERVER['DOCUMENT_ROOT'] ."/assets/images/"
            . basename($image_array['name']);
        $pdo = $this->db->getPDO();
        $size = $image_array['size'];
        # check to make sure mime is jpg or png
        $mime = mime_content_type($name);
        $valid_types = array('image/jpeg', 'image/png');
        $valid_mime = in_array($mime, $valid_types);
        $error = $image_array['error'] !== UPLOAD_ERR_OK;
        # can't be larger than 1 MiB
        $fail = $size > static::MAX_IMAGE || $error || !$valid_mime;

        if ( $fail )
        {
            return !$fail;
        }

        # if the move doesn't work we don't want the sql insertion to happen
        if (!move_uploaded_file($name, $dest))
        {
            return false;
        }

        $img_sql =
            "INSERT INTO whack.Image(image_path, content_type) VALUES (:path, :type)";
        $img_stmt = $pdo->prepare($img_sql);
        $server_path = '/assets/images/' . basename($image_array['name']);
        # whether the insertion failed or not
        $img_stmt->execute(array (
            ":type" => $mime,
            ":path" => $server_path
        ));
        $image_id = (int)$pdo->lastInsertId();

        $join_sql =
            "INSERT INTO whack.Image_Phrase(Phrase_id, Image_id) VALUES (:phrase, :image)";
        $join_stmt = $pdo->prepare($join_sql);
        # image_id won't be set to -1 if fail is false
        $success = $join_stmt->execute(array(
            ":phrase" => $this->id,
            ":image"  => $image_id
        ));

        $this->assoc_images = $this->fetch_imgs();
        $this->db->freePDO($pdo);
        return $success;
    }

    /**
     * Looks into the whack database for the image associated with this PhraseGame
     *
     * @return array - the image(s) associated with this phrase (instantiated
     *                 as Image objects)
     */
    private function fetch_imgs() : array
    {
        $pdo = $this->db->getPDO();
        $id_stmt = $pdo->prepare("SELECT Image_id FROM whack.Image_Phrase WHERE Phrase_id = :id");

        $id_stmt->bindParam(":id", $this->id);
        $id_stmt->setFetchMode(PDO::FETCH_ASSOC);
        $id_stmt->execute();
        $image_ids = $id_stmt->fetchAll();
        # A string with count($image_ids)'s worth of "?", All joined with a ","
        $sql_params = implode(",", array_fill(0, count($image_ids), "?"));

        # if we are not getting anything then we can just not do any work
        if ( empty($sql_params) )
        {
            return array();
        }

        $image_sql = "SELECT * FROM whack.Image WHERE id IN (". $sql_params." )";
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

    private function fetch_adio () : array
    {
        $audio_join = "SELECT Audio.path, Audio.content_type 
                       FROM whack.Audio 
                       INNER JOIN whack.Audio_Phrase
                       ON whack.Audio.id = whack.Audio_Phrase.Audio_id 
                       WHERE Phrase_id = :id";
        $audio_stmt = $this->db->getPDO()->prepare($audio_join);
        $audio_stmt->execute([":id" => $this->id]);
        return $audio_stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array
     */
    public function getAssocImages(): array
    {
        return $this->assoc_images;
    }

    public function getAssocAudio(): array
    {
        return $this->assoc_audio;
    }

    /**
     * Creates a new phrase entry into the phrase table
     * @param string $author
     * @param string $phrase
     * @param string $origin
     * @return Phrase
     */
    public static function create(string $author,
                                  string $phrase,
                                  string $origin): Phrase
    {
        $pdo = WhackDB::getInstance()->getPDO();
        $insert = $pdo->prepare(
            "INSERT INTO whack.Phrase (author, statement, char_count, origin)
             VALUES (:author, :phrase, :chars, :origin)"
        );

        # character count of the statement
        $chars = strlen($phrase);
        $execed = $insert->execute([
            ":author" => $author,
            ":phrase" => $phrase,
            ":chars"  => $chars,
            ':origin' => $origin
        ]);

        if ( $execed )
        {
            $new = new Phrase();
            $new->id = $pdo->lastInsertId();
            $new->author = $author;
            $new->char_count = $chars;
            $new->statement = $phrase;
            $new->origin = $origin;
        }
        else
        {
            $new = false;
        }

        WhackDB::getInstance()->freePDO($pdo);
        return $new;
    }

}