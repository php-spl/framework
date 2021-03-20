<?php

namespace Web\Database;

use Web\Database\Connection;

class Database
{
    protected $db;

    public function __construct(Connection $connection)
    {
        $this->db = $connection;

        return $this->db;
    }
}