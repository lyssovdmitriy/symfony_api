<?php

declare(strict_types=1);

namespace App\RequestHandler;

use App\DTO\BaseResponse\BaseResponseSuccessDataDTO;
use App\DTO\BaseResponse\BaseResponseSuccessDTO;
use App\DTO\User\UserResponseSuccessDTO;
use App\DTO\User\UserDTO;
use App\Exception\ApiException;
use App\Service\ResponseService;
use App\Service\User\UserServiceInterface;
use App\Service\ValidationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

final class UserRequestHandler extends AbstractRequestHandler
{

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
        $this->validateDTO($this->validationService, $dto, Response::HTTP_UNPROCESSABLE_ENTITY);

        return $this->createSuccessJsonResponse(
            new UserResponseSuccessDTO(
                $this->userService->createUser($dto)
            ),
            Response::HTTP_CREATED
        );
    }


    private function deserializeUserDTO(Request $request): UserDTO
    {
        try {
            $dto = $this->serializer->deserialize($request->getContent(), UserDTO::class, 'json');
        } catch (\Throwable $th) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $th->getMessage(), ['JSON parsing error: Invalid JSON syntax'], $th);
        }
        return $dto;
    }

    private function createSuccessJsonResponse(mixed $dto, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return $this->responseService->createSuccessResponse($dto, $statusCode);
    }

    public function getUser(int $id): JsonResponse
    {
        return $this->createSuccessJsonResponse(
            new UserResponseSuccessDTO(
                $this->userService->getUser($id)
            )
        );
    }


    public function updateUser(int $id, Request $request): JsonResponse
    {
        $userDto = $this->deserializeUserDTO($request);
        $this->validateDTO($this->validationService, $userDto, Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->userService->updateUser($id, $userDto);

        return $this->createSuccessJsonResponse(
            new BaseResponseSuccessDTO
        );
    }


    public function deleteUser(int $id): JsonResponse
    {
        $this->userService->deleteUser($id);
        return $this->createSuccessJsonResponse(new BaseResponseSuccessDTO());
    }


    public function getUsers(int $offset, int $limit): JsonResponse
    {
        $offset = $offset < 0 ? 0 : $offset;
        $limit = $limit < 0 ? 10 : $limit;

        $users = $this->userService->getUsers($offset, $limit);

        return $this->createSuccessJsonResponse(new BaseResponseSuccessDataDTO($users));
    }
}
