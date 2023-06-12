<?php

declare(strict_types=1);

namespace App\RequestHandler\Application;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

interface ApplicationCreationRequestHandlerInterface
{
    public function handle(Request $request, int $userId): JsonResponse;
}
