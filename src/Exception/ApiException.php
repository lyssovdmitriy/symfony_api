<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

final class ApiException extends HttpException
{
    private int $statusCode;
    /**
     * @var mixed[]
     */
    private array $errors;

    /**
     * @param integer $statusCode
     * @param string $message
     * @param mixed[] $errors
     * @param \Throwable|null $previous
     * @param mixed[] $headers
     * @param integer $code
     */
    public function __construct(int $statusCode, string $message = '', array $errors = [], \Throwable $previous = null, array $headers = [], int $code = 0)
    {
        $this->statusCode = $statusCode;
        $this->errors = $errors;

        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return mixed[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
