<?php
/**
 * Created by PhpStorm.
 * User: Alexey Itsekson
 * Date: 22.01.2019
 * Time: 16:31
 */

namespace App\EventSubscriber;


use function json_last_error;
use function json_last_error_msg;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestJsonParser implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'convertJsonStringToArray',
        ];
    }

    public function convertJsonStringToArray(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        if ($request->getContentType() != 'application/json' || !$request->getContent()) {
            return;
        }
        $data = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException('invalid json body: ' . json_last_error_msg());
        }
        $request->request->replace(is_array($data) ? $data : array());
    }


}