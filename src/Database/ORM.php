<?php

namespace Spl\Database;

use PDO;
use Exception;
use PDOException;

class ORM
{

    private const ACTION_INSERT = 'INSERT INTO ';
    private const ACTION_UPDATE = 'UPDATE ';
    private const ACTION_SELECT = 'SELECT ';
    private const ACTION_DELETE = 'DELETE ';

    protected $fillable = [];
    protected $fields = [];
    protected $prefix = null;
    protected $table = null;
    
    private $_driver,
            $_host,
            $_dbname,
            $_username,
            $_password;

    private $_pdo,
            $_sql,
            $_query,
            $_where,
            $_results,
            $_count = 0,
            $_values = [],
            $_error = false,
            $_fetchStyle = PDO::FETCH_OBJ;

    public function __construct($config = [])
    {
        if(!empty($config)) {
            $this->_driver = $config['driver'];
            $this->_host =  $config['host'];
            $this->_dbname = $config['dbname'];
            $this->_username = $config['username'];
            $this->_password = $config['password'];
        } else {
            throw new Exception('Missing config!');
        }

        try {
            $this->_pdo = new PDO("{$this->_driver}:host={$this->_host};dbname={$this->_dbname}", $this->_username, $this->_password);
            $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            if(!isset($this->table)) {
                $this->table = $this->getTableFromChildModelPlural();
            }
        } catch(PDOException $error) {
            die($error->getMessage());
        }
    }

    protected function getTableFromChildModelPlural()
    {
        $model = str_replace('Model', '', static::class);
        return strtolower(array_pop(explode('\\', $model))) . 's';
    }

    public function query($sql, $values = [])
    {
        $this->_error = false;

        if($this->_query = $this->_pdo->prepare($sql)) {
            $parameter = 1;
            if(count($values)) {
                foreach($values as $value) {
                    $this->_query->bindValue($parameter, $value);
                    $parameter++;
                }
            }

            if($this->_query->execute()) {
                if ($this->_query->rowCount() > 0) {
                    $this->_results = $this->_query->fetchAll($this->_fetchStyle);
                    $this->_count = $this->_query->rowCount();
                }
            } else {
                $this->_error = true;
            }
        }

        return $this;
    }

    public function action($action, $table, $where, $options = null)
    {
        if(isset($action) && isset($table)) {
            $this->_sql = trim("{$action} FROM {$table} {$where} {$options}");
                    
            if(!$this->query($this->_sql, $this->_values)) {
                return $this;
            }
        }

        return $this;
    }

    public function select($fields = '*')
    {
        if(is_array($fields)) {
              $fields = implode(', ', $fields);
        }
        $this->fields = $fields;
        return $this;
    }

    public function table($table = '')
    {
        if($table) { 
            $this->table = $table;
            return $this;
        }

        return false;
    }

    public function where($field, $operator = '', $value = '')
    {
        if(is_string($field) && empty($operator) && empty($value)) {
            $where = explode(' ', trim($field));
        } elseif(is_array($field)) {
            $where = $field;
        } else {
            $where = [$field, $operator, $value];
        }

        if(count($where) === 3) {
            $operators = ['=', '>', '<', '>=', '<='];

            $field = $where[0];
            $operator = $where[1];
            $value = $where[2];

            if(in_array($operator, $operators)) {
                $this->_where = "WHERE {$field} {$operator} ?";
                $this->_values = [$value];
                return $this;
            }
        }

        return false;
    }

    public function sql()
    {
        if($this->_sql) {
            return $this->_sql;
        }
    }

    public function get($options = null)
    {
        $data = $this->action(self::ACTION_SELECT . $this->fields, $this->table, $this->_where, $options);
        if($data->count()) {
            return $data->results();
        }
        return false;
    }

    public function delete()
    {
        return $this->action(self::ACTION_DELETE, $this->table, $this->_where);
    }

    public function insert($fields = [])
    {
        if(count($fields)) {
            $keys = array_keys($fields);
            $values = null;
            $counter = 1;

            foreach($fields as $field) {
                $values .= '?';
                if($counter < count($fields)) {
                    $values .= ', ';
                }
                $counter++;
            }

            $sql = self::ACTION_INSERT . $this->table . ' (`' . implode('`, `', $keys) . '`) ' . "VALUES ($values)";

            if(!$this->query($sql, $fields)->error()) {
                return $this->_pdo->lastInsertId();
            }
        }

        return false;
    }

    public function update($fields)
    {
       $set = '';
       $counter = 1;

       foreach($fields as $name => $value) {
           $set .= "{$name} = ?";
           if($counter < count($fields)) {
            $set .= ', ';
            }
            $counter++;
       }
       
       $sql = self::ACTION_UPDATE . $this->table . " SET {$set} " . $this->_where;
    
       $this->_values = array_merge($fields, $this->_values);

       if(!$this->query($sql, $this->_values)->error()) {
            return true;
        }

       return false;
    }

    public function results()
    {
        return $this->_results;
    }

    public function first()
    {
        if($this->exists()) {
            return $this->results()[0];
        }
        return false;
    }

    public function error()
    {
        return $this->_error;
    }

    public function count()
    {
        return $this->_count;
    }

    public function exists()
    {
        if($this->get()) {
            return true;
        }
        return false;
    }

    public function find($id, $field = 'id')
    {
        return $this->select()->table()->where($field, '=', $id)->first();
    }
    
}
