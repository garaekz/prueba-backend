<?php

namespace Garaekz\Services;

use Exception;
use PDO;

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

    public function __construct(PDO $pdo, $table)
    {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    public static function table(string $table): QueryBuilder
    {
        $pdo = Database::getInstance()->getConnection();
        return new self($pdo, $table);
    }

    public function find($id)
    {
        return $this->where('id', $id)->first();
    }

    public function select($columns = ['*'])
    {
        $this->columns = is_array($columns) ? implode(', ', $columns) : $columns;
        return $this;
    }

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

    public function orderBy($column, $direction = 'ASC')
    {
        $this->order = " ORDER BY $column $direction";
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = " LIMIT ?";
        $this->bindings[] = $limit;
        return $this;
    }

    public function offset($offset)
    {
        $this->offset = " OFFSET ?";
        $this->bindings[] = $offset;
        return $this;
    }

    public function get()
    {
        $where = !empty($this->wheres) ? " WHERE " . implode(' AND ', $this->wheres) : '';
        $sql = "SELECT {$this->columns} FROM {$this->table}{$where}{$this->order}{$this->limit}{$this->offset}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function first()
    {
        return $this->get()[0] ?? null;
    }

    public function count($column = '*')
    {
        $sql = "SELECT COUNT({$column}) FROM {$this->table} " . ($this->wheres ? ' WHERE ' . implode(' AND ', $this->wheres) : '');
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);
        return $stmt->fetchColumn();
    }

    public function insert($data)
    {
        $columns = implode(', ', array_keys($data));
        $values = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($values)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_values($data));
        return $this->pdo->lastInsertId();
    }

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

        // Merge the data and where bindings
        $bindings = array_merge(array_values($data), $this->bindings);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->rowCount();
    }

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
