<?php

namespace App\Models;

use Garaekz\Services\QueryBuilder;

abstract class Model
{
    protected static $table;
    protected $hidden = [];

    protected static function query(): QueryBuilder
    {
        return QueryBuilder::table(static::getTable());
    }

    protected static function getTable(): string
    {
        if (static::$table) {
            return static::$table;
        }

        $className = join('', array_slice(explode('\\', get_called_class()), -1));
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className));
    }

    protected function filterAttributes(array $data)
    {
        foreach ($this->hidden as $hidden) {
            unset($data[$hidden]);
        }
        return $data;
    }

    public static function find($id)
    {
        $result = static::query()->find($id);
        if (!$result) {
            return null;
        }
        return (new static)->filterAttributes($result);
    }

    public static function findOrFail($id)
    {
        $result = static::find($id);

        if (!$result) {
            throw new \Exception('Resource not found');
        }

        return $result;
    }

    public static function all($columns = ['*'])
    {
        $results = static::query()->select($columns)->get();
        return array_map(function ($item) {
            return (new static)->filterAttributes($item);
        }, $results);
    }

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

    public static function create($data)
    {
        $id = static::query()->insert($data);
        return static::find($id);
    }

    public static function update($id, $data)
    {
        $updated = static::query()->where('id', $id)->update($data);
        if (!$updated) {
            return null;
        }
        return static::find($id);
    }

    public static function destroy($id)
    {
        return static::query()->where('id', $id)->delete();
    }
}
