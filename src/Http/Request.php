<?php

namespace Garaekz\Http;

/**
 * Represents an HTTP request.
 */
class Request
{
    /**
     * The query parameters.
     *
     * @var array
     */
    protected $query;

    /**
     * The request parameters.
     *
     * @var array
     */
    protected $request;

    /**
     * The uploaded files.
     *
     * @var array
     */
    protected $files;

    /**
     * The server parameters.
     *
     * @var array
     */
    protected $server;

    /**
     * The parsed request data.
     *
     * @var mixed
     */
    protected $data;

    /**
     * Create a new Request instance.
     *
     * Initializes the query, request, files, and server parameters.
     * Parses the request data based on the request method and content type.
     *
     * @return void
     */
    public function __construct()
    {
        $this->query = $_GET;
        $this->request = $_POST;
        $this->files = $_FILES;
        $this->server = $_SERVER;

        $contentType = $this->server['CONTENT_TYPE'] ?? $this->server['HTTP_CONTENT_TYPE'] ?? '';

        if ($this->server('REQUEST_METHOD', 'POST') == 'PUT') {
            $inputContent = file_get_contents('php://input');
            if (stripos($contentType, 'application/json') !== false) {
                $this->data = json_decode($inputContent, true) ?? [];
            } elseif (stripos($contentType, 'application/x-www-form-urlencoded') !== false) {
                parse_str($inputContent, $this->data);
            } else {
                $this->data = $inputContent;
            }
        } else {
            $this->data = json_decode(file_get_contents('php://input'), true) ?? [];
        }
    }

    /**
     * Get all the request parameters.
     *
     * @return array
     */
    public function all()
    {
        return array_merge($this->query, $this->request, $this->data);
    }

    /**
     * Get the value of a request parameter.
     *
     * @param string $key
     * @param mixed $default
     * 
     * @return mixed
     */
    public function input($key, $default = null)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        if (isset($this->request[$key])) {
            return $this->request[$key];
        }
        if (isset($this->query[$key])) {
            return $this->query[$key];
        }
        return $default;
    }

    /**
     * Check if a request parameter exists.
     *
     * @param string $key
     * 
     * @return bool
     */
    public function has($key)
    {
        return isset($this->data[$key]) || isset($this->query[$key]) || isset($this->request[$key]);
    }

    /**
     * Get the uploaded file with the given key.
     *
     * @param string $key
     * 
     * @return mixed|null
     */
    public function file($key)
    {
        return $this->files[$key] ?? null;
    }

    /**
     * Get the value of a server parameter.
     *
     * @param string $key
     * @param mixed $default
     * 
     * @return mixed
     */
    public function server($key, $default = null)
    {
        return $this->server[$key] ?? $default;
    }

    /**
     * Set the request data.
     * 
     * @param array $data
     * 
     * @return void
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * Manually set the request method.
     * 
     * @return array
     */
    public function setMethod($method)
    {
        $this->server['REQUEST_METHOD'] = $method;
    }
}
