<?php

declare(strict_types=1);

namespace App\Service\Application;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ApplicationFileAttachmentServiceInterface
{
    public function attachFile(UploadedFile $uploadedFile, int $applicationId): void;
}
