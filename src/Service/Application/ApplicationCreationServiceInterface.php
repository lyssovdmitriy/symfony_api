<?php

declare(strict_types=1);

namespace App\Service\Application;

use App\DTO\Application\ApplicationDTO;

interface ApplicationCreationServiceInterface
{
    public function createApplication(ApplicationDTO $applicationDTO, int $userId): ApplicationDTO;
}
