<?php
/**
 * Created by PhpStorm.
 * User: Alexey Itsekson
 * Date: 25.01.2019
 * Time: 23:32
 */

namespace App\Security\Voter;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ApiResourcesVoter extends Voter
{
    private $em;
    private $cachePool;
    private $router;

    public function __construct(CacheItemPoolInterface $cache, EntityManagerInterface $em, RouterInterface $router)
    {
        $this->cachePool = $cache;
        $this->em = $em;
        $this->router = $router;
    }

    protected function supports($attribute, $subject)
    {
        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        return false;
    }
}
