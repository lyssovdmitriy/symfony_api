<?php

declare(strict_types=1);

namespace App\Serializer;

use Symfony\Component\Serializer\SerializerInterface;

class JsonDataSerializer implements DataSerializerInterface
{

    public function __construct(
        private SerializerInterface $serializer
    ) {
    }

    public function serialize(object $data): string
    {
        return $this->serializer->serialize($data, 'json');
    }

    public function deserialize(string $jsonData, string $className): object
    {
        return $this->serializer->deserialize($jsonData, $className, 'json');
    }
};
