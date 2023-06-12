<?php

declare(strict_types=1);

namespace App\Service\Application;

use Symfony\Component\HttpFoundation\StreamedResponse;

interface ApplicationFileRetrievalServiceInterface
{
    public function getFile(int $applicationId): StreamedResponse;
}
