<?php

declare(strict_types=1);

namespace App\Service\Application;

use App\Exception\NotFoundException;
use App\Repository\ApplicationRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Handler\UploadHandler;

final class ApplicationFileAttachmentService implements ApplicationFileAttachmentServiceInterface
{
    public function __construct(
        private ApplicationRepository $applicationRepository,
        private UploadHandler $uploadHandler,
    ) {
    }

    public function attachFile(UploadedFile $uploadedFile, int $applicationId): void
    {
        $application = $this->applicationRepository->findOneBy(['id' => $applicationId]) ?? throw new NotFoundException("Application $applicationId not found");
        $application->setApplicationFile($uploadedFile);

        $this->uploadHandler->upload($application, 'applicationFile');

        $this->applicationRepository->save($application, true);
    }
}
