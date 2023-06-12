<?php

declare(strict_types=1);

namespace App\Tests\Service\Application;

use App\Entity\Application;
use App\Service\Application\ApplicationFileAttachmentService;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class ApplicationFileAttachmentServiceTest extends BaseApplicationService
{

    private ApplicationFileAttachmentService $applicationFileAttachmentService;


    protected function setUp(): void
    {
        parent::setUp();

        $this->applicationFileAttachmentService = new ApplicationFileAttachmentService(
            $this->applicationRepository,
            $this->uploadHandler,
        );
    }


    public function testAttachFile(): void
    {
        $file = $this->createMock(UploadedFile::class);
        $application = $this->createMock(Application::class);

        $this->applicationRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($application);

        $application->expects($this->once())
            ->method('setApplicationFile')
            ->with($file);

        $this->uploadHandler->expects($this->once())
            ->method('upload')
            ->with($application);

        $this->applicationRepository->expects($this->once())
            ->method('save')
            ->with($application, true);

        $this->applicationFileAttachmentService->attachFile($file, 1);
    }
}
