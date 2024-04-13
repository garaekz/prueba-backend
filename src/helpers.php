<?php

if (!function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @param  mixed  ...$args
     * @return mixed
     */
    function value($value, ...$args)
    {
        return $value instanceof Closure ? $value(...$args) : $value;
    }
}

if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    function env($key, $default = null)
    {
        $value = $_ENV[$key] ?? getenv($key);
        return $value === false ? $default : $value;
    }
}

if (!function_exists('bcrypt')) {
    /**
     * Hash the given value.
     *
     * @param  string  $value
     * @return string
     */
    function bcrypt($value)
    {
        return password_hash($value, PASSWORD_DEFAULT);
    }
}
