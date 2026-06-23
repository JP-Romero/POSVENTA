<?php

namespace App\Exceptions;

use Exception;

class AppException extends Exception
{
    protected $statusCode = 500;

    public function __construct(string $message, int $statusCode = 500, ?Throwable $previous = null)
    {
        parent::__construct($message, $statusCode, $previous);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
