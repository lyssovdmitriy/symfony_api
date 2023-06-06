<?php

declare(strict_types=1);

namespace App\Service\User;

use App\DTO\User\UserDTO;

interface UserServiceInterface
{


    public function createUser(UserDTO $dto): UserDTO;

    public function getUser(int $id): UserDTO;

    public function updateUser(int $id, UserDTO $userDTO): void;

    public function deleteUser(int $id): void;

    /**
     * @param integer $offset
     * @param integer $limit
     * @return UserDTO[]
     */
    public function getUsers(int $offset, int $limit): array;
}
