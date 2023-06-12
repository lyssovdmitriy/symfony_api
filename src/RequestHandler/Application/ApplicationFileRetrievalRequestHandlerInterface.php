<?php

declare(strict_types=1);

namespace App\RequestHandler\Application;

use Symfony\Component\HttpFoundation\StreamedResponse;

interface ApplicationFileRetrievalRequestHandlerInterface
{
    public function handle(int $applicationId): StreamedResponse;
}
