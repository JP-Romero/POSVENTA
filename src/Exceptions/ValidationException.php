<?php

namespace App\Exceptions;

class ValidationException extends AppException
{
    protected $statusCode = 422;
    protected array $errors = [];

    public function __construct(array $errors, string $message = 'Error de validación', ?Throwable $previous = null)
    {
        parent::__construct($message, 422, $previous);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
