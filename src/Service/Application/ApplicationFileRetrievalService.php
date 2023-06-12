<?php

declare(strict_types=1);

namespace App\Service\Application;

use App\Exception\NotFoundException;
use App\Repository\ApplicationRepository;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Vich\UploaderBundle\Handler\DownloadHandler;

final class ApplicationFileRetrievalService implements ApplicationFileRetrievalServiceInterface
{
    public function __construct(
        private ApplicationRepository $applicationRepository,
        private DownloadHandler $downloadHandler,
    ) {
    }

    /** @throws \App\Exception\NotFoundException */
    public function getFile(int $applicationId): StreamedResponse
    {
        $application = $this->applicationRepository->findOneBy(['id' => $applicationId]) ?? throw new NotFoundException("Application $applicationId not found");
        return $this->downloadHandler->downloadObject($application, 'applicationFile');
    }
}
