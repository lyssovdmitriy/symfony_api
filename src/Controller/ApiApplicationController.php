<?php

namespace App\Controller;

use App\DTO\Application\ApplicationDTO;
use App\Exception\ApiException;
use App\RequestHandler\Application\ApplicationCreationRequestHandlerInterface;
use App\RequestHandler\Application\ApplicationFileAttachmentRequestHandlerInterface;
use App\RequestHandler\Application\ApplicationFileRetrievalRequestHandlerInterface;
use App\RequestHandler\Application\ApplicationRetrievalRequestHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

// TODO describe responses
#[OA\Tag(name: "Aplications")]
class ApiApplicationController extends AbstractController
{

    public function __construct(
        private ApplicationCreationRequestHandlerInterface $applicationCreationRequestHandler,
        private ApplicationRetrievalRequestHandlerInterface $applicationRetrievalRequestHandler,
        private ApplicationFileAttachmentRequestHandlerInterface $applicationFileAttachmentRequestHandler,
        private ApplicationFileRetrievalRequestHandlerInterface $applicationFileRetrievalRequestHandler,
    ) {
    }

    #[Route('/api/applications', methods: ['POST'])]
    #[OA\Post(
        path: '/api/applications',
        requestBody: new OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: ApplicationDTO::class, groups: ['create']))),
        summary: 'Create Application',
    )]
    public function createApplication(Request $request): JsonResponse
    {
        /** @var null|\App\Entity\User */
        $user = $this->getUser();
        $userId = $user?->getId() ?? throw new ApiException(404);
        return $this->applicationCreationRequestHandler->handle($request, $userId);
    }

    #[Route('/api/applications/{id}', methods: ['GET'])]
    #[OA\Get(
        path: '/api/applications/{id}',
        summary: 'Get Application',
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Successful response',
        content: new Model(type: ApplicationDTO::class, groups: ['get'])
    )]
    public function getApplication(int $id): JsonResponse
    {
        return $this->applicationRetrievalRequestHandler->handle($id);
    }

    #[Route('/api/applications/{id}/attach-file', methods: ['POST'])]
    #[OA\Post(
        path: '/api/applications/{id}/attach-file',
        summary: 'Attach File',
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [new OA\Property(
                        property: 'file',
                        type: 'file',
                    )]
                )
            )
        )
    )]
    public function attachFile(Request $request, int $id): JsonResponse
    {
        return $this->applicationFileAttachmentRequestHandler->handle($request, $id);
    }

    #[Route('/api/applications/{id}/file', methods: ['GET'])]
    #[OA\Get(path: '/api/applications/{id}/file', summary: 'Get File')]
    public function getFileByApplicationId(int $id): StreamedResponse
    {
        return $this->applicationFileRetrievalRequestHandler->handle($id);
    }
}
