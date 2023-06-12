<?php

declare(strict_types=1);

namespace App\Service\User;

use App\DTO\User\UserDTO;

interface UserCreationServiceInterface
{
    public function createUser(UserDTO $dto): UserDTO;
}
