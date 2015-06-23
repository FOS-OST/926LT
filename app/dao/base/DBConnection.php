<?php
namespace App\Libraries;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use MongoClient;

class DBConnection
{
    private $db = null;
    private $connection=null;
    private static $instance;
    /**
     * @var \Phalcon\Config
     */
    private $config;

    function __construct($config)
    {
        $this->config=$config;
        $this->createConnection();
    }

    public function getMongoDB() {
        return $this->db;
    }
    public function getConnection() {
        return $this->connection;
    }

    private function createConnection()
    {
        $dns = $this->getDns();
        $options = array();

        $options['username'] = $this->config->get('mongodb.username');

        $options['password'] = $this->config->get('mongodb.password');

        $this->connection = new MongoClient($dns, $options);
        $this->db = $this->connection->selectDB($this->config->get('mongodb.database'));
    }
    private function getDns() {

        $host=$this->config->get('mongodb.host');
        $port=$this->config->get('mongodb.port');
        // The database name needs to be in the connection string, otherwise it will
        // authenticate to the admin database, which may result in permission errors.
        return "mongodb://".$host.":".$port ;
    }

}
