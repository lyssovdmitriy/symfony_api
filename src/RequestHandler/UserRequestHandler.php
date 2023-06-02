<?php

declare(strict_types=1);

namespace App\RequestHandler;

use App\DTO\BaseResponse\BaseResponseSuccessDTO;
use App\DTO\User\UserResponseSuccessDTO;
use App\DTO\User\UserDTO;
use App\Entity\User;
use App\Exception\ApiException;
use App\Service\PopulateService;
use App\Service\ResponseService;
use App\Service\UserService;
use App\Service\ValidationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
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
        private UserService $userService,
        private ResponseService $responseService,
        private PopulateService $populateService,
    ) {
    }

    public function createUser(Request $request): JsonResponse
    {
        $dto = $this->deserializeUserDTO($request);
        $validationErrors = $this->validationService->validateDTO($dto);

        if (count($validationErrors) > 0) {
            throw new ApiException(self::USER_CREATE_VALIDATION_ERROR, 'Validation failed', $validationErrors);
        }

        /** @var UserDTO */
        $userDTO = $this->populateService->populateDTOFromEntity($this->userService->createUser($dto), UserDTO::class, ['get']);
        $responseDto = new UserResponseSuccessDTO($userDTO);
        return $this->responseService->createSuccessResponse($responseDto, self::USER_CREATE_SUCCESS);
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
                $this->populateService->populateDTOFromEntity(
                    $this->userService->getUser($id),
                    UserDTO::class,
                    ['get', 'user', 'application_link', 'application']
                )
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
        $user = $this->userService->getUser($id);

        $this->userService->updateUser($user, $userDto);

        return $this->responseService->createSuccessResponse(
            new UserResponseSuccessDTO(
                $this->populateService->populateDTOFromEntity(
                    $user,
                    UserDTO::class,
                    ['get']
                )
            )
        );
    }


    public function deleteUser(int $id): JsonResponse
    {
        $this->userService->deleteUser($id);
        return $this->responseService->createSuccessResponse(new BaseResponseSuccessDTO());
    }
}
