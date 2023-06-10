<?php

declare(strict_types=1);

namespace App\Service\Application;

use App\DTO\Application\ApplicationDTO;
use App\Entity\Application;
use App\Exception\NotFountException;
use App\Repository\ApplicationRepository;
use App\Repository\UserRepository;
use App\Service\PopulateService;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Vich\UploaderBundle\Handler\UploadHandler;
use Vich\UploaderBundle\Handler\DownloadHandler;

class ApplicationService implements ApplicationServiceInterface
{

    public function __construct(
        private ApplicationRepository $applicationRepository,
        private UserRepository $userRepository,
        private PopulateService $populateService,
        private UploadHandler $uploadHandler,
        private DownloadHandler $downloadHandler,
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

    public function getApplicationById(int $id): ApplicationDTO
    {
        $application = $this->applicationRepository->findOneBy(['id' => $id]);

        if (null === $application) {
            throw new NotFountException('Application not found');
        }

        /** @var ApplicationDTO */
        $applicationDTO = $this->populateService->populateDTOFromEntity($application, ApplicationDTO::class, ['get', 'application']);
        return $applicationDTO;
    }


    /** @throws \Vich\UploaderBundle\Exception\MappingNotFoundException */
    public function attachFile(UploadedFile $uploadedFile, int $applicationId): void
    {
        $application = $this->applicationRepository->findOneBy(['id' => $applicationId]) ?? throw new NotFountException("Application $applicationId not found");
        $application->setApplicationFile($uploadedFile);

        $this->uploadHandler->upload($application, 'applicationFile');

        $this->applicationRepository->save($application, true);
    }

    /** @throws \App\Exception\NotFountException */
    public function getFile(int $applicationId): StreamedResponse
    {
        $application = $this->applicationRepository->findOneBy(['id' => $applicationId]) ?? throw new NotFountException("Application $applicationId not found");
        return $this->downloadHandler->downloadObject($application, 'applicationFile');
    }
}
