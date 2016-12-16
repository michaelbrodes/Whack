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
    private $user = "root";
    private $pass = "";
    private $host = "localhost";
    const PDO_OPTS = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE
    ];

    /**
     * WhackDB constructor.
     */
    protected function __construct ()
    {
        $conf_path = $_SERVER['DOCUMENT_ROOT'] . "/conf/conf.json";
        $db_vars = json_decode(file_get_contents($conf_path), true)['database'];

        if ( !$db_vars )
        {
            echo "There was a problem opening the config file";
            die();
        }

        $this->user = $db_vars['user'];
        $this->pass = $db_vars['password'];
        $this->host = $db_vars['host'];

        $this->db = $this->createPDO();
    }

    /**
     * @return null|PDO - the connection to the database
     */
    private function &createPDO() : PDO
    {
        $connection = null;
        $dsn = 'mysql:host=' . $this->host . ';dbname=whack;port=3306';
        try
        {
            $connection = new PDO(
                $dsn,
                $this->user,
                $this->pass
            );

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

            echo $message;
            die();
            #error_log($message, 3, $destination);
        }

        return $connection;
    }

    /**
     * Get the single instance of WhackDB. If not already defined, the
     * constructor is called.
     *
     * @return WhackDB - the Single instance of WhackDB.
     */
    public static function getInstance() : WhackDB
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
    public function &getPDO() : PDO
    {
        # the pdo object has been instantiated and hasn't been freed
        if ($this->db === null)
        {
            $this->db = $this->createPDO();
        }
        return $this->db;
    }

    /**
     * Frees the pdo object.
     * @param PDO $pdo - the pdo object to free
     */
    public function freePDO(PDO &$pdo)
    {
        $pdo = null;
    }
}
