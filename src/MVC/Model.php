<?php

namespace Web\MVC;

use Web\Database\Database;
use Web\MVC\Interfaces\ModelInterface;

use Exception;

class Model implements ModelInterface
{
    protected $db;
    protected $table;
    protected $fields = ['*'];
    protected $plural = 's';

    public function __construct(Database $db) 
    {
        $this->db = $db;

        if(!isset($this->table)) {
            $this->table = strtolower(array_pop(explode('\\', static::class))) . $this->plural;
        }
    }

    public function create($fields = [])
    {
        return $this->db->table($this->table)->insert($fields);
    }

    public function read($where = [])
    {
        return $this->db
        ->table($this->table)
        ->fields($this->fields)
        ->select($where)
        ->results();
    }

    public function update($fields = [], $where = [])
    {
        return $this->db->table($this->table)->update($fields, $where);
    }

    public function delete($where = [])
    {
        return $this->db->table($this->table)->delete($where);
    }
}