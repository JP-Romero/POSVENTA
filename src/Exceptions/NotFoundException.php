<?php

namespace App\Exceptions;

class NotFoundException extends AppException
{
    protected $statusCode = 404;

    public function __construct(string $message = 'Recurso no encontrado', ?Throwable $previous = null)
    {
        parent::__construct($message, 404, $previous);
    }
}
