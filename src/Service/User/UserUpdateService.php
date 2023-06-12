<?php

declare(strict_types=1);

namespace App\Service\User;

use App\DTO\User\UserDTO;
use App\Entity\User;
use App\Exception\NotFoundException;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserUpdateService implements UserUpdateServiceInterface
{

    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    /** @throws \App\Exception\NotFoundException */
    public function updateUser(int $id, UserDTO $userDTO): void
    {
        $user = $this->userRepository->findOneBy(['id' => $id]) ?? throw new NotFoundException("User $id not found", 404);
        $this->changeUserData($user, $userDTO);
        $this->userRepository->save($user, true);
    }

    private function changeUserData(User $user, UserDTO $userDTO): void
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
    }
}
