<?php

declare(strict_types=1);

namespace App\DTO\BaseResponse;

use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

class BaseResponseSuccessDTO implements ResponseDTOInterface
{

    #[OA\Property(type: 'bool', example: true)]
    #[Groups(['get'])]
    public bool $success = true;
}
