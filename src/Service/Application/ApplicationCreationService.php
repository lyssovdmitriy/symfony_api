<?php

declare(strict_types=1);

namespace App\Service\Application;

use App\DTO\Application\ApplicationDTO;
use App\Entity\Application;
use App\Repository\ApplicationRepository;
use App\Repository\UserRepository;
use App\Service\Utils\PopulateServiceInterface;
use DateTimeImmutable;

final class ApplicationCreationService implements ApplicationCreationServiceInterface
{

    public function __construct(
        private UserRepository $userRepository,
        private ApplicationRepository $applicationRepository,
        private PopulateServiceInterface $populateService,
    ) {
    }

    public function createApplication(ApplicationDTO $applicationDTO, int $userId): ApplicationDTO
    {
        $user = $this->userRepository->findOneBy(['id' => $userId]);

        $application = new Application();
        $application->setUser($user);
        $application->setTitle($applicationDTO->title);
        $application->setNumber($applicationDTO->number);
        $application->setDate(new DateTimeImmutable($applicationDTO->date));
        $application->setUpdatedAt(new DateTimeImmutable());

        $this->applicationRepository->save($application, true);

        /** @var ApplicationDTO */
        $applicationDTO = $this->populateService->populateDTOFromEntity($application, ApplicationDTO::class, ['application', 'get']);

        return $applicationDTO;
    }
}
