<?php

namespace App\Tests\Service;

use App\DTO\User\UserDTO;
use App\Entity\User;
use App\Exception\ApiException;
use App\Repository\UserRepository;
use App\Service\UserService;
use DateTimeImmutable;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\DBAL\Exception\DriverException as DBALDriverException;

class UserServiceTest extends TestCase
{
    private UserService $userService;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private UserPasswordHasherInterface $passwordHasher;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);

        $this->userService = new UserService(
            $this->passwordHasher,
            $this->userRepository,
        );
    }

    public function testCreateUser(): void
    {
        $dto = new UserDTO();
        $dto->email = 'test@example.com';
        $dto->password = 'password123';
        $dto->address = '123 Main St';
        $dto->birthday = '1990-01-01';
        $dto->phone = '1234567890';

        $hashedPassword = 'hashed_password';
        $this->passwordHasher->expects($this->once())
            ->method('hashPassword')
            ->with($this->isInstanceOf(User::class), $dto->password)
            ->willReturn($hashedPassword);

        $this->userRepository->expects($this->once())
            ->method('save');

        $user = $this->userService->createUser($dto);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($dto->email, $user->getEmail());
        $this->assertEquals($hashedPassword, $user->getPassword());
        $this->assertEquals($dto->address, $user->getAddress());
        $this->assertEquals(new DateTimeImmutable($dto->birthday), $user->getBirthday());
        $this->assertEquals($dto->phone, $user->getPhone());
    }

    public function testCreateUserThrowsApiExceptionOnUniqueConstraintViolation(): void
    {
        $dto = new UserDTO();
        $dto->email = 'test@example.com';
        $dto->password = 'password123';
        $dto->address = '123 Main St';
        $dto->birthday = '1990-01-01';
        $dto->phone = '1234567890';

        $driverException = $this->createMock(DBALDriverException::class);
        $exception = new UniqueConstraintViolationException($driverException, null);

        $this->userRepository->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('User already exists.');
        $this->userService->createUser($dto);
    }
}
