<?php

declare(strict_types=1);

namespace App\RequestHandler\User;

use App\DTO\BaseResponse\BaseResponseSuccessDataDTO;
use App\Service\User\UserListServiceInterface;
use App\Service\Utils\ResponseServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

final class UserListRequestHandler extends BaseUserRequestHandler implements UserListRequestHandlerInterface
{
    public function __construct(
        private UserListServiceInterface $userListService,
        private ResponseServiceInterface $responseSerivce,
    ) {
    }

    public function handle(int $offset, int $limit): JsonResponse
    {
        $offset = $offset < 0 ? 0 : $offset;
        $limit = $limit < 0 ? 10 : $limit;

        return $this->responseSerivce->createSuccessResponse(
            new BaseResponseSuccessDataDTO(
                $this->userListService->getUsers($offset, $limit)
            )
        );
    }
}
