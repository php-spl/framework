<?php

namespace Spl\Database;

use PDO;

class Query
{
    // actions
    const ACTION_INSERT = "INSERT";
    const ACTION_INSERT_REPLACE = "INSERT OR REPLACE";
    const ACTION_UPDATE = "UPDATE";
    const ACTION_DELETE = "DELETE";
    const ACTION_SELECT = "SElECT";
    
    protected $pdo;
    protected $mode = PDO::FETCH_OBJ;
    protected $results;
    protected $count = 0;
    protected $error = false;

    protected $inputParams = [];
    protected $fieldsFromInput = [];
    protected $inputIsAssoc = true; 

    protected $action = "";
    protected $fields = [];
    protected $table = "";

    protected $join = [];
    protected $lastJoinId = -1;
    protected $onClauses = []; 

    protected $where = [];
    protected $having = [];

    protected $orderBy = [];
    protected $groupBy = [];

    protected $limit = "";
    protected $offset = "";

    public function setPdo(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function __construct(\PDO $pdo = null)
    {
        $this->pdo = $pdo;
        
        if(empty($this->table)) {
            $this->table = $this->getChildModel();
        }
    }

    protected function getChildModel()
    {
        $model = str_replace('Model', '', get_called_class());
        return strtolower(array_pop(explode('\\', $model))) . 's';
    }

    /**
     * @param string|string[] $fields
     */
    public function insert($fields = null, string $actionType = null): self
    {
        $this->action = $actionType ?: self::ACTION_INSERT;

        if (is_string($fields)) {
            $this->fields[] = $fields;
        } elseif (is_array($fields)) {
            // either an array of string (the fields)
            // either the input params (assoc array or array of arrays)
            if (isset($fields[0]) && is_string($fields[0])) {
                $this->fields = $fields;
            } else {
                $this->setInputParams($fields);
            }
        }

        return $this;
    }

    /**
     * @param string|string[] $fields
     */
    public function insertOrReplace($fields = null): self
    {
        return $this->insert($fields, self::ACTION_INSERT_REPLACE);
    }

    protected function buildInsertQueryString(): string
    {
        $fields = empty($this->fields) ? $this->fieldsFromInput : $this->fields;
        if (empty($fields)) {
            throw new \Exception("No field is set for INSERT action");
        }

        // build a single row
        $fieldsCount = count($fields);
        $rowParts = str_repeat( "?, ", $fieldsCount);
        if ($this->inputIsAssoc) {
            $rowParts = "";
            foreach ($fields as $field) {
                $rowParts .= ":$field, ";
            }
        }
        $row = "(" . substr($rowParts, 0, -2) . "), ";

        // build multiple row if needed
        $rows = $row; // for when inputParams contain only a single row
        $rowCount = count($this->inputParams) / $fieldsCount;
        if ($rowCount >= 2) { // multiple rows are inserted
            $rows = str_repeat($row, $rowCount);
        }

        return "$this->action INTO $this->table (" . implode(", ", $fields) .
            ") VALUES " . substr($rows, 0, -2);
    }

    /**
     * @param string|string[] $fields
     */
    public function update($fields = null): self
    {
        return $this->insert($fields, self::ACTION_UPDATE);
    }

    protected function buildUpdateQueryString(): string
    {
        $fields = empty($this->fields) ? $this->fieldsFromInput : $this->fields;
        if (empty($fields)) {
            throw new \Exception("No field is set for UPDATE action");
        }

        $query = "UPDATE $this->table SET ";

        foreach ($fields as $field) {
            if ($this->inputIsAssoc) {
                $query .= "$field = :$field, ";
            } else {
                $query .= "$field = ?, ";
            }
        }

        $query = substr($query, 0, -2) . " " . $this->buildWhereQueryString();
        return rtrim($query);
    }

    public function delete(): self
    {
        $this->action = self::ACTION_DELETE;
        return $this;
    }

    /**
     * @param string|string[] $fields
     */
    public function select($fields = null, string $alias = null): self
    {
        $this->action = self::ACTION_SELECT;

        if (is_string($fields)) {
            if ($alias !== null) {
                $fields .= " as $alias";
            }
            $this->fields[] = $fields;
        } elseif (is_array($fields)) {
            $this->fields = $fields;
        }

        return $this;
    }

    public function get(array $inputParams = null) 
    {
        $pdo = $this->execute($inputParams);

        if ($pdo->rowCount() > 0) {
            $this->results = $pdo->fetchAll($this->mode);
            $this->count = $pdo->rowCount();
            return $this;
        } else {
            return false;
        }
    }

    public function results()
    {
        return $this->results;
    }

    public function first()
    {
        if($this->count()) {
            return $this->results()[0];
        }
        return false;
    }

    public function error()
    {
        return $this->error;
    }

    public function count()
    {
        return $this->count;
    }

    public function exists()
    {
        if($this->get()) {
            return true;
        }
        return false;
    }

    public function find($id, $table = '', $field = 'id')
    {
        if(empty($table)) {
            $table = $this->table;
        }

        $pdo = $this->select()->table($table)->where($field, '=', $id)->get();
        
        if($pdo) {
            return $pdo->results();
        }

        return false;
    }

    protected function buildSelectQueryString(): string
    {
        $fields = $this->fields;
        if (empty($fields)) {
            $fields = "*";
        } else {
            $fields = implode(", ", $fields);
        }

        $query = "SELECT $fields FROM $this->table ";
        $query .= $this->buildJoinQueryString();
        $query .= $this->buildWhereQueryString();
        $query .= $this->buildGroupByQueryString();
        $query .= $this->buildHavingQueryString();
        $query .= $this->buildOrderByQueryString();
        $query .= $this->limit;
        $query .= $this->offset;

        return rtrim($query);
    }

    // table
    public function table(string $tableName): self
    {
        $this->table = $tableName; // no trailing space here !
        return $this;
    }

    public function inTable(string $tableName): self
    {
        return $this->table($tableName);
    }

    public function fromTable(string $tableName): self
    {
        return $this->table($tableName);
    }

    // join
    // on clauses by join id
    // unlike where and having
    // on is an array or conditional arrays
    protected function buildJoinQueryString(): string
    {
        $str = "";
        foreach ($this->join as $id => $joinTable) {
            $str .= $joinTable . "ON ";
            if (! isset($this->onClauses[$id]) || empty($this->onClauses[$id])) {
                throw new \Exception("Join statement without any ON clause: $joinTable");
            }
            $str .= $this->buildConditionalQueryString($this->onClauses[$id]) . " ";
        }
        return $str;
    }

    public function join(string $tableName, string $alias = null, string $joinType = null): self
    {
        if ($alias !== null) {
            $tableName .= " AS $alias";
        }

        if ($joinType === null) {
            $joinType = "JOIN";
        } else {
            $joinType .= " JOIN";
        }

        $this->join[] = "$joinType $tableName ";
        $this->lastJoinId++;
        return $this;
    }

    public function leftJoin(string $tableName, string $alias = null): self
    {
        return $this->join($tableName, $alias, "LEFT");
    }

    public function rightJoin(string $tableName, string $alias = null): self
    {
        return $this->join($tableName, $alias, "RIGHT");
    }

    public function fullJoin(string $tableName, string $alias = null): self
    {
        return $this->join($tableName, $alias, "FULL");
    }

    /**
     * @param string|callable $field
     */
    public function on($field, string $sign = null, string $value = null, string $cond = "AND"): self
    {
        if (! isset($this->onClauses[$this->lastJoinId])) {
            $this->onClauses[$this->lastJoinId] = [];
        }

        return $this->addConditionalClause($this->onClauses[$this->lastJoinId], $field, $sign, $value, $cond);
    }

    public function orOn($field, string $sign = null, $value = null): self
    {
        return $this->on($field, $sign, $value, "OR");
    }

    // where
    protected function buildWhereQueryString(): string
    {
        $where = $this->buildConditionalQueryString($this->where);
        if ($where !== "") {
            $where = "WHERE $where ";
        }
        return $where;
    }

    /**
     * @param string|callable $field
     */
    public function where($field, string $sign = null, string $value = null, string $cond = "AND"): self
    {
        return $this->addConditionalClause($this->where, $field, $sign, $value, $cond);
    }

    public function orWhere($field, string $sign = null, $value = null)
    {
        return $this->where($field, $sign, $value, "OR");
    }

    public function whereNull(string $field): self
    {
        return $this->where("$field IS NULL");
    }

    public function orWhereNull(string $field): self
    {
        return $this->orWhere("$field IS NULL");
    }

    public function whereNotNull(string $field): self
    {
        return $this->where("$field IS NOT NULL");
    }

    public function orWhereNotNull(string $field): self
    {
        return $this->orWhere("$field IS NOT NULL");
    }

    /**
     *
     * @param string|int $min
     * @param string|int $max
     */
    public function whereBetween(string $field, $min, $max): self
    {
        return $this->where("$field BETWEEN $min AND $max");
    }

    public function orWhereBetween(string $field, $min, $max): self
    {
        return $this->orWhere("$field BETWEEN $min AND $max");
    }

    public function whereNotBetween(string $field, $min, $max): self
    {
        return $this->where("$field NOT BETWEEN $min AND $max");
    }

    public function orWhereNotBetween(string $field, $min, $max): self
    {
        return $this->orWhere("$field NOT BETWEEN $min AND $max");
    }

    public function whereIn(string $field, array $values): self
    {
        $values = implode(", ", $values);
        return $this->where("$field IN ($values)");
    }

    public function orWhereIn(string $field, array $values): self
    {
        $values = implode(", ", $values);
        return $this->orWhere("$field IN ($values)");
    }

    public function whereNotIn(string $field, array $values): self
    {
        $values = implode(", ", $values);
        return $this->where("$field NOT IN ($values)");
    }

    public function orWhereNotIn(string $field, array $values): self
    {
        $values = implode(", ", $values);
        return $this->orWhere("$field NOT IN ($values)");
    }

    // order by, group by, having
    protected function buildOrderByQueryString(): string
    {
        if (empty($this->orderBy)) {
            return "";
        }
        return "ORDER BY " . implode(", ", $this->orderBy) . " ";
    }

    public function orderBy(string $field, string $direction = "ASC"): self
    {
        $direction = strtoupper($direction);
        $this->orderBy[] = "$field $direction";
        return $this;
    }

    protected function buildGroupByQueryString(): string
    {
        if (empty($this->groupBy)) {
            return "";
        }
        return "GROUP BY " . implode(", ", $this->groupBy) . " ";
    }

    /**
     * @param string|string[] $fields
     */
    public function groupBy($fields): self
    {
        if (is_string($fields)) {
            $this->groupBy[] = $fields;
        } elseif (is_array($fields)) {
            $this->groupBy = $fields;
        }
        return $this;
    }

    protected function buildHavingQueryString(): string
    {
        $having = $this->buildConditionalQueryString($this->having);
        if ($having === "") {
            return "";
        }
        return "HAVING $having ";
    }

    public function having(string $field, $sign = null, $value = null, string $cond = "AND"): self
    {
        return $this->addConditionalClause($this->having, $field, $sign, $value, $cond);
    }

    public function orHaving(string $field, $sign = null, $value = null): self
    {
        return $this->having($field, $sign, $value, "OR");
    }

    // limit offset
    public function limit(int $limit, int $offset = null): self
    {
        $this->limit = "LIMIT $limit ";
        if ($offset !== null) {
            $this->offset($offset);
        }
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = "OFFSET $offset ";
        return $this;
    }

    // non-query building methods
    public function prepare(): \PDOStatement
    {
        return $this->pdo->prepare($this->toString());
    }

    /**
     * @param array|null $inputParams
     * - an associative array of named parameters
     * - or an in-order array of parameters, when placeholders are ?
     * - an array of these two kinds fo array, which is useful to insert or update several rows with the same query
     *
     * @return bool|\PDOStatement|string
     * - `false` when the query is unsuccessful
     * - `true` when the query is successful and the action is `INSERT OR REPLACE`, `UPDATE` or `DELETE`.
     * - the last inserted id when the action is `INSERT`.
     * - the PDOStatement object when the action is `SELECT`.
     */
    public function execute(array $inputParams = null)
    {
        if ($inputParams !== null) {
            $this->setInputParams($inputParams);
        }

        $stmt = $this->pdo->prepare($this->toString());
        $success = $stmt->execute($this->inputParams);

        if (
            ! $success ||
            $this->action === self::ACTION_INSERT_REPLACE ||
            $this->action === self::ACTION_UPDATE ||
            $this->action === self::ACTION_DELETE
        ) {
            return $this->error = $success;
        }

        if ($this->action === self::ACTION_INSERT) {
            return $this->pdo->lastInsertId();
        }

        return $stmt; // ACTION_SELECT
    }

    public function isValid()
    {
        try {
            $this->pdo->prepare($this->toString());
        } catch (\PDOException $e) {
            return false;
        }
        return true;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        if ($this->action === self::ACTION_INSERT || $this->action === self::ACTION_INSERT_REPLACE) {
            return $this->buildInsertQueryString();
        }
        if ($this->action === self::ACTION_SELECT) {
            return $this->buildSelectQueryString();
        }
        if ($this->action === self::ACTION_UPDATE) {
            return $this->buildUpdateQueryString();
        }
        if ($this->action === self::ACTION_DELETE) {
            $query = "DELETE FROM $this->table " . $this->buildWhereQueryString();
            return rtrim($query);
        }
        return "QueryBuilder::toString() error: no action has been set";
    }

    // each clause entry is an array
    /*
    [
        "cond" => "AND" // "OR"
        "expr" => "expression"
    ]
    // or
    [
        "cond" => "AND" // "OR"
        "expr" => [
            [
                "cond" => "AND"
                expr => "expression"
            ],
            ...
        ]
    ]
    */
    protected function buildConditionalQueryString(array $clauses): string
    {
        if (empty($clauses)) {
            return "";
        }

        $str = "";
        foreach ($clauses as $id => $clause) {
            if ($id > 0) {
                $str .= $clause["cond"] . " ";
            }

            $expr = $clause["expr"];
            if (is_array($expr)) {
                $expr = "(" . $this->buildConditionalQueryString($expr) . ")";
            }
            $str .= "$expr ";
        }
        return rtrim($str);
    }

    /**
     * @param string|callable $field
     */
    protected function addConditionalClause(array &$clauses, $field, string $sign = null, string $value = null, $cond = "AND"): self
    {
        $clause = [
            "cond" => $cond,

            // either one expression as a string
            // or an array of clauses
            "expr" => "",
        ];

        if (is_callable($field)) {
            $beforeCount = count($clauses);
            $field($this);
            $afterCount = count($clauses);
            if ($afterCount === $beforeCount) {
                return $this;
            }
            $clause["expr"] = array_splice($clauses, $beforeCount);

        } elseif (is_array($field)) {
            foreach ($field as $fieldName => $value) {
                $this->addConditionalClause($clauses, "$fieldName = :$fieldName", null, null, $cond);
            }
            $this->setInputParams($field);
            return $this;

        } elseif ($sign === null && $value === null) {
            $clause["expr"] = $field;

        } elseif ($sign !== null && $value === null) {
            $clause["expr"] = "$field = " . $this->escapeValue($sign);

        } elseif ($sign !== null && $value !== null) {
            $clause["expr"] = "$field $sign " . $this->escapeValue($value);
        }

        $clauses[] = $clause;

        return $this;
    }

    protected function escapeValue($value): string
    {
        if (
            $this->pdo === null ||
            $value === "?" ||
            (is_string($value) && $value[0] === ":") // suppose named placeholder
        ) {
            return $value;
        }

        $quoted = $this->pdo->quote($value);
        if ($quoted === false) {
            return $value;
        }
        return $quoted;
    }
    
    /**
     * @param array $inputParams
     * @see QueryBuilder::execute();
     */
    protected function setInputParams(array $inputParams)
    {
        if (empty($inputParams)) {
            $this->inputIsAssoc = true;
            $this->inputParams = [];
            $this->fieldsFromInput = [];
            return;
        }

        // get format of input
        // and flatten it when needed
        $formattedInput = $inputParams;

        $keys = array_keys($inputParams);
        $this->inputIsAssoc = is_string($keys[0]);

        if ($this->inputIsAssoc) {
            // save fields from input when data is assoc array, if we need them later
            $this->fieldsFromInput = $keys;
        } elseif (is_array($inputParams[0])) {
            $keys = array_keys($inputParams[0]);
            if (is_string($keys[0])) {
                $this->fieldsFromInput = $keys;
                // input is assoc but will be flatten in a regular array just after
                // so don't set inputIsAssoc = true here
            }

            // flatten input
            $formattedInput = [];
            foreach ($inputParams as $params) {
                $formattedInput = array_merge($formattedInput, array_values($params));
            }
        }

        $this->inputParams = $formattedInput;
    }
}