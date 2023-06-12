<?php

declare(strict_types=1);

namespace App\Serializer;

interface DataSerializerInterface
{
    public function serialize(object $data): string;

    /**
     * @template T of object
     * @param string $jsonData
     * @param class-string<T> $className
     * @return T
     */
    public function deserialize(string $jsonData, string $className): object;
}
