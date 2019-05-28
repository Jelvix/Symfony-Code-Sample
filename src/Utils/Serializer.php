<?php
/**
 * Created by PhpStorm.
 * User: Alexey Itsekson
 * Date: 02.02.2019
 * Time: 20:33
 */

namespace App\Utils;

use Symfony\Component\Serializer\SerializerInterface;

class Serializer implements SerializerInterface
{
    private $_serializer;
    public function __construct(\JMS\Serializer\SerializerInterface $jmsSerializer)
    {
        $this->_serializer = $jmsSerializer;
    }

    public function serialize($data, $format, array $context = array())
    {
        return $this->_serializer->serialize($data, $format, $context);
    }

    public function deserialize($data, $type, $format, array $context = array())
    {
        return $this->_serializer->deserialize($data, $type, $format, $context);
    }


}