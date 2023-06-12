<?php

declare(strict_types=1);

namespace App\Service\Utils;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

interface ResponseServiceInterface
{
    public function createSuccessResponse(mixed $dto, int $statusCode = Response::HTTP_OK): JsonResponse;

    /**
     * @param int $statusCode
     * @param string $message
     * @param mixed[] $errors
     * @param integer $code
     * @return JsonResponse
     */
    public function createErrorResponse(int $statusCode = Response::HTTP_BAD_REQUEST, string $message = 'Something went wrong', array $errors = [], int $code = 0): JsonResponse;
}
