<?php
/**
 * Created by PhpStorm.
 * User: Alexey Itsekson
 * Date: 02.02.2019
 * Time: 22:47
 */

namespace App\Serializer\EventSubscriber;


use App\Entity\User;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;

class UserSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            [
                'event' => 'serializer.pre_deserialize',
                'method' => 'onPreDeserialize',
                'class' => User::class,
                'format' => 'json',
                'priority' => 0,
            ],
        ];
    }


    public function onPreDeserialize(PreDeserializeEvent $event)
    {
        $context = $event->getContext();
        $group = $context->hasAttribute('groups') ? current($context->getAttribute('groups')) : null;
        $data = $event->getData();
        if ($group === 'external') {
            if (isset($data['fb'])) {
                $data['fb_email'] = ((array)$data['fb'])['email'] ?? '';
            }
            if (isset($data['geo'])) {
                $data['geo_country'] = ((array)$data['geo'])['country'] ?? '';
            }

            $nonEmptyStringFields = ['fb_email', 'geo_country'];
            foreach ($nonEmptyStringFields as $field) {
                if(!isset($data[$field])){
                    $data[$field] = '';
                }
            }

        }
        if ($group === 'webpanel') {

            if (isset($data['fb_email'])) {
                if (!isset($data['fb'])) {
                    $data['fb'] = [];
                }
                $data['fb']['email'] = $data['fb_email'];
            }
            if (isset($data['geo_country'])) {
                if (!isset($data['geo'])) {
                    $data['geo'] = [];
                }
                $data['geo']['country'] = $data['geo_country'];
            }
        }

        $event->setData($data);
    }
}