<?php

namespace Spl\Database;

use PDO;
use PDOException;

class Connection
{
    protected static $factory = null;

    protected $connection = [
        'host' => '127.0.0.1',
        'driver' => 'mysql',
        'dbname' => '',
        'username' => 'root',
        'password' => 'mysql',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ];

    public $pdo;
    
    public $driver;
    public $host;
    public $dbname;
    public $username;
    public $password;
    public $charset;
    public $collation;

    public static function factory($connection = array())
    {
        if (!isset(self::$factory)) {
            self::$factory = new Connection($connection);
        }

        return self::$factory;
    }

    public function __construct($connection = array())
    {
        if(!empty($connection)) {
            $this->connection = $connection;
        }

        $this->driver = $this->connection['driver'];
        $this->host = $this->connection['host'];
        $this->dbname = $this->connection['dbname'];
        $this->username = $this->connection['username'];
        $this->password = $this->connection['password'];
        $this->charset = $this->connection['charset'];

        try {
            $this->pdo = new PDO(
                $this->driver . ':host=' . 
                $this->host . ';dbname=' . 
                $this->dbname . ';charset=' .
                $this->charset, 
                $this->username, 
                $this->password
            );

        } catch (PDOException $error) {
            die($error->getMessage());
        }
    }
}