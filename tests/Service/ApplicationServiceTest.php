<?php

namespace App\Tests\Service;

use App\DTO\Application\ApplicationDTO;
use App\Entity\Application;
use App\Entity\User;
use App\Exception\NotFountException;
use App\Repository\ApplicationRepository;
use App\Repository\UserRepository;
use App\Service\Application\ApplicationService;
use App\Service\PopulateService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Vich\UploaderBundle\Handler\DownloadHandler;
use Vich\UploaderBundle\Handler\UploadHandler;

class ApplicationServiceTest extends TestCase
{

    private ApplicationService $applicationService;

    /** @var UserRepository&MockObject */
    private MockObject $userRepository;

    /** @var ApplicationRepository&MockObject */
    private MockObject $applicationRepository;

    /** @var PopulateService&MockObject */
    private MockObject $populateService;

    /** @var UploadHandler&MockObject */
    private UploadHandler $uploadHandler;

    /** @var DownloadHandler&MockObject */
    private DownloadHandler $downloadHandler;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->applicationRepository = $this->createMock(ApplicationRepository::class);
        $this->populateService = $this->createMock(PopulateService::class);
        $this->uploadHandler = $this->createMock(UploadHandler::class);
        $this->downloadHandler = $this->createMock(DownloadHandler::class);

        $this->applicationService = new ApplicationService(
            $this->applicationRepository,
            $this->userRepository,
            $this->populateService,
            $this->uploadHandler,
            $this->downloadHandler,
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

        $applicationDTO = $this->applicationService->createApplication($dto, 1);
        $this->assertEquals($dto, $applicationDTO);
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

        $applicationDTO = $this->applicationService->getApplicationById(1);

        $this->assertInstanceOf(ApplicationDTO::class, $applicationDTO);
    }

    public function testGetApplicationByIdNull()
    {
        $this->applicationRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->expectException(NotFountException::class);

        $this->applicationService->getApplicationById(1);
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

        $this->applicationService->attachFile($file, 1);
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

        $response = $this->applicationService->getFile(1);

        $this->assertInstanceOf(StreamedResponse::class, $response);
    }

    public function testGetFileNull()
    {
        $this->applicationRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->expectException(NotFountException::class);

        $this->applicationService->getFile(1);
    }
}
