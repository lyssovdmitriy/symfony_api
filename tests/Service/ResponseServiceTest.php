<?php

namespace App\Tests\Service;

use App\DTO\BaseResponse\BaseResponseErrorDTO;
use App\DTO\User\UserCreateResponseSuccessDTO;
use App\DTO\User\UserDTO;
use App\Service\ResponseService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ResponseServiceTest extends TestCase
{
    private ResponseService $responseService;

    protected function setUp(): void
    {
        $this->responseService = new ResponseService();
    }

    public function testCreateSuccessResponse(): void
    {
        $dto = new UserCreateResponseSuccessDTO(new UserDTO());
        $response = $this->responseService->createSuccessResponse($dto);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(json_encode($dto), $response->getContent());
    }

    public function testCreateErrorResponse(): void
    {
        $response = $this->responseService->createErrorResponse();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $expectedErrorDto = new BaseResponseErrorDTO(0, 'Something went wrong', []);
        $this->assertEquals(json_encode($expectedErrorDto), $response->getContent());
    }
}
