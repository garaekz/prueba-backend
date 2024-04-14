<?php

namespace App\Controllers;

use Garaekz\Services\HttpResponse;

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
     * @return void|HttpResponse
     */
    protected function jsonResponse($data, $status = 200)
    {
        $response = new HttpResponse(
            json_encode(['status' => $status, 'data' => $data]),
            $status,
            ['Content-Type' => 'application/json']
        );

        if (env('APP_ENV') === 'testing') {
            return $response;
        } else {
            $response->send();
            exit;
        }
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
        return $this->jsonResponse([
            'items' => $data['data'],
            'meta' => [
                'current_page' => $data['current_page'],
                'per_page' => $data['per_page'],
                'total' => $data['total'],
            ]
        ], $status);
    }
}
