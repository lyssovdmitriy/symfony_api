<?php

declare(strict_types=1);

namespace App\RequestHandler\Application;

use Symfony\Component\HttpFoundation\JsonResponse;

interface ApplicationRetrievalRequestHandlerInterface
{
    public function handle(int $id): JsonResponse;
}
