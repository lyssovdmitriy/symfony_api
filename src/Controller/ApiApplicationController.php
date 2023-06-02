<?php

namespace App\Controller;

use App\DTO\Application\ApplicationDTO;
use App\Repository\ApplicationRepository;
use App\RequestHandler\ApplicationRequestHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;

#[OA\Tag(name: "Aplications")]
#
class ApiApplicationController extends AbstractController
{

    public function __construct(
        private ApplicationRequestHandler $applicationRequestHandler,
    ) {
    }

    #[Route('/api/applications', methods: ['POST'])]
    #[OA\Post(
        path: '/api/applications',
        requestBody: new OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: ApplicationDTO::class, groups: ['create']))),
        description: 'Creating a new application'
    )]
    public function createApplication(Request $request): JsonResponse
    {
        /** @var User */
        $user = $this->getUser();
        return $this->applicationRequestHandler->createApplication($request, $user);
    }

    #[Route('/api/applications/{id}', methods: ['GET'])]
    #[OA\Response(
        response: ApplicationRequestHandler::APPLICATION_GET_SUCCESS,
        description: 'Successful response',
        content: new Model(type: ApplicationDTO::class, groups: ['get'])
    )]
    public function getApplication(int $id): JsonResponse
    {
        return $this->applicationRequestHandler->getApplication($id);
    }
}
