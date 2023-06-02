<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationService
{
    public function __construct(
        private ValidatorInterface $validator
    ) {
    }

    /**
     * @param object $dto
     * @return mixed[]
     */
    public function validateDTO(object $dto): array
    {
        $list = $this->validator->validate($dto);
        return $this->getErrorsFromConstraintViolation($list);
    }

    /**
     * @param ConstraintViolationListInterface $violationList
     * @return mixed[]
     */
    private function getErrorsFromConstraintViolation(ConstraintViolationListInterface $violationList): array
    {
        $errorMessages = [];
        foreach ($violationList as $violation) {
            $errorMessages[] = $violation->getMessage();
        }

        return $errorMessages;
    }
}
