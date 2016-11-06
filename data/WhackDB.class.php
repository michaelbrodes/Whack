<?php
namespace whack\data;

use \PDO;
use \PDOException;

/**
 * Class WhackDB
 *
 * The WhackDB singleton class is an abstraction of the PDO object which
 * provides an interface for common database functions
 * @property string image_table
 */
class WhackDB
{
    public static $image_table = "Images";
    public static $phase_table = "Phrase";
    private static $_instance;
    private $db = null;
    private $db_path;
    const PDO_OPTS = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ];

    /**
     * WhackDB constructor.
     */
    protected function __construct ()
    {
        $this->db_path = getenv('DB');
        $this->db = $this->createPDO($this->db_path);
    }

    /**
     * @param string $db_path
     * @return null|PDO - the connection to the database
     */
    private function &createPDO( string $db_path )
    {
        $connection = null;
        try
        {
            $connection = new PDO('sqlite:' . $db_path);
            foreach ( static::PDO_OPTS as $attr => $option )
            {
                $connection->setAttribute($attr, $option);
            }
        }
        catch ( PDOException $e )
        {
            $file = $e->getFile();
            $line = $e->getLine();
            $error_reason = $e->getMessage();
            $stack_trace = $e->getTraceAsString();
            $destination = getenv("SITE_ERROR");

            $message = "Error found in $file at line $line\n" .
                "Reason: $error_reason\n" . $stack_trace;

            error_log($message, 3, $destination);
        }

        return $connection;
    }

    /**
     * Get the single instance of WhackDB. If not already defined, the
     * constructor is called.
     *
     * @return WhackDB - the Single instance of WhackDB.
     */
    public static function getInstance()
    {
        if ( null === static::$_instance )
        {
            static::$_instance = new static();
        }

        return static::$_instance;
    }

    /**
     * Checks to make sure the database is connected. If not, throw a
     * PDOException.
     *
     * @return PDO - the database connection
     */
    public function &getPDO()
    {
        # the pdo object has been instantiated and hasn't been freed
        if ($this->db === null)
        {
            $this->db = $this->createPDO($this->db_path);
        }
        return $this->db;
    }

    /**
     * Frees the pdo object.
     */
    public function freePDO()
    {
        $this->db = null;
    }
}
