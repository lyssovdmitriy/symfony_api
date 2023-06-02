<?php

declare(strict_types=1);

namespace App\DTO\BaseResponse;

use OpenApi\Attributes as OA;

class BaseResponseErrorDTO
{

    #[OA\Property(type: 'bool', example: false)]
    public bool $success = false;

    /**
     * @var mixed[]
     */
    #[OA\Property(
        type: 'array',
        items: new OA\Items(
            properties: [
                new OA\Property(type: 'int', property: 'code', example: 400),
                new OA\Property(type: 'string', property: 'message', example: 'Bad request'),
                new OA\Property(type: 'array', property: 'details', items: new OA\Items(type: 'string')),
            ]
        )
    )]
    public array $error;


    /**
     * @param integer $code
     * @param string $message
     * @param mixed[] $details
     */
    public function __construct(int $code, string $message, array $details)
    {
        $this->error = [
            'code' => $code,
            'message' => $message,
            'details' => $details
        ];
    }
}
