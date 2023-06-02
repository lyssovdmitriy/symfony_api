<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Application\ApplicationDTO;
use App\Entity\Application;
use App\Entity\User;
use App\Exception\NotFountException;
use App\Repository\ApplicationRepository;
use DateTimeImmutable;

class ApplicationService
{

    public function __construct(
        private ApplicationRepository $applicationRepository,
    ) {
    }

    public function createApplication(ApplicationDTO $applicationDTO, User $user): Application
    {
        $application = new Application();
        $application->setUser($user);
        $application->setTitle($applicationDTO->title);
        $application->setNumber($applicationDTO->number);
        $application->setDate(new DateTimeImmutable($applicationDTO->date));

        $this->applicationRepository->save($application, true);

        return $application;
    }

    public function getApplicationById(int $id): Application
    {
        $app = $this->applicationRepository->getApplicationById($id);
        if (null === $app) {
            throw new NotFountException('Application not found');
        }
        return $app;
    }
}
