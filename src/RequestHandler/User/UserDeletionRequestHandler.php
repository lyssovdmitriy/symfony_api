<?php

declare(strict_types=1);

namespace App\RequestHandler\User;

use App\DTO\BaseResponse\BaseResponseSuccessDTO;
use App\Service\User\UserDeletionServiceInterface;
use App\Service\Utils\ResponseServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

final class UserDeletionRequestHandler extends BaseUserRequestHandler implements UserDeletionRequestHandlerInterface
{
    public function __construct(
        private UserDeletionServiceInterface $userDeletionService,
        private ResponseServiceInterface $responseSerivce,
    ) {
    }

    public function handle(int $id): JsonResponse
    {
        $this->userDeletionService->deleteUser($id);
        return $this->responseSerivce->createSuccessResponse(
            new BaseResponseSuccessDTO()
        );
    }
}
