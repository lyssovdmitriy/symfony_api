<?php

namespace App\Tests\Service\Application;

use App\Repository\ApplicationRepository;
use App\Repository\UserRepository;
use App\Service\Utils\PopulateServiceInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Vich\UploaderBundle\Handler\DownloadHandler;
use Vich\UploaderBundle\Handler\UploadHandler;

abstract class BaseApplicationService extends TestCase
{
    protected ApplicationRepository&MockObject $applicationRepository;
    protected UserRepository&MockObject $userRepository;
    protected PopulateServiceInterface&MockObject $populateService;
    protected UploadHandler&MockObject $uploadHandler;
    protected DownloadHandler&MockObject $downloadHandler;

    protected function setUp(): void
    {
        $this->applicationRepository = $this->createMock(ApplicationRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->populateService = $this->createMock(PopulateServiceInterface::class);
        $this->uploadHandler = $this->createMock(UploadHandler::class);
        $this->downloadHandler = $this->createMock(DownloadHandler::class);
    }
}
