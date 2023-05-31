<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\User\UserDTO;
use App\Entity\User;
use App\Exception\ApiException;
use App\Repository\UserRepository;
use App\RequestHandler\UserRequestHandler;
use DateTimeImmutable;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function createUser(UserDTO $dto): User
    {
        $user = new User();
        $user->setEmail($dto->email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $dto->password));
        $user->setAddress($dto->address);
        $user->setBirthday(new DateTimeImmutable($dto->birthday));
        $user->setPhone($dto->phone);

        $this->entityManager->persist($user);
        try {
            $this->entityManager->flush();
        } catch (UniqueConstraintViolationException $exception) {
            throw new ApiException(UserRequestHandler::USER_CREATE_CONFLICT_ERROR, 'User already exists.', ['The user with the provided email already exists in the system.'], $exception);
        }

        return $user;
    }
}
