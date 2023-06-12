<?php

declare(strict_types=1);

namespace App\Service\User;

use App\Exception\NotFoundException;
use App\Repository\UserRepository;

final class UserDeletionService implements UserDeletionServiceInterface
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    /** @throws \App\Exception\NotFoundException */
    public function deleteUser(int $id): void
    {
        $user = $this->userRepository->findOneBy(['id' => $id]) ?? throw new NotFoundException("User $id not found");
        $this->userRepository->remove($user, true);
    }
}
