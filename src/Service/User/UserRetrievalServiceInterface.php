<?php

declare(strict_types=1);

namespace App\Service\User;

use App\DTO\User\UserDTO;

interface UserRetrievalServiceInterface
{
    public function getUser(int $id): UserDTO;
}
