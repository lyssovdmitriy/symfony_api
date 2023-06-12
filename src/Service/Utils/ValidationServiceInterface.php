<?php

declare(strict_types=1);

namespace App\Service\Utils;


interface ValidationServiceInterface
{
    /**
     * @param object $dto
     * @return mixed[] $errors
     */
    public function validateDTO(object $dto): array;
}
