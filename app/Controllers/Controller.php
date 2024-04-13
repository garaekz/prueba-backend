<?php

namespace App\Controllers;

/**
 * The Controller class is a base class for all controllers in the application.
 * It provides common methods for returning JSON responses.
 */
class Controller
{
    /**
     * Returns a JSON response with the given data and status code.
     *
     * @param mixed $data The data to be included in the response.
     * @param int $status The HTTP status code of the response. Default is 200.
     * 
     * @return void
     */
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

    /**
     * Returns a paginated JSON response with the given data and status code.
     *
     * @param array $data The paginated data to be included in the response.
     * @param int $status The HTTP status code of the response. Default is 200.
     * 
     * @return void
     */
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
