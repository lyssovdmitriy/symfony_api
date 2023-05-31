<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

final class ApiException extends HttpException
{
    private int $statusCode;
    private array $errors;

    public function __construct(int $statusCode, string $message = null, array $errors = [], \Throwable $previous = null, array $headers = [], ?int $code = 0)
    {
        $this->statusCode = $statusCode;
        $this->errors = $errors;

        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
