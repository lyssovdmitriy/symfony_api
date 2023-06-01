<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\BaseResponse\BaseResponseErrorDTO;
use OpenApi\Attributes as OA;
use App\DTO\User\UserCreateResponseSuccessDTO;
use App\DTO\User\UserDTO;
use App\Entity\User;
use App\RequestHandler\UserRequestHandler;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ApiUserController extends AbstractController
{

    public function __construct(
        private UserRequestHandler $userRequestHandler,
    ) {
    }





    #[Route('/api/users', methods: ['POST'])]
    #[OA\Tag(name: "Users")]
    #[OA\Post(
        path: '/api/users',
        requestBody: new OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: UserDTO::class, groups: ['create']))),
        description: 'Creating a new user'
    )]
    #[OA\Response(
        response: UserRequestHandler::USER_CREATE_SUCCESS,
        description: 'Successful response',
        content: new Model(type: UserCreateResponseSuccessDTO::class, groups: ['get'])
    )]
    #[OA\Response(
        response: UserRequestHandler::USER_CREATE_ERROR,
        description: 'Invalid request',
        content: new Model(type: BaseResponseErrorDTO::class)
    )]
    #[OA\Response(
        response: UserRequestHandler::USER_CREATE_VALIDATION_ERROR,
        description: 'Invalid data',
        content: new Model(type: BaseResponseErrorDTO::class)
    )]
    #[OA\Response(
        response: UserRequestHandler::USER_CREATE_CONFLICT_ERROR,
        description: 'User already exists.',
        content: new Model(type: BaseResponseErrorDTO::class)
    )]
    public function createUser(Request $request): JsonResponse
    {
        return $this->userRequestHandler->createUser($request);
    }






    #[Route('/api/users/{id}', methods: ['GET'])]
    #[OA\Tag(name: "Users")]
    #[OA\Response(
        response: UserRequestHandler::USER_GET_SUCCESS,
        description: 'Successful response',
        content: new Model(type: UserDTO::class, groups: ['get'])
    )]
    #[Security(name: 'Bearer')]
    #[OA\Response(
        response: Response::HTTP_FORBIDDEN,
        description: 'Access denied',
        content: new Model(type: BaseResponseErrorDTO::class)
    )]
    #[OA\Response(
        response: UserRequestHandler::USER_NOT_FOUNT,
        description: 'User not found',
        content: new Model(type: BaseResponseErrorDTO::class)
    )]
    public function getUsers(int $id): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var User */
        $user = $this->getUser();
        if ($user->getId() !== $id && !in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            throw new AccessDeniedHttpException('Access denied.');
        }
        return $this->userRequestHandler->getUser($id);
    }





    #[Route('/api/users/{id}', methods: ['PUT'])]
    #[OA\Tag(name: "Users")]
    #[OA\Put(
        path: '/api/users/{id}',
        requestBody: new OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: UserDTO::class, groups: ['update']))),
        description: 'Creating a new user'
    )]
    #[OA\Response(
        response: UserRequestHandler::USER_GET_SUCCESS,
        description: 'Successful response',
        content: new Model(type: UserDTO::class, groups: ['update'])
    )]
    #[Security(name: 'Bearer')]
    #[OA\Response(
        response: Response::HTTP_FORBIDDEN,
        description: 'Access denied',
        content: new Model(type: BaseResponseErrorDTO::class)
    )]
    #[OA\Response(
        response: UserRequestHandler::USER_NOT_FOUNT,
        description: 'User not found',
        content: new Model(type: BaseResponseErrorDTO::class)
    )]
    public function updateUser(int $id, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var User */
        $user = $this->getUser();
        if ($user->getId() !== $id && !in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            throw new AccessDeniedHttpException('Access denied.');
        }
        return $this->userRequestHandler->updateUser($id, $request);
    }
}
