<?php

namespace Garaekz\Exceptions;

use Exception;

/**
 * Represents an exception that occurs during validation.
 */
class ValidationException extends Exception
{
    /**
     * The validation errors.
     *
     * @var array
     */
    protected $errors;

    /**
     * Create a new validation exception instance.
     *
     * @param array $errors The validation errors.
     * @param string $message The exception message.
     * @param int $code The exception code.
     * 
     * @param Exception|null $previous The previous exception.
     */
    public function __construct(array $errors, $message = "Validation errors", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    /**
     * Get the validation errors.
     *
     * @return array The validation errors.
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
