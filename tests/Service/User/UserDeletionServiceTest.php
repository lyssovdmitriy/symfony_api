<?php

namespace App\Tests\Service\User;

use App\Exception\NotFoundException;
use App\Service\User\UserDeletionService;

class UserDeletionServiceTest extends BaseUserServiceTest
{

    private UserDeletionService $userDeletionService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userDeletionService = new UserDeletionService(
            $this->userRepository,
        );
    }

    public function testDeleteUser(): void
    {
        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($this->user);

        $this->userRepository->expects($this->once())
            ->method('remove')
            ->with($this->user, true);

        $this->userDeletionService->deleteUser(1);
    }

    public function testDeleteUserNull(): void
    {
        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->userDeletionService->deleteUser(1);
    }
}
