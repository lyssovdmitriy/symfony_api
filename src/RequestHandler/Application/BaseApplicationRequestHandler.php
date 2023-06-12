<?php

declare(strict_types=1);

namespace App\RequestHandler\Application;

use App\DTO\Application\ApplicationDTO;
use App\Exception\ApiException;
use App\Serializer\DataSerializerInterface;
use App\Service\Utils\DTOValidationTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseApplicationRequestHandler
{

    use DTOValidationTrait;

    /** @throws \App\Exception\ApiException */
    protected function deserializeApplicationDTO(DataSerializerInterface $serializer, Request $request): ApplicationDTO
    {
        try {
            $dto = $serializer->deserialize($request->getContent(), ApplicationDTO::class);
        } catch (\Throwable $th) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $th->getMessage(), ['JSON parsing error: Invalid JSON syntax'], $th);
        }
        return $dto;
    }
}
