<?php

declare(strict_types=1);

namespace App\DTO\User;

use App\DTO\BaseResponse\BaseResponseSuccessDTO;
use Symfony\Component\Serializer\Annotation\Groups;

class UserCreateResponseSuccessDTO extends BaseResponseSuccessDTO
{
    #[Groups(['get'])]
    public UserDTO $data;

    public function __construct(UserDTO $data)
    {
        $this->data = $data;
    }
}
