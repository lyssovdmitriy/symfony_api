<?php

declare(strict_types=1);

namespace App\DTO\BaseResponse;

use App\DTO\BaseResponse\BaseResponseSuccessDTO;

class BaseResponseSuccessDataDTO extends BaseResponseSuccessDTO
{
    /**
     * @param mixed[] $data
     */
    public function __construct(
        public readonly array $data,
    ) {
    }
}
