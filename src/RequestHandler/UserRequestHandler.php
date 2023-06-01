<?php

declare(strict_types=1);

namespace App\RequestHandler;

use App\DTO\User\UserCreateResponseSuccessDTO;
use App\DTO\User\UserDTO;
use App\Entity\User;
use App\Exception\ApiException;
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
    const USER_NOT_FOUNT = Response::HTTP_NOT_FOUND;
    const USER_UPDATE_VALIDATION_ERROR = Response::HTTP_UNPROCESSABLE_ENTITY;


    public function __construct(
        private SerializerInterface $serializer,
        private ValidationService $validationService,
        private UserService $userService,
        private ResponseService $responseService,
    ) {
    }

    public function createUser(Request $request): JsonResponse
    {
        try {
            $dto = $this->deserializeUserDTO($request);
        } catch (\Throwable $th) {
            throw new ApiException(self::USER_CREATE_ERROR, $th->getMessage(), ['JSON parsing error: Invalid JSON syntax'], $th);
        }

        $validationErrors = $this->validationService->validateUserDTO($dto);

        if (count($validationErrors) > 0) {
            throw new ApiException(self::USER_CREATE_VALIDATION_ERROR, 'Validation failed', $validationErrors);
        }

        $user = $this->userService->createUser($dto);
        $responseDto = new UserCreateResponseSuccessDTO($this->populateDTOFromEntity($user, UserDTO::class, ['get']));
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

    /**
     * @param User $entity
     * @param string $type
     * @param string[] $groups
     * @return UserDTO
     */
    private function populateDTOFromEntity(User $entity, string $type, array $groups): UserDTO
    {
        /** @var UserDTO $dto */
        $dto = $this->serializer->deserialize(
            $this->serializer->serialize($entity, 'json'),
            $type,
            'json',
            [AbstractNormalizer::GROUPS => $groups]
        );

        return $dto;
    }

    public function getUser(int $id): JsonResponse
    {
        return $this->responseService->createSuccessResponse(
            $this->populateDTOFromEntity(
                $this->userService->getUser($id),
                UserDTO::class,
                ['get']
            ),
        );
    }


    public function updateUser(int $id, Request $request): JsonResponse
    {
        $userDto = $this->deserializeUserDTO($request);
        $validationErrors = $this->validationService->validateUserDTO($userDto);
        if (count($validationErrors) > 0) {
            throw new ApiException(self::USER_UPDATE_VALIDATION_ERROR, 'Validation failed', $validationErrors);
        }
        $user = $this->userService->getUser($id);

        $this->userService->updateUser($user, $userDto);

        return $this->responseService->createSuccessResponse(
            $this->populateDTOFromEntity(
                $user,
                UserDTO::class,
                ['get']
            ),
        );
    }
}
