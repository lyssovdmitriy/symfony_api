<?php

declare(strict_types=1);

namespace App\Service\Application;

use App\DTO\Application\ApplicationDTO;
use App\Exception\NotFoundException;
use App\Repository\ApplicationRepository;
use App\Service\Utils\PopulateServiceInterface;

final class ApplicationRetrievalService implements ApplicationRetrievalServiceInterface
{
    public function __construct(
        private ApplicationRepository $applicationRepository,
        private PopulateServiceInterface $populateService,
    ) {
    }

    public function getApplicationById(int $id): ApplicationDTO
    {
        $application = $this->applicationRepository->findOneBy(['id' => $id]);

        if (null === $application) {
            throw new NotFoundException('Application not found');
        }

        /** @var ApplicationDTO */
        $applicationDTO = $this->populateService->populateDTOFromEntity($application, ApplicationDTO::class, ['get', 'application']);
        return $applicationDTO;
    }
}
