<?php

declare(strict_types=1);

namespace App\Service\User;

use App\DTO\User\UserDTO;
use App\Entity\User;
use App\Exception\UserCreateConflictException;
use App\Repository\UserRepository;
use App\Service\Utils\PopulateServiceInterface;
use DateTimeImmutable;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserCreationService implements UserCreationServiceInterface
{

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
        private PopulateServiceInterface $populateService,
    ) {
    }

    public function createUser(UserDTO $dto): UserDTO
    {
        $user = new User();
        $user->setEmail($dto->email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $dto->password));
        $user->setAddress($dto->address);
        $user->setBirthday(new DateTimeImmutable($dto->birthday));
        $user->setPhone($dto->phone);

        $this->tryToSaveNewUser($user);

        return $this->populateService->populateDTOFromEntity($user, UserDTO::class, ['user', 'create', 'id']);
    }

    /** @throws \App\Exception\UserCreateConflictException */
    private function tryToSaveNewUser(User $user): void
    {
        try {
            $this->userRepository->save($user, true);
        } catch (UniqueConstraintViolationException $exception) {
            throw new UserCreateConflictException('User already exists.', previous: $exception);
        }
    }
}
