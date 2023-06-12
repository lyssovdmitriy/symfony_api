<?php

namespace App\Tests\Service\User;

use App\DTO\User\UserDTO;
use App\Exception\NotFoundException;
use App\Service\User\UserListService;

class UserListServiceTest extends BaseUserServiceTest
{

    private UserListService $userListService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userListService = new UserListService(
            $this->userRepository,
            $this->populateService,
        );
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

        $userList = $this->userListService->getUsers(0, 1);
        $this->assertEquals([$this->userDTO], $userList);
    }
}
