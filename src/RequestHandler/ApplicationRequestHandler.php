<?php

declare(strict_types=1);

namespace App\RequestHandler;

use App\DTO\Application\ApplicationDTO;
use App\DTO\Application\ApplicationResponseSuccessDTO;
use App\DTO\BaseResponse\BaseResponseSuccessDTO;
use App\Exception\ApiException;
use App\Service\Application\ApplicationServiceInterface;
use App\Service\ResponseService;
use App\Service\ValidationService;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

final class ApplicationRequestHandler extends AbstractRequestHandler
{

    public function __construct(
        private SerializerInterface $serializer,
        private ValidationService $validationService,
        private ApplicationServiceInterface $applicationService,
        private ResponseService $responseService,
    ) {
    }

    public function createApplication(Request $request, int $userId): JsonResponse
    {
        $dto = $this->deserializeApplicationDTO($request);
        $this->validateDTO($this->validationService, $dto, Response::HTTP_UNPROCESSABLE_ENTITY);

        return $this->createSuccessJsonResponse(
            new ApplicationResponseSuccessDTO(
                $this->applicationService->createApplication($dto, $userId)
            ),
            Response::HTTP_CREATED
        );
    }

    private function createSuccessJsonResponse(mixed $dto, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return $this->responseService->createSuccessResponse($dto, $statusCode);
    }

    public function getApplication(int $id): JsonResponse
    {
        $applicationDTO = $this->applicationService->getApplicationById($id);
        if (isset($applicationDTO->file)) {
            $applicationDTO->file = "applications/{$applicationDTO->id}/file";
        }
        $responseDTO = new ApplicationResponseSuccessDTO($applicationDTO);

        return $this->createSuccessJsonResponse($responseDTO);
    }

    /** @throws \App\Exception\ApiException */
    private function deserializeApplicationDTO(Request $request): ApplicationDTO
    {
        try {
            $dto = $this->serializer->deserialize($request->getContent(), ApplicationDTO::class, 'json');
        } catch (\Throwable $th) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $th->getMessage(), ['JSON parsing error: Invalid JSON syntax'], $th);
        }
        return $dto;
    }

    /** @throws \App\Exception\ApiException */
    public function attachFile(Request $request, int $applicationId): JsonResponse
    {
        $file = $request->files->get('file');
        if (!$file instanceof UploadedFile) {
            throw new ApiException(Response::HTTP_UNPROCESSABLE_ENTITY, 'File is required');
        }
        $this->tryToAttachFile($file, $applicationId);

        return $this->createSuccessJsonResponse(new BaseResponseSuccessDTO);
    }

    /** @throws \App\Exception\ApiException */
    private function tryToAttachFile(UploadedFile $file, int $applicationId): void
    {
        try {
            $this->applicationService->attachFile($file, $applicationId);
        } catch (Throwable $e) {
            throw new ApiException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to upload file.', [$e->getMessage()], $e);
        }
    }

    public function getFileByApplicationId(int $id): StreamedResponse
    {
        return $this->applicationService->getFile($id);
    }
}
