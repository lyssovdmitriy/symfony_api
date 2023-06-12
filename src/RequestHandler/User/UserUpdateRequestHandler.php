<?php

declare(strict_types=1);

namespace App\RequestHandler\User;

use App\DTO\BaseResponse\BaseResponseSuccessDTO;
use App\Serializer\DataSerializerInterface;
use App\Service\User\UserUpdateServiceInterface;
use App\Service\Utils\ResponseServiceInterface;
use App\Service\Utils\ValidationServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


final class UserUpdateRequestHandler extends BaseUserRequestHandler implements UserUpdateRequestHandlerInterface
{

    public function __construct(
        private DataSerializerInterface $serializer,
        private ValidationServiceInterface $validationService,
        private UserUpdateServiceInterface $userUpdateService,
        private ResponseServiceInterface $responseService,
    ) {
    }

    public function handle(int $id, Request $request): JsonResponse
    {
        $userDto = $this->deserializeUserDTO($this->serializer, $request);
        $this->validateDTO($this->validationService, $userDto, JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        $this->userUpdateService->updateUser($id, $userDto);

        return $this->responseService->createSuccessResponse(
            new BaseResponseSuccessDTO
        );
    }
}
