<?php

declare(strict_types=1);

namespace App\RequestHandler;

use App\Exception\ApiException;
use App\Service\ValidationService;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractRequestHandler
{

    protected function validateDTO(ValidationService $validationService, object $dto, int $errorCode = Response::HTTP_BAD_REQUEST): void
    {
        $validationErrors = $validationService->validateDTO($dto);
        if (count($validationErrors) > 0) {
            throw new ApiException($errorCode, 'Validation failed', $validationErrors);
        }
    }
}
