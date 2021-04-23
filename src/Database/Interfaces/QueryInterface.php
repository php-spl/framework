<?php

namespace Spl\Database\Interfaces;

interface QueryInterface
{
    /**
    * @var PDO
    */
    public function setPdo($pdo);

    public function getTableFromChildModelPlural(): string;

    /**
     * @param string|string[] $fields
     */
    public function insert($fields = null, string $actionType = null): self;

    /**
     * @param string|string[] $fields
     */
    public function insertOrReplace($fields = null): self;

    /**
     * @param string|string[] $fields
     */
    public function update($fields = null): self;

    public function delete(): self;

    /**
     * @param string|string[] $fields
     */
    public function select($fields = null, string $alias = null): self;

    public function table(string $tableName): self;

    public function inTable(string $tableName): self;

    public function fromTable(string $tableName): self;

    public function join(string $tableName, string $alias = null, string $joinType = null): self;

    public function leftJoin(string $tableName, string $alias = null): self;

    public function rightJoin(string $tableName, string $alias = null): self;

    public function innerJoin(string $tableName, string $alias = null): self;

    public function fullJoin(string $tableName, string $alias = null): self;

     /**
     * @param string|callable $field
     */
    public function on($field, string $sign = null, string $value = null, string $cond = "AND"): self;

    public function orOn($field, string $sign = null, $value = null): self;

    /**
     * @param string|callable $field
     */
    public function where($field, string $sign = null, string $value = null, string $cond = "AND"): self;

    public function orWhere($field, string $sign = null, $value = null);

    public function whereNull(string $field): self;

    public function orWhereNull(string $field): self;

    public function whereNotNull(string $field): self;

    public function orWhereNotNull(string $field): self;

    /**
     *
     * @param string|int $min
     * @param string|int $max
     */
    public function whereBetween(string $field, $min, $max): self;

    public function orWhereBetween(string $field, $min, $max): self;

    public function whereNotBetween(string $field, $min, $max): self;

    public function orWhereNotBetween(string $field, $min, $max): self;

    public function whereIn(string $field, array $values): self;

    public function orWhereIn(string $field, array $values): self;

    public function whereNotIn(string $field, array $values): self;

    public function orWhereNotIn(string $field, array $values): self;

    public function orderBy(string $field, string $direction = "ASC"): self;

    /**
     * @param string|string[] $fields
     */
    public function groupBy($fields): self;

    public function having(string $field, $sign = null, $value = null, string $cond = "AND"): self;

    public function orHaving(string $field, $sign = null, $value = null): self;

    public function limit(int $limit, int $offset = null): self;

    public function offset(int $offset): self;

    public function prepare(): \PDOStatement;

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
    public function execute(array $inputParams = null);

    public function isValid();

    public function toString(): string;

    public function has($options = null): bool;

    public function results(): array;

    public function save($options = null);

    public function get($options = null);

    public function first($options = null);

    public function error();

    public function count($options = null);

    public function lastId();
}