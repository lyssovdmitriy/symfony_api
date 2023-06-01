<?php

declare(strict_types=1);

namespace App\DTO\User;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;

final class UserDTO
{

    #[OA\Property(example: '123')]
    #[Groups(['get'])]
    public int $id;

    #[OA\Property(example: 'vasya@pupkin.org')]
    #[Assert\NotBlank(message: 'The email value should not be blank.', groups: ['create'])]
    #[Assert\Email(message: 'The email {{ value }} is not a valid email.')]
    #[Groups(['get', 'create'])]
    public string $email;

    #[OA\Property(example: '1234qwerty')]
    #[Assert\NotBlank(message: 'The password value should not be blank.', groups: ['create'])]
    #[Groups(['create', 'update'])]
    public string $password;

    #[OA\Property(example: '1980-01-25')]
    #[Assert\NotBlank(message: 'The birthday value should not be blank.', groups: ['create'])]
    #[Assert\Date(message: 'The date {{ value }} is not a valid date.')]
    #[Groups(['get', 'create', 'update'])]
    public string $birthday;

    #[OA\Property(example: '+98123456789')]
    #[Assert\NotBlank(message: 'The phone value should not be blank.', groups: ['create'])]
    #[Groups(['get', 'create', 'update'])]
    public string $phone;

    #[OA\Property(example: 'Mozart street 1')]
    #[Assert\NotBlank(message: 'The address value should not be blank.', groups: ['create'])]
    #[Groups(['get', 'create', 'update'])]
    public string $address;
}
