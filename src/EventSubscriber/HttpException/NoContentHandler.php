<?php

namespace App\EventSubscriber\HttpException;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class NoContentHandler implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException'
        ];
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if($event->getException() instanceof HttpException && $event->getException()->getCode() === Response::HTTP_NO_CONTENT) {
            $event->setResponse(new JsonResponse(['message' => $event->getException()->getMessage()], JsonResponse::HTTP_NO_CONTENT));
        }
    }

}