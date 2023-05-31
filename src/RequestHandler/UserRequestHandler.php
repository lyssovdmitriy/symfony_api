<?php

declare(strict_types=1);

namespace App\RequestHandler;

use App\DTO\User\UserCreateResponseSuccessDTO;
use App\DTO\User\UserDTO;
use App\Exception\ApiException;
use App\Service\ResponseService;
use App\Service\UserService;
use App\Service\ValidationService;
use App\Transformer\UserTransformer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UserRequestHandler
{

    const USER_CREATE_SUCCESS = Response::HTTP_CREATED;
    const USER_CREATE_ERROR = Response::HTTP_BAD_REQUEST;
    const USER_CREATE_VALIDATION_ERROR = Response::HTTP_UNPROCESSABLE_ENTITY;


    public function __construct(
        private SerializerInterface $serializer,
        private NormalizerInterface $normalizer,
        private ValidatorInterface $validator,
        private ValidationService $validationService,
        private UserService $userService,
        private UserTransformer $userTransformer,
        private ResponseService $responseService,
    ) {
    }

    public function createUser(Request $request): Response
    {
        try {
            $dto = $this->deserializeUserCreateDTO($request);
        } catch (\Throwable $th) {
            throw new ApiException(self::USER_CREATE_ERROR, $th->getMessage(), ['JSON parsing error: Invalid JSON syntax'], $th);
        }

        $validationErrors = $this->validationService->validateUserCreateDTO($dto);

        if (!empty($validationErrors)) {
            throw new ApiException(self::USER_CREATE_VALIDATION_ERROR, 'Validation failed', $validationErrors);
        }

        $user = $this->userService->createUser($dto);
        $responseDto = new UserCreateResponseSuccessDTO($this->populateDTOFromEntity($user, UserDTO::class, ['get']));
        return $this->responseService->createSuccessResponse($responseDto, self::USER_CREATE_SUCCESS);
    }

    private function deserializeUserCreateDTO(Request $request): UserDTO
    {
        try {
            $dto = $this->serializer->deserialize($request->getContent(), UserDTO::class, 'json');
        } catch (\Throwable $th) {
            throw new ApiException(self::USER_CREATE_ERROR, $th->getMessage(), ['JSON parsing error: Invalid JSON syntax'], $th);
        }
        return $dto;
    }

    private function populateDTOFromEntity(object $entity, string $type, array $groups): object
    {
        return $this->serializer->deserialize(
            $this->serializer->serialize($entity, 'json'),
            $type,
            'json',
            [AbstractNormalizer::GROUPS => $groups]
        );
    }
}
