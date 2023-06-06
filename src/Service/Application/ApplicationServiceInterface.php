<?php

declare(strict_types=1);

namespace App\Service\Application;

use App\DTO\Application\ApplicationDTO;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface ApplicationServiceInterface
{

    public function createApplication(ApplicationDTO $applicationDTO, int $userId): ApplicationDTO;

    public function getApplicationById(int $id): ApplicationDTO;

    public function attachFile(UploadedFile $uploadedFile, int $applicationId): void;

    public function getFile(int $applicationId): StreamedResponse;
}
