<?php

namespace App\Tests\Service;

use App\DTO\User\UserDTO;
use App\Service\ValidationService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationServiceTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private ValidatorInterface $validator;
    private ValidationService $validationService;

    protected function setUp(): void
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->validationService = new ValidationService($this->validator);
    }

    public function testValidateUserCreateDTOSuccess(): void
    {
        $dto = new UserDTO();
        $dto->email = 'test@example.com';
        $dto->password = 'password123';
        $dto->birthday = '1990-01-01';
        $dto->phone = '1234567890';
        $dto->address = '123 Main St';

        $constraintViolationList = new ConstraintViolationList();
        $this->validator->expects($this->once())
            ->method('validate')
            ->with($dto)
            ->willReturn($constraintViolationList);

        $errors = $this->validationService->validateUserDTO($dto);

        $this->assertEmpty($errors);
    }

    public function testValidateUserCreateDTOFailure(): void
    {
        $dto = new UserDTO();

        /** @var \PHPUnit\Framework\MockObject\MockObject */
        $constraintViolation1 = $this->createMock(ConstraintViolationInterface::class);
        $constraintViolation1->expects($this->once())
            ->method('getMessage')
            ->willReturn('The email value should not be blank.');

        /** @var \PHPUnit\Framework\MockObject\MockObject */
        $constraintViolation2 = $this->createMock(ConstraintViolationInterface::class);
        $constraintViolation2->expects($this->once())
            ->method('getMessage')
            ->willReturn('The password value should not be blank.');

        $constraintViolationList = new ConstraintViolationList([$constraintViolation1, $constraintViolation2]);
        $this->validator->expects($this->once())
            ->method('validate')
            ->with($dto)
            ->willReturn($constraintViolationList);

        $errors = $this->validationService->validateUserDTO($dto);

        $this->assertCount(2, $errors);
        $this->assertContains('The email value should not be blank.', $errors);
        $this->assertContains('The password value should not be blank.', $errors);
    }
}
