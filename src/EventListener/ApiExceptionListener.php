<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Exception\ApiException;
use App\Exception\UserCreateConflictException;
use App\Exception\NotFountException;
use App\RequestHandler\UserRequestHandler;
use App\Service\ResponseService;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

final class ApiExceptionListener
{

    public function __construct(
        private ResponseService $responseService,
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HttpExceptionInterface) {
            $errors = [];
            if ($exception instanceof ApiException) {
                $errors = $exception->getErrors();
            }
            $event->setResponse($this->responseService->createErrorResponse($exception->getStatusCode(), $exception->getMessage(), $errors, $exception->getCode()));
        }

        if ($exception instanceof UserCreateConflictException) {
            $event->setResponse($this->responseService->createErrorResponse(UserRequestHandler::USER_CREATE_CONFLICT_ERROR, $exception->getMessage(), [], $exception->getCode()));
        }

        if ($exception instanceof NotFountException) {
            $event->setResponse($this->responseService->createErrorResponse(UserRequestHandler::USER_NOT_FOUND, $exception->getMessage(), [], $exception->getCode()));
        }
    }
}
