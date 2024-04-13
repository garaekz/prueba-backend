<?php

namespace Garaekz\Exceptions;

class ErrorHandler
{
    /**
     * Registra los manejadores globales para errores y excepciones.
     */
    public static function register()
    {
        error_reporting(E_ALL);
        set_exception_handler([self::class, 'handleException']);
        set_error_handler([self::class, 'handleError']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    /**
     * Maneja excepciones no capturadas.
     */
    public static function handleException($exception)
    {
        http_response_code(500);
        header('Content-Type: application/json');

        if ($exception instanceof ValidationException) {
            http_response_code(422);
            echo json_encode([
                'error' => [
                    'status' => 422, // Unprocessable Entity
                    'message' => $exception->getMessage(),
                    'errors' => $exception->getErrors()
                ]
            ]);
        } else {
            echo json_encode([
                'error' => [
                    'status' => 500, // Internal Server Error
                    'message' => 'Internal Server Error',
                    'details' => $exception->getMessage()
                ]
            ]);
        }

        exit;
    }

    /**
     * Convierte errores de PHP en excepciones.
     */
    public static function handleError($severity, $message, $file, $line)
    {
        if (!(error_reporting() & $severity)) {
            return;
        }
        throw new \ErrorException($message, 0, $severity, $file, $line);
    }

    /**
     * Maneja errores críticos que ocurren en el shutdown script.
     */
    public static function handleShutdown()
    {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
            self::handleError($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }
}
