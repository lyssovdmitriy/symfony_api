<?php

declare(strict_types=1);

namespace App\Service\User;

interface UserDeletionServiceInterface
{
    public function deleteUser(int $id): void;
}
