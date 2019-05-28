<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

interface PermissionsProviderInterface
{
    /**
     * @param UserInterface $user
     * @param $resource
     * @param null|string $action
     * @return bool
     */
    public function isGranted(UserInterface $user, $resource, $action = null): bool;
}
