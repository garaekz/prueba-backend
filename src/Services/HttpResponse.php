<?php

namespace Garaekz\Services;

class HttpResponse
{
    private $status;
    private $headers;
    private $body;

    public function __construct($body = '', $status = 200, array $headers = [])
    {
        $this->status = $status;
        $this->headers = $headers;
        $this->body = $body;
    }

    public function send()
    {
        http_response_code($this->status);
        foreach ($this->headers as $header => $value) {
            header("$header: $value");
        }
        echo $this->body;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getHeaders()
    {
        return $this->headers;
    }
}
