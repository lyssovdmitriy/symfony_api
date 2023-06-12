<?php

declare(strict_types=1);

namespace App\RequestHandler\User;

use Symfony\Component\HttpFoundation\JsonResponse;

interface UserListRequestHandlerInterface
{
    public function handle(int $offset, int $limit): JsonResponse;
}
