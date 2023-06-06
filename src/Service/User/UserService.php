<?php

declare(strict_types=1);

namespace App\Service\User;

use App\DTO\User\UserDTO;
use App\Entity\User;
use App\Exception\UserCreateConflictException;
use App\Exception\NotFountException;
use App\Repository\UserRepository;
use App\Service\PopulateService;
use DateTimeImmutable;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class UserService implements UserServiceInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
        private PopulateService $populateService,
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

        try {
            $this->userRepository->save($user, true);
        } catch (UniqueConstraintViolationException $exception) {
            throw new UserCreateConflictException('User already exists.', previous: $exception);
        }

        /** @var UserDTO */
        $userDTO = $this->populateService->populateDTOFromEntity($user, UserDTO::class, ['user', 'create', 'id']);
        return $userDTO;
    }


    public function getUser(int $id): UserDTO
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        if (null === $user) {
            throw new UserNotFoundException('User not found');
        }

        /** @var UserDTO */
        $userDto = $this->populateService->populateDTOFromEntity($user, UserDTO::class, ['user', 'get', 'application_link', 'application']);
        return $userDto;
    }

    public function updateUser(int $id, UserDTO $userDTO): void
    {

        $user = $this->userRepository->findOneBy(['id' => $id]) ?? throw new UserNotFoundException("User $id not found", 404);

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
        $user = $this->userRepository->findOneBy(['id' => $id]) ?? throw new UserNotFoundException("User $id not found", 404);
        $this->userRepository->remove($user, true);
    }


    public function getUsers(int $offset, int $limit): array
    {
        $users = $this->userRepository->findBy([], ['id' => 'ASC'], $limit, $offset);
        /** @var UserDTO[] */
        $dtos = [];
        foreach ($users as $user) {
            $dtos[] = $this->populateService->populateDTOFromEntity($user, UserDTO::class, ['get', 'user']);
        }

        /** @var UserDTO[] */
        return $dtos;
    }
}
