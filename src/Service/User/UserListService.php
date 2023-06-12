<?php

declare(strict_types=1);

namespace App\Service\User;

use App\DTO\User\UserDTO;
use App\Repository\UserRepository;
use App\Service\Utils\PopulateServiceInterface;

final class UserListService implements UserListServiceInterface
{

    public function __construct(
        private UserRepository $userRepository,
        private PopulateServiceInterface $populateService,
    ) {
    }

    /**
     * @return \App\DTO\User\UserDTO[]
     */
    public function getUsers(int $offset, int $limit): array
    {
        $users = $this->userRepository->findBy([], ['id' => 'ASC'], $limit, $offset);
        $dtos = [];
        foreach ($users as $user) {
            $dtos[] = $this->populateService->populateDTOFromEntity($user, UserDTO::class, ['get', 'user']);
        }

        return $dtos;
    }
}
