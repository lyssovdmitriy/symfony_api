<?php

declare(strict_types=1);

namespace App\DTO\Application;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;

final class ApplicationDTO
{

    #[OA\Property(example: '123')]
    #[Groups(['get'])]
    public int $id;

    #[OA\Property(example: 'Harz-4 Antrag')]
    #[Assert\NotBlank(message: 'The title value should not be blank.', groups: ['create'])]
    #[Groups(['get', 'create', 'update'])]
    public string $title;

    #[OA\Property(example: '2023-01-25')]
    #[Assert\NotBlank(message: 'The date value should not be blank.', groups: ['create'])]
    #[Assert\Date(message: 'The date {{ value }} is not a valid date.')]
    #[Groups(['get', 'create', 'update'])]
    public string $date;


    #[OA\Property(example: '123456A-789B')]
    #[Assert\NotBlank(message: 'The number value should not be blank.', groups: ['create'])]
    #[Groups(['get', 'create', 'update'])]
    public string $number;

    #[Groups(['get'])]
    #[OA\Property(type: 'string', example: 'applications/1/file')]
    public ?string $file;
}
