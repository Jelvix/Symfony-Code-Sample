<?php

namespace App\EventSubscriber\HttpException;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class UnauthorizedHandler implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException'
        ];
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ($event->getException() instanceof UnauthorizedHttpException) {
            $event->setResponse(new JsonResponse(['message' => $event->getException()->getMessage()],JsonResponse::HTTP_UNAUTHORIZED));
        } else if ($event->getException() instanceof AccessDeniedHttpException) {
            $event->setResponse(new JsonResponse(['message' => $event->getException()->getMessage()],JsonResponse::HTTP_FORBIDDEN));
        }
    }
}