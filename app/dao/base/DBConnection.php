<?php
namespace App\Libraries;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use MongoClient;

class DBConnection
{
    private $db_config="mongodb";
    private $db = null;
    private $connection=null;
    private static $instance;

    function __construct()
    {
        $this->createConnection();
    }


    public static function getInstance()
    {
        if(self::$instance==null) {
            self::$instance = new DBConnection();
        }
        return self::$instance;
    }

    public function getMongoDB() {
        return $this->db;
    }
    public function getConnection() {
        return $this->connection;
    }

    private function createConnection() {
        $configs=Config::get("database.connections.".$this->db_config);
        $dns=$this->getDns($configs);
        $options=array();
        if ( ! empty($config['username']))
        {
            $options['username'] = $config['username'];
        }
        if ( ! empty($config['password']))
        {
            $options['password'] = $config['password'];
        }
        $this->connection= new MongoClient($dns, $options);
        $this->db=$this->connection->selectDB($configs['database']);
    }
    private function getDns($configs) {

        extract($configs);
        // Treat host option as array of hosts

        $hosts = is_array($host) ? $host : [$host];
        foreach ($hosts as &$host)
        {
            // Check if we need to add a port to the host
            if (strpos($host, ':') === false and isset($port))
            {
                $host = "{$host}:{$port}";
            }
        }
        // The database name needs to be in the connection string, otherwise it will
        // authenticate to the admin database, which may result in permission errors.
        return "mongodb://" . implode(',', $hosts) . "/{$database}";
    }

}
