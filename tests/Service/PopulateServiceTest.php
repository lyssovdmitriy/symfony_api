<?php

namespace App\Tests\Service;

use App\Service\Utils\PopulateService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\SerializerInterface;

class PopulateServiceTest extends TestCase
{

    /** @var MockObject&SerializerInterface */
    private MockObject $serializer;

    private PopulateService $populateService;

    public function setUp(): void
    {
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->populateService = new PopulateService($this->serializer);
    }

    public function testpopulateDTOFromEntity(): void
    {
        $groups = ['group'];
        $type = 'testType';

        $context = (new ObjectNormalizerContextBuilder())
            ->withGroups($groups)
            ->toArray();

        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->with('', $type, 'json', $context)
            ->willReturn(new stdClass());

        $this->populateService->populateDTOFromEntity(new stdClass(), $type, $groups);
    }
}
