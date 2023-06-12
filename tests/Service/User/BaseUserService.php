<?php

namespace App\Tests\Service\User;

use App\DTO\User\UserDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Utils\PopulateServiceInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class BaseUserService extends TestCase
{
    protected UserPasswordHasherInterface&MockObject $passwordHasher;

    protected UserRepository&MockObject $userRepository;

    protected PopulateServiceInterface&MockObject $populateService;

    protected UserDTO $userDTO;

    protected User&MockObject $user;


    protected function setUp(): void
    {
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->populateService = $this->createMock(PopulateServiceInterface::class);

        $this->userDTO = new UserDTO();
        $this->userDTO->email = 'test@example.com';
        $this->userDTO->password = 'password123';
        $this->userDTO->address = '123 Main St';
        $this->userDTO->birthday = '1990-01-01';
        $this->userDTO->phone = '1234567890';

        $this->user = $this->createMock(User::class);
    }
}
