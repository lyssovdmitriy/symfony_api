<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\User\UserDTO;
use App\Entity\User;
use App\Exception\UserCreateConflictException;
use App\Exception\UserNotFountException;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
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

        try {
            $this->userRepository->save($user, true);
        } catch (UniqueConstraintViolationException $exception) {
            throw new UserCreateConflictException('User already exists.', previous: $exception);
        }

        return $user;
    }

    public function getUser(int $id): User
    {
        $user = $this->userRepository->getUserById($id);
        if (null === $user) {
            throw new UserNotFountException('User not found');
        }
        return $user;
    }

    public function updateUser(User $user, UserDTO $userDTO): void
    {

        $changer = [
            'address' => fn () => $user->setAddress($userDTO->address),
            'birthday' => fn () => $user->setBirthday(new DateTimeImmutable($userDTO->birthday)),
            'phone' => fn () => $user->setPhone($userDTO->phone),
            'password' => fn () => $user->setPassword($this->passwordHasher->hashPassword($user, $userDTO->password)),
        ];

        foreach ($changer as $field => $func) {
            in_array($field, array_keys((array)$userDTO), true) ? $func() : null;
        }

        $this->userRepository->save($user, true);
    }


    public function deleteUser(int $id): void
    {
        $this->userRepository->remove($this->getUser($id), true);
    }
}
