<?php

namespace App\Transformer;

use App\Entity\User;

class UserTransformer
{
    /**
     * @param User $user
     * @return mixed[]
     */
    public function transform(User $user): array
    {
        return [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'address' => $user->getAddress(),
            'phone' => $user->getPhone(),
            'birthday' => $user->getBirthday(),
        ];
    }
}
