<?php

declare(strict_types=1);

namespace App\Tests\Service\User;

use App\DTO\User\UserDTO;
use App\Entity\User;
use App\Exception\UserCreateConflictException;
use App\Service\User\UserCreationService;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

final class UserCreationServiceTest extends BaseUserServiceTest
{

    private UserCreationService $userCreationService;


    protected function setUp(): void
    {
        parent::setUp();

        $this->userCreationService = new UserCreationService(
            $this->passwordHasher,
            $this->userRepository,
            $this->populateService,
        );
    }

    public function testCreateUser(): void
    {
        $hashedPassword = 'hashed_password';
        $this->passwordHasher->expects($this->once())
            ->method('hashPassword')
            ->with($this->isInstanceOf(User::class), $this->userDTO->password)
            ->willReturn($hashedPassword);

        $this->userRepository->expects($this->once())
            ->method('save');

        $this->populateService->expects($this->once())
            ->method('populateDTOFromEntity')
            ->willReturn($this->userDTO);

        $user = $this->userCreationService->createUser($this->userDTO);
        $this->assertInstanceOf(UserDTO::class, $user);
    }


    public function testCreateUserThrowsApiExceptionOnUniqueConstraintViolation(): void
    {
        $driverException = $this->createMock(DriverException::class);
        $exception = new UniqueConstraintViolationException($driverException, null);

        $this->userRepository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(User::class), true)
            ->willThrowException($exception);

        $this->expectException(UserCreateConflictException::class);
        $this->expectExceptionMessage('User already exists.');
        $this->userCreationService->createUser($this->userDTO);
    }
}
