<?php

declare(strict_types=1);

namespace App\Service\User;

interface UserListServiceInterface
{
    /**
     * @return \App\DTO\User\UserDTO[]
     */
    public function getUsers(int $offset, int $limit): array;
};
