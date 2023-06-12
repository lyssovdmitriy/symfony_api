<?php

declare(strict_types=1);

namespace App\RequestHandler\User;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

interface UserUpdateRequestHandlerInterface
{
    public function handle(int $id, Request $request): JsonResponse;
}
