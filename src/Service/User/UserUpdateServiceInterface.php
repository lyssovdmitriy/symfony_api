<?php

declare(strict_types=1);

namespace App\Service\User;

use App\DTO\User\UserDTO;

interface UserUpdateServiceInterface
{
    public function updateUser(int $id, UserDTO $userDTO): void;
}
