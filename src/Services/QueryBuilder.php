<?php

namespace Garaekz\Services;

use Exception;
use PDO;

/**
 * The QueryBuilder class provides a fluent interface for building and executing SQL queries.
 */
class QueryBuilder
{
    protected $pdo;
    protected $table;
    protected $columns = '*';
    protected $bindings = [];
    protected $wheres = [];
    protected $order = '';
    protected $limit = '';
    protected $offset = '';

    /**
     * Create a new QueryBuilder instance.
     *
     * @param PDO $pdo The PDO instance.
     * @param string $table The table name.
     */
    public function __construct(PDO $pdo, $table)
    {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    /**
     * Create a new QueryBuilder instance for the specified table.
     *
     * @param string $table The table name.
     * 
     * @return QueryBuilder The QueryBuilder instance.
     */
    public static function table(string $table): QueryBuilder
    {
        $pdo = Database::getInstance()->getConnection();
        return new self($pdo, $table);
    }

    /**
     * Find a record by its ID.
     *
     * @param mixed $id The ID of the record.
     * 
     * @return mixed The first record matching the ID, or null if not found.
     */
    public function find($id)
    {
        return $this->where('id', $id)->first();
    }

    /**
     * Set the columns to be selected.
     *
     * @param mixed $columns The columns to be selected. Defaults to all columns.
     * 
     * @return QueryBuilder The QueryBuilder instance.
     */
    public function select($columns = ['*'])
    {
        $this->columns = is_array($columns) ? implode(', ', $columns) : $columns;
        return $this;
    }

    /**
     * Add a WHERE clause to the query.
     *
     * @param string $column The column name.
     * @param string $operator The comparison operator. Defaults to '='.
     * @param mixed $value The value to compare against.
     * 
     * @return QueryBuilder The QueryBuilder instance.
     */
    public function where($column, $operator, $value = null)
    {
        if (func_num_args() == 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = "$column $operator ?";
        $this->bindings[] = $value;
        return $this;
    }

    /**
     * Add an ORDER BY clause to the query.
     *
     * @param string $column The column name.
     * @param string $direction The sort direction. Defaults to 'ASC'.
     * 
     * @return QueryBuilder The QueryBuilder instance.
     */
    public function orderBy($column, $direction = 'ASC')
    {
        $this->order = " ORDER BY $column $direction";
        return $this;
    }

    /**
     * Add a LIMIT clause to the query.
     *
     * @param int $limit The maximum number of rows to return.
     * 
     * @return QueryBuilder The QueryBuilder instance.
     */
    public function limit($limit)
    {
        $this->limit = " LIMIT ?";
        $this->bindings[] = $limit;
        return $this;
    }

    /**
     * Add an OFFSET clause to the query.
     *
     * @param int $offset The number of rows to skip.
     * 
     * @return QueryBuilder The QueryBuilder instance.
     */
    public function offset($offset)
    {
        $this->offset = " OFFSET ?";
        $this->bindings[] = $offset;
        return $this;
    }

    /**
     * Execute the query and return the results.
     *
     * @return array The query results as an associative array.
     */
    public function get()
    {
        $where = !empty($this->wheres) ? " WHERE " . implode(' AND ', $this->wheres) : '';
        $sql = "SELECT {$this->columns} FROM {$this->table}{$where}{$this->order}{$this->limit}{$this->offset}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Execute the query and return the first result.
     *
     * @return mixed The first result, or null if no results.
     */
    public function first()
    {
        return $this->get()[0] ?? null;
    }

    /**
     * Count the number of records matching the query.
     *
     * @param string $column The column to count. Defaults to all columns.
     * 
     * @return int The number of records matching the query.
     */
    public function count($column = '*')
    {
        $sql = "SELECT COUNT({$column}) FROM {$this->table} " . ($this->wheres ? ' WHERE ' . implode(' AND ', $this->wheres) : '');
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);
        return $stmt->fetchColumn();
    }

    /**
     * Insert a new record into the table.
     *
     * @param array $data The data to be inserted.
     * 
     * @return int The ID of the inserted record.
     */
    public function insert($data)
    {
        $columns = implode(', ', array_keys($data));
        $values = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($values)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_values($data));
        return $this->pdo->lastInsertId();
    }

    /**
     * Update records in the table.
     *
     * @param array $data The data to be updated.
     * 
     * @return int The number of affected rows.
     * @throws Exception If no WHERE clause is specified.
     */
    public function update($data)
    {
        if (empty($this->wheres)) {
            throw new Exception("No WHERE clause specified for update operation.");
        }

        $columns = array_keys($data);
        $setClause = array_map(function ($column) {
            return "$column = ?";
        }, $columns);

        $setClauseString = implode(', ', $setClause);
        $whereClauseString = implode(' AND ', $this->wheres);
        $sql = "UPDATE {$this->table} SET $setClauseString WHERE $whereClauseString";

        $bindings = array_merge(array_values($data), $this->bindings);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->rowCount();
    }

    /**
     * Delete records from the table.
     *
     * @return int The number of affected rows.
     * @throws Exception If no WHERE clause is specified.
     */
    public function delete()
    {
        if (empty($this->wheres)) {
            throw new Exception("No WHERE clause specified for delete operation");
        }

        $where = " WHERE " . implode(' AND ', $this->wheres);
        $sql = "DELETE FROM {$this->table}{$where}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);
        return $stmt->rowCount();
    }
}
