<?php

namespace App\Tests\Service\User;

use App\DTO\User\UserDTO;
use App\Exception\NotFoundException;
use App\Service\User\UserRetrievalService;

class UserRetrievalServiceTest extends BaseUserService
{

    private UserRetrievalService $userRetrievalService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRetrievalService = new UserRetrievalService(
            $this->userRepository,
            $this->populateService,
        );
    }

    public function testGetUser(): void
    {
        $id = 1;
        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => $id])
            ->willReturn($this->user);

        $this->populateService->expects($this->once())
            ->method('populateDTOFromEntity')
            ->with($this->user, UserDTO::class)
            ->willReturn($this->userDTO);

        $userDto = $this->userRetrievalService->getUser($id);
        $this->assertInstanceOf(UserDTO::class, $userDto);
    }

    public function testGetUserThrowsNotFoundException(): void
    {
        $id = 1;
        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => $id])
            ->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->userRetrievalService->getUser($id);
    }
}
