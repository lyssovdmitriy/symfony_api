<?php

declare(strict_types=1);

namespace App\Service\User;

use App\DTO\User\UserDTO;
use App\Exception\NotFoundException;
use App\Repository\UserRepository;
use App\Service\Utils\PopulateServiceInterface;

final class UserRetrievalService implements UserRetrievalServiceInterface
{

    public function __construct(
        private UserRepository $userRepository,
        private PopulateServiceInterface $populateService,
    ) {
    }

    /** @throws \App\Exception\NotFoundException */
    public function getUser(int $id): UserDTO
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        if (null === $user) {
            throw new NotFoundException('User not found');
        }
        return $this->populateService->populateDTOFromEntity($user, UserDTO::class, ['user', 'get', 'application_link', 'application']);
    }
}
