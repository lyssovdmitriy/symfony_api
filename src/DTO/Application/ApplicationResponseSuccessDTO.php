<?php

declare(strict_types=1);

namespace App\DTO\Application;

use App\DTO\BaseResponse\BaseResponseSuccessDTO;
use Symfony\Component\Serializer\Annotation\Groups;

class ApplicationResponseSuccessDTO extends BaseResponseSuccessDTO
{
    #[Groups(['get', 'update'])]
    public ApplicationDTO $data;

    public function __construct(ApplicationDTO $data)
    {
        $this->data = $data;
    }
}
