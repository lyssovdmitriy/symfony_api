<?php

namespace App\Tests\Service\User;

use App\DTO\User\UserDTO;
use App\Exception\NotFoundException;
use App\Service\User\UserUpdateService;
use DateTimeImmutable;

class UserUpdateServiceTest extends BaseUserServiceTest
{

    private UserUpdateService $userUpdateService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userUpdateService = new UserUpdateService(
            $this->userRepository,
            $this->passwordHasher,
        );
    }

    public function testUpdateUser(): void
    {
        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($this->user);

        $hashedPassword = 'hashed_password';
        $this->passwordHasher->expects($this->once())
            ->method('hashPassword')
            ->with($this->user, $this->userDTO->password)
            ->willReturn($hashedPassword);


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

        $this->userUpdateService->updateUser(1, $this->userDTO);
    }

    public function testUpdateUserThrowsNotFoundException(): void
    {
        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->userUpdateService->updateUser(1, new UserDTO());
    }
}
