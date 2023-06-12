<?php

declare(strict_types=1);

namespace App\RequestHandler\User;

use App\DTO\User\UserResponseSuccessDTO;
use App\Serializer\DataSerializerInterface;
use App\Service\User\UserCreationServiceInterface;
use App\Service\Utils\ResponseServiceInterface;
use App\Service\Utils\ValidationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class UserCreationRequestHandler extends BaseUserRequestHandler implements UserCreationRequestHandlerInterface
{

    public function __construct(
        private DataSerializerInterface $serializer,
        private ValidationService $validationService,
        private ResponseServiceInterface $responseService,
        private UserCreationServiceInterface $userCreationService,
    ) {
    }

    public function handle(Request $request): JsonResponse
    {
        $dto = $this->deserializeUserDTO($this->serializer, $request);
        $this->validateDTO($this->validationService, $dto, JsonResponse::HTTP_UNPROCESSABLE_ENTITY);

        return $this->responseService->createSuccessResponse(
            new UserResponseSuccessDTO(
                $this->userCreationService->createUser($dto)
            ),
            JsonResponse::HTTP_CREATED
        );
    }
}
