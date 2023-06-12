<?php

declare(strict_types=1);

namespace App\RequestHandler\User;

use Symfony\Component\HttpFoundation\JsonResponse;

interface UserRetrievalRequestHandlerInterface
{
    public function handle(int $id): JsonResponse;
}
