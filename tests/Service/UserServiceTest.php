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

        $this->populateService->expects($this->once())
            ->method('populateDTOFromEntity')
            ->willReturn($dto);

        $user = $this->userService->createUser($dto);
        $this->assertInstanceOf(UserDTO::class, $user);
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

        $this->expectException(UserCreateConflictException::class);
        $this->expectExceptionMessage('User already exists.');
        $this->userService->createUser($dto);
    }

    public function testGetUser(): void
    {
        $user = $this->createMock(User::class);
        $dto = new UserDTO();
        $dto->email = 'test@example.com';
        $dto->password = 'password123';
        $dto->address = '123 Main St';
        $dto->birthday = '1990-01-01';
        $dto->phone = '1234567890';

        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($user);

        $this->populateService->expects($this->once())
            ->method('populateDTOFromEntity')
            ->with($user, UserDTO::class)
            ->willReturn($dto);

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
        $user = $this->createMock(User::class);

        $dto = new UserDTO();
        $dto->password = 'password123';
        $dto->address = '123 Main St';
        $dto->birthday = '1990-01-01';
        $dto->phone = '1234567890';

        $hashedPassword = 'hashed_password';
        $this->passwordHasher->expects($this->once())
            ->method('hashPassword')
            ->with($user, $dto->password)
            ->willReturn($hashedPassword);

        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($user);

        $this->userRepository->expects($this->once())
            ->method('save')
            ->with($user, true);

        $user->expects($this->once())
            ->method('setAddress')
            ->with($dto->address);

        $user->expects($this->once())
            ->method('setBirthday')
            ->with(new DateTimeImmutable($dto->birthday));

        $user->expects($this->once())
            ->method('setPhone')
            ->with($dto->phone);

        $user->expects($this->once())
            ->method('setPassword')
            ->with($hashedPassword);

        $this->userService->updateUser(1, $dto);
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
        $user = $this->createMock(User::class);

        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($user);

        $this->userRepository->expects($this->once())
            ->method('remove')
            ->with($user, true);

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
        $user = $this->createMock(User::class);

        $dto = new UserDTO();
        $dto->email = 'test@example.com';
        $dto->password = 'password123';
        $dto->address = '123 Main St';
        $dto->birthday = '1990-01-01';
        $dto->phone = '1234567890';


        $this->userRepository->expects($this->once())
            ->method('findBy')
            ->willReturn([$user]);

        $this->populateService->expects($this->once())
            ->method('populateDTOFromEntity')
            ->with($user, UserDTO::class, ['get', 'user'])
            ->willReturn($dto);

        $userList = $this->userService->getUsers(0, 1);
        $this->assertEquals([$dto], $userList);
    }
}
