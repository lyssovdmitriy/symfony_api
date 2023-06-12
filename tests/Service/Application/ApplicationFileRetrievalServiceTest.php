<?php

declare(strict_types=1);

namespace App\Tests\Service\Application;

use App\Entity\Application;
use App\Exception\NotFoundException;
use App\Service\Application\ApplicationFileRetrievalService;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ApplicationFileRetrievalServiceTest extends BaseApplicationService
{

    private ApplicationFileRetrievalService $applicationFileRetrievalService;


    protected function setUp(): void
    {
        parent::setUp();

        $this->applicationFileRetrievalService = new ApplicationFileRetrievalService(
            $this->applicationRepository,
            $this->downloadHandler,
        );
    }


    public function testGetFile(): void
    {
        $application = $this->createMock(Application::class);
        $this->applicationRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($application);

        $this->downloadHandler->expects($this->once())
            ->method('downloadObject')
            ->with($application);

        $response = $this->applicationFileRetrievalService->getFile(1);

        $this->assertInstanceOf(StreamedResponse::class, $response);
    }

    public function testGetFileThrowsNotFoundException()
    {
        $this->applicationRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->applicationFileRetrievalService->getFile(1);
    }
}
