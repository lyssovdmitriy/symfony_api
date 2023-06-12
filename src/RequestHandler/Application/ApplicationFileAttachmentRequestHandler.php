<?php

declare(strict_types=1);

namespace App\RequestHandler\Application;

use App\DTO\BaseResponse\BaseResponseSuccessDTO;
use App\Exception\ApiException;
use App\Service\Application\ApplicationFileAttachmentServiceInterface;
use App\Service\Utils\ResponseServiceInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

final class ApplicationFileAttachmentRequestHandler extends BaseApplicationRequestHandler implements ApplicationFileAttachmentRequestHandlerInterface
{
    public function __construct(
        private ApplicationFileAttachmentServiceInterface $applicationFileAttachmentService,
        private ResponseServiceInterface $responseSerivce,
    ) {
    }

    /** @throws \App\Exception\ApiException */
    public function handle(Request $request, int $applicationId): JsonResponse
    {
        $file = $request->files->get('file');
        if (!$file instanceof UploadedFile) {
            throw new ApiException(JsonResponse::HTTP_UNPROCESSABLE_ENTITY, 'File is required');
        }
        $this->tryToAttachFile($file, $applicationId);

        return $this->responseSerivce->createSuccessResponse(new BaseResponseSuccessDTO);
    }

    /** @throws \App\Exception\ApiException */
    private function tryToAttachFile(UploadedFile $file, int $applicationId): void
    {
        try {
            $this->applicationFileAttachmentService->attachFile($file, $applicationId);
        } catch (Throwable $e) {
            throw new ApiException(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, 'Failed to upload file.', [$e->getMessage()], $e);
        }
    }
}
