<?php

declare(strict_types=1);

namespace App\RequestHandler\Application;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

interface ApplicationFileAttachmentRequestHandlerInterface
{
    public function handle(Request $request, int $applicationId): JsonResponse;
}
