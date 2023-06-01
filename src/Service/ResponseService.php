<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\BaseResponse\BaseResponseErrorDTO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ResponseService
{
    public function createSuccessResponse(object $dto, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse($dto, $statusCode);
    }

    /**
     * @param int $statusCode
     * @param string $message
     * @param mixed[] $errors
     * @param integer $code
     * @return JsonResponse
     */
    public function createErrorResponse(int $statusCode = Response::HTTP_BAD_REQUEST, string $message = 'Something went wrong', array $errors = [], int $code = 0): JsonResponse
    {
        return new JsonResponse(new BaseResponseErrorDTO($code, $message, $errors), $statusCode);
    }
}
