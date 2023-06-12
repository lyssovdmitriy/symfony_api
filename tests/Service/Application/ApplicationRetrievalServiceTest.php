<?php

declare(strict_types=1);

namespace App\Tests\Service\Application;

use App\DTO\Application\ApplicationDTO;
use App\Entity\Application;
use App\Exception\NotFoundException;
use App\Service\Application\ApplicationRetrievalService;

final class ApplicationRetrievalServiceTest extends BaseApplicationService
{

    private ApplicationRetrievalService $applicationRetrievalService;


    protected function setUp(): void
    {
        parent::setUp();

        $this->applicationRetrievalService = new ApplicationRetrievalService(
            $this->applicationRepository,
            $this->populateService,
        );
    }

    public function testGetApplicationById()
    {
        $application = $this->createMock(Application::class);
        $dto = new ApplicationDTO();

        $this->applicationRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($application);

        $this->populateService->expects($this->once())
            ->method('populateDTOFromEntity')
            ->with($application, ApplicationDTO::class)
            ->willReturn($dto);

        $applicationDTO = $this->applicationRetrievalService->getApplicationById(1);

        $this->assertInstanceOf(ApplicationDTO::class, $applicationDTO);
    }

    public function testGetApplicationByIdThrowsNotFoundException()
    {
        $this->applicationRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->applicationRetrievalService->getApplicationById(1);
    }
}
