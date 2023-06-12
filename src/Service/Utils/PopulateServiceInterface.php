<?php

declare(strict_types=1);

namespace App\Service\Utils;

interface PopulateServiceInterface
{
    /**
     * @template T of object
     * @param object $entity
     * @param class-string<T> $type
     * @param string[] $groups
     * @return T
     */
    public function populateDTOFromEntity(object $entity, string $type, array $groups): object;
}
