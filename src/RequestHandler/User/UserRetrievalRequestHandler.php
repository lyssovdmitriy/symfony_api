<?php

declare(strict_types=1);

namespace App\RequestHandler\User;

use App\DTO\User\UserResponseSuccessDTO;
use App\Service\User\UserRetrievalServiceInterface;
use App\Service\Utils\ResponseServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

final class UserRetrievalRequestHandler extends BaseUserRequestHandler implements UserRetrievalRequestHandlerInterface
{
    public function __construct(
        private UserRetrievalServiceInterface $userRetrievalService,
        private ResponseServiceInterface $responseSerivce,
    ) {
    }

    public function handle(int $id): JsonResponse
    {
        return $this->responseSerivce->createSuccessResponse(
            new UserResponseSuccessDTO(
                $this->userRetrievalService->getUser($id)
            )
        );
    }
}
