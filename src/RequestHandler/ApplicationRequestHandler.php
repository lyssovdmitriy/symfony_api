<?php

declare(strict_types=1);

namespace App\RequestHandler;

use App\DTO\Application\ApplicationDTO;
use App\DTO\Application\ApplicationResponseSuccessDTO;
use App\DTO\BaseResponse\BaseResponseSuccessDataDTO;
use App\DTO\BaseResponse\BaseResponseSuccessDTO;
use App\Entity\User;
use App\Exception\ApiException;
use App\Service\Application\ApplicationServiceInterface;
use App\Service\PopulateService;
use App\Service\ResponseService;
use App\Service\ValidationService;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

class ApplicationRequestHandler
{

    const APPLICATION_CREATE_SUCCESS = Response::HTTP_CREATED;
    const APPLICATION_CREATE_ERROR = Response::HTTP_BAD_REQUEST;
    const APPLICATION_CREATE_VALIDATION_ERROR = Response::HTTP_UNPROCESSABLE_ENTITY;
    const APPLICATION_CREATE_CONFLICT_ERROR = Response::HTTP_CONFLICT;
    const APPLICATION_GET_SUCCESS = Response::HTTP_OK;
    const APPLICATION_NOT_FOUND = Response::HTTP_NOT_FOUND;
    const APPLICATION_UPDATE_VALIDATION_ERROR = Response::HTTP_UNPROCESSABLE_ENTITY;


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

        $validationErrors = $this->validationService->validateDTO($dto);

        if (count($validationErrors) > 0) {
            throw new ApiException(self::APPLICATION_CREATE_VALIDATION_ERROR, 'Validation failed', $validationErrors);
        }

        $applicationDTO = $this->applicationService->createApplication($dto, $userId);
        $responseDTO = new ApplicationResponseSuccessDTO($applicationDTO);
        return $this->responseService->createSuccessResponse($responseDTO, self::APPLICATION_CREATE_SUCCESS);
    }

    public function getApplication(int $id): JsonResponse
    {
        $applicationDTO = $this->applicationService->getApplicationById($id);
        if (isset($applicationDTO->file)) {
            $applicationDTO->file = "applications/{$applicationDTO->id}/file";
        }
        $responseDTO = new ApplicationResponseSuccessDTO($applicationDTO);

        return $this->responseService->createSuccessResponse($responseDTO);
    }

    private function deserializeApplicationDTO(Request $request): ApplicationDTO
    {
        try {
            $dto = $this->serializer->deserialize($request->getContent(), ApplicationDTO::class, 'json');
        } catch (\Throwable $th) {
            throw new ApiException(self::APPLICATION_CREATE_ERROR, $th->getMessage(), ['JSON parsing error: Invalid JSON syntax'], $th);
        }
        return $dto;
    }

    public function attachFile(Request $request, int $applicationId): JsonResponse
    {
        $file = $request->files->get('file');
        if (!$file instanceof UploadedFile) {
            throw new ApiException(self::APPLICATION_CREATE_VALIDATION_ERROR, 'File is required');
        }

        try {
            $this->applicationService->attachFile($file, $applicationId);
        } catch (Throwable $e) {
            throw new ApiException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to upload file.', [$e->getMessage()], $e);
        }

        return $this->responseService->createSuccessResponse(new BaseResponseSuccessDTO);
    }

    public function getFileByApplicationId(int $id): StreamedResponse
    {
        return $this->applicationService->getFile($id);
    }
}
