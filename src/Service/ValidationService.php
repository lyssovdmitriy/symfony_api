<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\User\UserDTO;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationService
{
    public function __construct(
        private ValidatorInterface $validator
    ) {
    }

    public function validateUserCreateDTO(UserDTO $dto): array
    {
        $list = $this->validator->validate($dto);
        return $this->getErrorsFromConstraintViolation($list);
    }

    private function getErrorsFromConstraintViolation(ConstraintViolationListInterface $violationList): array
    {
        $errorMessages = [];
        /** @var ConstraintViolation $violation **/
        foreach ($violationList as $violation) {
            $errorMessages[] = $violation->getMessage();
        }

        return $errorMessages;
    }
}
