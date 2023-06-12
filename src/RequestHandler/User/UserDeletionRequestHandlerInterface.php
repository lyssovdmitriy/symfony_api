<?php

declare(strict_types=1);

namespace App\RequestHandler\User;

use Symfony\Component\HttpFoundation\JsonResponse;

interface UserDeletionRequestHandlerInterface
{
    public function handle(int $id): JsonResponse;
}
