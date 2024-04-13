<?php

namespace App\Models;

use Exception;
use Garaekz\Services\QueryBuilder;

/**
 * This is the base model class that other models can extend from.
 */
abstract class Model
{
    /**
     * The table name associated with the model.
     *
     * @var string
     */
    protected static $table;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get a new query builder instance for the model's table.
     *
     * @return QueryBuilder
     */
    protected static function query(): QueryBuilder
    {
        return QueryBuilder::table(static::getTable());
    }

    /**
     * Get the table associated with the model or infer it from the class name.
     *
     * @return string
     */
    protected static function getTable(): string
    {
        if (static::$table) {
            return static::$table;
        }

        $className = join('', array_slice(explode('\\', get_called_class()), -1));
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className));
    }

    /**
     * Filter the given data by removing hidden attributes.
     *
     * @param array $data
     * 
     * @return array
     */
    protected function filterAttributes(array $data)
    {
        foreach ($this->hidden as $hidden) {
            unset($data[$hidden]);
        }
        return $data;
    }

    /**
     * Find a model by its primary key.
     *
     * @param mixed $id
     * 
     * @return mixed|null
     */
    public static function find($id)
    {
        $result = static::query()->find($id);
        if (!$result) {
            return null;
        }
        return (new static)->filterAttributes($result);
    }

    /**
     * Find a model by its primary key or throw an exception.
     *
     * @param mixed $id
     * 
     * @return mixed
     * @throws Exception
     */
    public static function findOrFail($id)
    {
        $result = static::find($id);

        if (!$result) {
            throw new Exception('Resource not found');
        }

        return $result;
    }

    /**
     * Get all the models from the database.
     *
     * @param array $columns
     * 
     * @return array
     */
    public static function all($columns = ['*'])
    {
        $results = static::query()->select($columns)->get();
        return array_map(function ($item) {
            return (new static)->filterAttributes($item);
        }, $results);
    }

    /**
     * Paginate the models from the database.
     *
     * @param int $page
     * @param int $perPage
     * @param array $columns
     * 
     * @return array
     */
    public static function paginate($page = 1, $perPage = 15, $columns = ['*'])
    {
        $query = static::query()->select($columns);
        $total = $query->count('id');
        $results = $query->limit($perPage)->offset(($page - 1) * $perPage)->get();

        $filteredResults = array_map(function ($item) {
            return (new static)->filterAttributes($item);
        }, $results);

        return [
            'data' => $filteredResults,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
        ];
    }

    /**
     * Create a new model instance and save it to the database.
     *
     * @param array $data
     * @return mixed
     */
    public static function create($data)
    {
        $id = static::query()->insert($data);
        return static::find($id);
    }

    /**
     * Update the model in the database.
     *
     * @param mixed $id
     * @param array $data
     * @return mixed|null
     */
    public static function update($id, $data)
    {
        $updated = static::query()->where('id', $id)->update($data);
        if (!$updated) {
            return null;
        }
        return static::find($id);
    }

    /**
     * Delete the model from the database.
     *
     * @param mixed $id
     * @return mixed
     */
    public static function destroy($id)
    {
        return static::query()->where('id', $id)->delete();
    }
}
