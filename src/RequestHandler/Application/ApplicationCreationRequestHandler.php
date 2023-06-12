<?php

declare(strict_types=1);

namespace App\RequestHandler\Application;

use App\DTO\Application\ApplicationResponseSuccessDTO;
use App\Serializer\DataSerializerInterface;
use App\Service\Application\ApplicationCreationServiceInterface;
use App\Service\Utils\ResponseServiceInterface;
use App\Service\Utils\ValidationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class ApplicationCreationRequestHandler extends BaseApplicationRequestHandler implements ApplicationCreationRequestHandlerInterface
{

    public function __construct(
        private DataSerializerInterface $serializer,
        private ValidationService $validationService,
        private ResponseServiceInterface $responseService,
        private ApplicationCreationServiceInterface $applicationCreationService,
    ) {
    }

    public function handle(Request $request, int $userId): JsonResponse
    {
        $dto = $this->deserializeApplicationDTO($this->serializer, $request);
        $this->validateDTO($this->validationService, $dto, JsonResponse::HTTP_UNPROCESSABLE_ENTITY);

        return $this->responseService->createSuccessResponse(
            new ApplicationResponseSuccessDTO(
                $this->applicationCreationService->createApplication($dto, $userId)
            ),
            JsonResponse::HTTP_CREATED
        );
    }
}
