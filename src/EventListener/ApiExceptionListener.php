<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Exception\ApiException;
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
    }
}
