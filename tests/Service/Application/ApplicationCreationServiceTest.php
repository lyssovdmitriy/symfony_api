<?php

declare(strict_types=1);

namespace App\Tests\Service\Application;

use App\DTO\Application\ApplicationDTO;
use App\Entity\Application;
use App\Entity\User;
use App\Service\Application\ApplicationCreationService;

final class ApplicationCreationServiceTest extends BaseApplicationService
{

    private ApplicationCreationService $applicationCreationService;


    protected function setUp(): void
    {
        parent::setUp();

        $this->applicationCreationService = new ApplicationCreationService(
            $this->userRepository,
            $this->applicationRepository,
            $this->populateService,
        );
    }

    public function testCreateApplication(): void
    {
        $dto = new ApplicationDTO();
        $dto->title = 'test title';
        $dto->number = 'test number';
        $dto->date = '1234-01-01';
        $user = new User();

        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($user);

        $this->applicationRepository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Application::class), true);

        $this->populateService->expects($this->once())
            ->method('populateDTOFromEntity')
            ->with($this->isInstanceOf(Application::class), ApplicationDTO::class)
            ->willReturn($dto);

        $applicationDTO = $this->applicationCreationService->createApplication($dto, 1);
        $this->assertEquals($dto, $applicationDTO);
    }
}
