<?php

declare(strict_types=1);

namespace App\Service\Utils;

use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\SerializerInterface;

class PopulateService implements PopulateServiceInterface
{

    public function __construct(
        private SerializerInterface $serializer,
    ) {
    }


    /** @inheritDoc */
    public function populateDTOFromEntity(object $entity, string $type, array $groups): object
    {

        $context = (new ObjectNormalizerContextBuilder())
            ->withGroups($groups)
            ->toArray();

        return $this->serializer->deserialize(
            $this->serializer->serialize($entity, 'json', $context),
            $type,
            'json',
            $context
        );
    }
}
