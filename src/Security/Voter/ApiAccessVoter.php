<?php
/**
 * Created by PhpStorm.
 * User: Alexey Itsekson
 * Date: 25.01.2019
 * Time: 23:32
 */

namespace App\Security\Voter;

use App\Entity\User;
use App\Security\PermissionsProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ApiAccessVoter extends Voter
{
    private $permissionsProvider;

    public function __construct(PermissionsProviderInterface $permissionsProvider)
    {
        $this->permissionsProvider = $permissionsProvider;
    }

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['GET', 'POST', 'PUT', 'DELETE'])
            && is_string($subject);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            $user = new User();
            $user->setRoles(['ROLE_GUEST']);
        }

        return $this->permissionsProvider->isGranted($user, $subject, $attribute);

    }
}
