<?php

namespace Web\Database;

use PDO;

class Database
{
    protected $db;

    public function __construct(PDO $connection)
    {
        $this->db = $connection;

        return $this->db;
    }
}