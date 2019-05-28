<?php
/**
 * Created by PhpStorm.
 * User: Alexey Itsekson
 * Date: 25.01.2019
 * Time: 23:32
 */

namespace App\Security\EventListener;

use App\Entity\Permission;
use App\Entity\User;
use App\Security\PermissionsProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;

class PermissionsCheckListener implements EventSubscriberInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var PermissionsProviderInterface
     */
    private $permissionsProvider;

    public function __construct(TokenStorageInterface $tokenStorage, PermissionsProviderInterface $permissionsProvider)
    {
        $this->tokenStorage = $tokenStorage;
        $this->permissionsProvider = $permissionsProvider;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        if (!$this->tokenStorage->getToken() ||
            !($this->tokenStorage->getToken()->getUser() instanceof UserInterface)) {
            $user = new User();
        } else {
            $user = $this->tokenStorage->getToken()->getUser();
        }
        $route = $event->getRequest()->attributes->get('_route');
        if ($route === null) {
            return;
        }
        if (!$this->permissionsProvider->isGranted($user, $route, $event->getRequest()->getMethod())) {
            throw new AccessDeniedException('access denied');
        }

    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [KernelEvents::CONTROLLER => 'onKernelController'];
    }


}