<?php

declare(strict_types=1);

namespace App\RequestHandler\User;

use App\DTO\User\UserDTO;
use App\Exception\ApiException;
use App\Serializer\DataSerializerInterface;
use App\Service\Utils\DTOValidationTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseUserRequestHandler
{

    use DTOValidationTrait;


    protected function deserializeUserDTO(DataSerializerInterface $serializer, Request $request): UserDTO
    {
        try {
            $dto = $serializer->deserialize($request->getContent(), UserDTO::class);
        } catch (\Throwable $th) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $th->getMessage(), ['JSON parsing error: Invalid JSON syntax'], $th);
        }
        return $dto;
    }
}
