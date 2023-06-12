<?php

declare(strict_types=1);

namespace App\Service\Utils;

use App\Exception\ApiException;
use Symfony\Component\HttpFoundation\Response;

trait DTOValidationTrait
{
    protected function validateDTO(ValidationServiceInterface $validationService, object $dto, int $errorCode = Response::HTTP_BAD_REQUEST): void
    {
        $validationErrors = $validationService->validateDTO($dto);
        if (count($validationErrors) > 0) {
            throw new ApiException($errorCode, 'Validation failed', $validationErrors);
        }
    }
}
