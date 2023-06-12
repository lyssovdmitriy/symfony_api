<?php

declare(strict_types=1);

namespace App\RequestHandler\Application;

use App\Service\Application\ApplicationFileRetrievalServiceInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ApplicationFileRetrievalRequestHandler extends BaseApplicationRequestHandler implements ApplicationFileRetrievalRequestHandlerInterface
{
    public function __construct(
        private ApplicationFileRetrievalServiceInterface $applicationFileRetrievalService,
    ) {
    }

    public function handle(int $applicationId): StreamedResponse
    {
        return $this->applicationFileRetrievalService->getFile($applicationId);
    }
}
