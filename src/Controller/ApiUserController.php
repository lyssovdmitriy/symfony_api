<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\BaseResponse\BaseResponseErrorDTO;
use OpenApi\Attributes as OA;
use App\DTO\User\UserCreateResponseSuccessDTO;
use App\DTO\User\UserDTO;
use App\RequestHandler\UserRequestHandler;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

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
}
