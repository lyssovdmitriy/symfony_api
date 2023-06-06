<?php

declare(strict_types=1);

namespace App\RequestHandler;

use App\DTO\BaseResponse\BaseResponseSuccessDataDTO;
use App\DTO\BaseResponse\BaseResponseSuccessDTO;
use App\DTO\User\UserResponseSuccessDTO;
use App\DTO\User\UserDTO;
use App\Exception\ApiException;
use App\Service\PopulateService;
use App\Service\ResponseService;
use App\Service\User\UserServiceInterface;
use App\Service\ValidationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

final class UserRequestHandler
{

    const USER_CREATE_SUCCESS = Response::HTTP_CREATED;
    const USER_CREATE_ERROR = Response::HTTP_BAD_REQUEST;
    const USER_CREATE_VALIDATION_ERROR = Response::HTTP_UNPROCESSABLE_ENTITY;
    const USER_CREATE_CONFLICT_ERROR = Response::HTTP_CONFLICT;
    const USER_GET_SUCCESS = Response::HTTP_OK;
    const USER_NOT_FOUND = Response::HTTP_NOT_FOUND;
    const USER_UPDATE_VALIDATION_ERROR = Response::HTTP_UNPROCESSABLE_ENTITY;


    public function __construct(
        private SerializerInterface $serializer,
        private ValidationService $validationService,
        private UserServiceInterface $userService,
        private ResponseService $responseService,
    ) {
    }

    public function createUser(Request $request): JsonResponse
    {
        $dto = $this->deserializeUserDTO($request);
        $validationErrors = $this->validationService->validateDTO($dto);

        if (count($validationErrors) > 0) {
            throw new ApiException(self::USER_CREATE_VALIDATION_ERROR, 'Validation failed', $validationErrors);
        }

        return $this->responseService->createSuccessResponse(
            new UserResponseSuccessDTO(
                $this->userService->createUser($dto)
            ),
            self::USER_CREATE_SUCCESS
        );
    }

    private function deserializeUserDTO(Request $request): UserDTO
    {
        try {
            $dto = $this->serializer->deserialize($request->getContent(), UserDTO::class, 'json');
        } catch (\Throwable $th) {
            throw new ApiException(self::USER_CREATE_ERROR, $th->getMessage(), ['JSON parsing error: Invalid JSON syntax'], $th);
        }
        return $dto;
    }

    public function getUser(int $id): JsonResponse
    {
        return $this->responseService->createSuccessResponse(
            new UserResponseSuccessDTO(
                $this->userService->getUser($id)
            )
        );
    }


    public function updateUser(int $id, Request $request): JsonResponse
    {
        $userDto = $this->deserializeUserDTO($request);
        $validationErrors = $this->validationService->validateDTO($userDto);
        if (count($validationErrors) > 0) {
            throw new ApiException(self::USER_UPDATE_VALIDATION_ERROR, 'Validation failed', $validationErrors);
        }

        $userDTO = $this->userService->updateUser($id, $userDto);

        return $this->responseService->createSuccessResponse(
            new UserResponseSuccessDTO($userDTO)
        );
    }


    public function deleteUser(int $id): JsonResponse
    {
        $this->userService->deleteUser($id);
        return $this->responseService->createSuccessResponse(new BaseResponseSuccessDTO());
    }


    public function getUsers(int $offset, int $limit): JsonResponse
    {
        $offset = $offset < 0 ? 0 : $offset;
        $limit = $limit < 0 ? 10 : $limit;

        $users = $this->userService->getUsers($offset, $limit);

        return $this->responseService->createSuccessResponse(new BaseResponseSuccessDataDTO($users));
    }
}
