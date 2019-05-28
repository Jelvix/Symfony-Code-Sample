<?php
/**
 * Created by PhpStorm.
 * User: Alexey Itsekson
 * Date: 02.02.2019
 * Time: 21:25
 */

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Context\Context;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\AbstractFOSRestController as BaseController;
use Symfony\Component\HttpFoundation\Response;

class AbstractController extends BaseController
{
    /** @var EntityManagerInterface  */
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param $data
     * @param int $status
     * @param array $headers
     * @param array $options
     * @return JsonResponse
     */
    protected function json($data, int $status = Response::HTTP_OK, array $headers = array(), array $options = array()): JsonResponse
    {
        $view = $this->view($data, $status, $headers);
        if(isset($options['groups'])){
            $context = new Context();
            $context->setGroups([$options['groups']]);
            $view->setContext($context);
        }
        return $this->handleView($view);
    }
}
