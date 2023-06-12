<?php

declare(strict_types=1);

namespace App\RequestHandler\Application;

use App\DTO\Application\ApplicationResponseSuccessDTO;
use App\Service\Application\ApplicationRetrievalServiceInterface;
use App\Service\Utils\ResponseServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ApplicationRetrievalRequestHandler extends BaseApplicationRequestHandler implements ApplicationRetrievalRequestHandlerInterface
{
    public function __construct(
        private ApplicationRetrievalServiceInterface $applicationRetrievalService,
        private ResponseServiceInterface $responseSerivce,
    ) {
    }

    public function handle(int $id): JsonResponse
    {
        $applicationDTO = $this->applicationRetrievalService->getApplicationById($id);
        if (isset($applicationDTO->file)) {
            $applicationDTO->file = "applications/{$applicationDTO->id}/file";
        }

        return $this->responseSerivce->createSuccessResponse(
            new ApplicationResponseSuccessDTO($applicationDTO)
        );
    }
}
