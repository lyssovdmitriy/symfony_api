<?php

namespace App\Tests\Service;

use App\DTO\User\UserDTO;
use App\Entity\User;
use App\Exception\UserCreateConflictException;
use App\Repository\UserRepository;
use App\Service\PopulateService;
use App\Service\User\UserService;
use DateTimeImmutable;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\DBAL\Exception\DriverException as DBALDriverException;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class UserServiceTest extends TestCase
{
    private UserService $userService;

    /** @var UserPasswordHasherInterface&MockObject */
    private MockObject $passwordHasher;

    /** @var UserRepository */
    private MockObject $userRepository;

    /** @var PopulateService&MockObject */
    private MockObject $populateService;

    private UserDTO $userDTO;

    /** @var User&MockObject */
    private MockObject $user;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->populateService = $this->createMock(PopulateService::class);

        $this->userService = new UserService(
            $this->passwordHasher,
            $this->userRepository,
            $this->populateService,
        );

        $this->userDTO = new UserDTO();
        $this->userDTO->email = 'test@example.com';
        $this->userDTO->password = 'password123';
        $this->userDTO->address = '123 Main St';
        $this->userDTO->birthday = '1990-01-01';
        $this->userDTO->phone = '1234567890';

        $this->user = $this->createMock(User::class);
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

        $user = $this->userService->createUser($this->userDTO);
        $this->assertInstanceOf(UserDTO::class, $user);
    }

    public function testCreateUserThrowsApiExceptionOnUniqueConstraintViolation(): void
    {
        $driverException = $this->createMock(DBALDriverException::class);
        $exception = new UniqueConstraintViolationException($driverException, null);

        $this->userRepository->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->expectException(UserCreateConflictException::class);
        $this->expectExceptionMessage('User already exists.');
        $this->userService->createUser($this->userDTO);
    }

    public function testGetUser(): void
    {
        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($this->user);

        $this->populateService->expects($this->once())
            ->method('populateDTOFromEntity')
            ->with($this->user, UserDTO::class)
            ->willReturn($this->userDTO);

        $userDto = $this->userService->getUser(1);
        $this->assertInstanceOf(UserDTO::class, $userDto);
    }

    public function testGetUserNull(): void
    {
        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->expectException(UserNotFoundException::class);

        $this->userService->getUser(1);
    }

    public function testUpdateUser(): void
    {
        $hashedPassword = 'hashed_password';
        $this->passwordHasher->expects($this->once())
            ->method('hashPassword')
            ->with($this->user, $this->userDTO->password)
            ->willReturn($hashedPassword);

        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($this->user);

        $this->userRepository->expects($this->once())
            ->method('save')
            ->with($this->user, true);

        $this->user->expects($this->once())
            ->method('setAddress')
            ->with($this->userDTO->address);

        $this->user->expects($this->once())
            ->method('setBirthday')
            ->with(new DateTimeImmutable($this->userDTO->birthday));

        $this->user->expects($this->once())
            ->method('setPhone')
            ->with($this->userDTO->phone);

        $this->user->expects($this->once())
            ->method('setPassword')
            ->with($hashedPassword);

        $this->userService->updateUser(1, $this->userDTO);
    }

    public function testUpdateUserNull(): void
    {
        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->expectException(UserNotFoundException::class);

        $this->userService->updateUser(1, new UserDTO());
    }

    public function testDeleteUser(): void
    {
        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($this->user);

        $this->userRepository->expects($this->once())
            ->method('remove')
            ->with($this->user, true);

        $this->userService->deleteUser(1);
    }

    public function testDeleteUserNull(): void
    {
        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->expectException(UserNotFoundException::class);

        $this->userService->deleteUser(1);
    }

    public function testGetUsers(): void
    {
        $this->userRepository->expects($this->once())
            ->method('findBy')
            ->willReturn([$this->user]);

        $this->populateService->expects($this->once())
            ->method('populateDTOFromEntity')
            ->with($this->user, UserDTO::class, ['get', 'user'])
            ->willReturn($this->userDTO);

        $userList = $this->userService->getUsers(0, 1);
        $this->assertEquals([$this->userDTO], $userList);
    }
}
