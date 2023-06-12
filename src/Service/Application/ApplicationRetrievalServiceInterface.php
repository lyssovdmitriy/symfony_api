<?php

declare(strict_types=1);

namespace App\Service\Application;

use App\DTO\Application\ApplicationDTO;

interface ApplicationRetrievalServiceInterface
{
    public function getApplicationById(int $id): ApplicationDTO;
}
