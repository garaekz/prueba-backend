<?php

namespace Garaekz\Http;

use Garaekz\Exceptions\ValidationException;
use Garaekz\Services\Database;

/**
 * Abstract class representing a form request.
 * This class provides methods for validating form data based on defined rules.
 */
abstract class FormRequest extends Request
{
    /**
     * Array to store validation errors.
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Get the validation rules for the form request.
     *
     * @return array
     */
    abstract public function rules();

    /**
     * Validate the form request data.
     * Throws a ValidationException if validation fails.
     *
     * @throws ValidationException
     */
    public function validate()
    {
        try {
            $rules = $this->rules();
            foreach ($rules as $field => $conditions) {
                foreach ($conditions as $condition) {
                    $this->validateCondition($field, $condition);
                }
            }

            if (!empty($this->errors)) {
                throw new ValidationException($this->errors);
            }
        } catch (ValidationException $e) {
            throw $e;
        }
    }

    /**
     * Validate a single condition for a field.
     *
     * @param string $field
     * @param string $condition
     */
    private function validateCondition($field, $condition)
    {
        match (true) {
            $condition === 'required' && !$this->has($field) => $this->addError($field, "$field is required"),
            str_starts_with($condition, 'min:') && $this->input($field) !== null => $this->validateMinLength($field, $condition),
            $condition === 'email' && $this->input($field) !== null && !filter_var($this->input($field), FILTER_VALIDATE_EMAIL) => $this->addError($field, "$field must be a valid email address"),
            str_starts_with($condition, 'unique:') => $this->validateUnique($field, $condition),
            default => null,
        };
    }

    /**
     * Validate the minimum length of a field.
     *
     * @param string $field
     * @param string $condition
     */
    private function validateMinLength($field, $condition)
    {
        $min = substr($condition, 4);
        if (strlen($this->input($field, '')) < $min) {
            $this->addError($field, "$field must be at least $min characters long");
        }
    }

    /**
     * Add an error message for a field.
     *
     * @param string $field
     * @param string $error
     */
    private function addError($field, $error)
    {
        $this->errors[$field][] = $error;
    }

    /**
     * Validate the uniqueness of a field.
     *
     * @param string $field
     * @param string $condition
     */
    private function validateUnique($field, $condition)
    {
        $pdo = Database::getInstance()->getConnection();
        [$table, $column] = explode(',', substr($condition, 7));
        $query = $pdo->prepare("SELECT COUNT(*) FROM $table WHERE $column = ?");
        $query->execute([$this->input($field)]);
        $count = $query->fetchColumn();
        if ($count > 0) {
            $this->addError($field, "$field must be unique");
        }
    }

    /**
     * Get the validated data from the form request.
     *
     * @return array
     */
    public function validated()
    {
        $validatedData = [];
        foreach (array_keys($this->rules()) as $field) {
            if ($this->has($field)) {
                $validatedData[$field] = $this->input($field);
            }
        }
        return $validatedData;
    }
}
