<?php

namespace App\Controllers;

class Controller
{
    protected function jsonResponse($data, $status = 200)
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode([
            'status' => $status,
            'data' => $data
        ]);
        exit;
    }

    protected function jsonPaginate($data, $status = 200)
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode([
            'status' => $status,
            'data' => $data['data'],
            'meta' => [
                'current_page' => $data['current_page'],
                'per_page' => $data['per_page'],
                'total' => $data['total'],
            ]
        ]);
        exit;
    }
}
