<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\BaseResponse\BaseResponseErrorDTO;
use App\DTO\BaseResponse\BaseResponseSuccessDTO;
use OpenApi\Attributes as OA;
use App\DTO\User\UserResponseSuccessDTO;
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

#[OA\Response(
    response: Response::HTTP_UNAUTHORIZED,
    description: 'Invalid credentials.',
    content: new OA\JsonContent(properties: [
        new OA\Property(type: 'int', property: 'code', example: 401),
        new OA\Property(type: 'string', property: 'message', example: 'Invalid credentials.'),
    ])
)]
#[OA\Tag(name: "Users")]
class ApiUserController extends AbstractController
{

    public function __construct(
        private UserRequestHandler $userRequestHandler,
    ) {
    }


    #[Route('/api/users', methods: ['POST'])]
    #[OA\Post(
        path: '/api/users',
        requestBody: new OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: UserDTO::class, groups: ['create']))),
        description: 'Creating a new user'
    )]
    #[OA\Response(
        response: UserRequestHandler::USER_CREATE_SUCCESS,
        description: 'Successful response',
        content: new Model(type: UserResponseSuccessDTO::class, groups: ['get'])
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
    #[OA\Response(
        response: UserRequestHandler::USER_GET_SUCCESS,
        description: 'Successful response',
        content: new Model(type: UserResponseSuccessDTO::class, groups: ['get'])
    )]

    #[OA\Response(
        response: Response::HTTP_FORBIDDEN,
        description: 'Access denied',
        content: new Model(type: BaseResponseErrorDTO::class)
    )]
    #[OA\Response(
        response: UserRequestHandler::USER_NOT_FOUND,
        description: 'User not found',
        content: new Model(type: BaseResponseErrorDTO::class)
    )]
    public function getUserById(int $id): JsonResponse
    {
        $this->checkAuthForUserAction($id);
        return $this->userRequestHandler->getUser($id);
    }


    #[Route('/api/users/{id}', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/users/{id}',
        requestBody: new OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: UserDTO::class, groups: ['update']))),
        description: 'Creating a new user'
    )]
    #[OA\Response(
        response: UserRequestHandler::USER_GET_SUCCESS,
        description: 'Successful response',
        content: new Model(type: UserResponseSuccessDTO::class, groups: ['update'])
    )]

    #[OA\Response(
        response: Response::HTTP_FORBIDDEN,
        description: 'Access denied',
        content: new Model(type: BaseResponseErrorDTO::class)
    )]
    #[OA\Response(
        response: UserRequestHandler::USER_NOT_FOUND,
        description: 'User not found',
        content: new Model(type: BaseResponseErrorDTO::class)
    )]
    public function updateUserById(int $id, Request $request): JsonResponse
    {
        $this->checkAuthForUserAction($id);
        return $this->userRequestHandler->updateUser($id, $request);
    }



    #[Route('/api/users/{id}', methods: ['DELETE'])]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Successful response',
        content: new Model(type: BaseResponseSuccessDTO::class)
    )]

    #[OA\Response(
        response: Response::HTTP_FORBIDDEN,
        description: 'Access denied',
        content: new Model(type: BaseResponseErrorDTO::class)
    )]
    #[OA\Response(
        response: UserRequestHandler::USER_NOT_FOUND,
        description: 'User not found',
        content: new Model(type: BaseResponseErrorDTO::class)
    )]
    public function deleteUserById(int $id): JsonResponse
    {
        $this->checkAuthForUserAction($id);
        return $this->userRequestHandler->deleteUser($id);
    }



    private function checkAuthForUserAction(int $id): void
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var User */
        $user = $this->getUser();
        if ($user->getId() !== $id && !in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            throw new AccessDeniedHttpException('Access denied.');
        }
    }
}
