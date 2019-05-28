<?php
/**
 * Created by PhpStorm.
 * User: Alexey Itsekson
 * Date: 04.02.2019
 * Time: 13:43
 */

namespace App\Serializer\EventSubscriber;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

class NormalizeFieldNamingSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            [
                'event' => 'serializer.pre_deserialize',
                'method' => 'onPreDeserialize',
                'format' => 'json',
                'priority' => 0,
            ],
        ];
    }


    public function onPreDeserialize(PreDeserializeEvent $event)
    {
        $data = $event->getData();
        if (!is_array($data)) {
            return;
        }
        $res = [];
        foreach ($data as $key => $item) {
            $res[$this->normalizeName($key)] = $item;
        }
        $event->setData($res);
    }

    private function normalizeName($name)
    {
        $normalizer = new CamelCaseToSnakeCaseNameConverter();
        return $normalizer->normalize($name);
    }

}