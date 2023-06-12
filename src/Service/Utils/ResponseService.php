<?php

declare(strict_types=1);

namespace App\Service\Utils;

use App\DTO\BaseResponse\BaseResponseErrorDTO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ResponseService implements ResponseServiceInterface
{
    public function createSuccessResponse(mixed $dto, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse($dto, $statusCode);
    }

    /** @inheritDoc */
    public function createErrorResponse(int $statusCode = Response::HTTP_BAD_REQUEST, string $message = 'Something went wrong', array $errors = [], int $code = 0): JsonResponse
    {
        $code = $statusCode;
        return new JsonResponse(new BaseResponseErrorDTO($code, $message, $errors), $statusCode);
    }
}
