<?php

declare(strict_types=1);


namespace App\EventListener;

use App\DTO\BaseResponse\BaseResponseSuccessDataDTO;
use App\Service\Utils\ResponseService;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTFailureEventInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;

class JWTAuthenticationListener
{

    public function __construct(
        private ResponseService $responseService
    ) {
    }

    /**
     * @param AuthenticationFailureEvent $event
     */
    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event): void
    {
        $this->setErrorResponse($event, JsonResponse::HTTP_UNAUTHORIZED, 'Bad credentials, please verify that your email/password are correctly set');
    }

    /**
     * @param JWTInvalidEvent $event
     */
    public function onJWTInvalid(JWTInvalidEvent $event): void
    {
        $this->setErrorResponse($event, JsonResponse::HTTP_FORBIDDEN, 'Your token is invalid, please login again to get a new one');
    }

    /**
     * @param JWTNotFoundEvent $event
     */
    public function onJWTNotFound(JWTNotFoundEvent $event): void
    {
        $this->setErrorResponse($event, JsonResponse::HTTP_FORBIDDEN, 'Missing JWT token');
    }

    /**
     * @param JWTExpiredEvent $event
     */
    public function onJWTExpired(JWTExpiredEvent $event): void
    {
        $this->setErrorResponse($event, JsonResponse::HTTP_UNAUTHORIZED, 'Your token is expired, please renew it.');
    }

    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): void
    {
        $event->setData((array)new BaseResponseSuccessDataDTO($event->getData()));
    }

    private function setErrorResponse(AuthenticationFailureEvent|JWTFailureEventInterface $event, int $httpCode, string $message): void
    {
        $event->setResponse(
            $this->responseService->createErrorResponse(
                $httpCode,
                $message,
            )
        );
    }
}
