<?php

/**
 * Created By a.itsekson
 */

namespace App\DataFixtures;

use App\Entity\Permission;
use App\Entity\User;
use App\Entity\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TestFixtures extends Fixture implements FixtureGroupInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(UserPasswordEncoderInterface $encoder, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->encoder = $encoder;
    }

    public static function getGroups(): array
    {
        return [
            'dev',
            'test'
        ];
    }


    public function load(ObjectManager $manager)
    {
        if(!in_array(getenv('APP_ENV'), ['dev', 'test'])) {
            return;
        }
        $user = new User();
        $user->setEmail('admin@admin.com');
        $user->setPassword($this->encoder->encodePassword($user, '123456'));
        $user->setRoles(['ROLE_ADMIN']);
        $user->setCreatedAt(new \DateTime());
        $user->setAvatar('/img/no-photo.jpg');
        $user->setDisplayName('Admin');
        $manager->persist($user);

        $user = new User();
        $user->setEmail('support@support.com');
        $user->setPassword($this->encoder->encodePassword($user, '123456'));
        $user->setRoles(['ROLE_SUPPORT']);
        $user->setAvatar('/img/no-photo.jpg');
        $user->setDisplayName('Support Man');
        $user->setCreatedAt(new \DateTime());
        $manager->persist($user);


        $user = new User();
        $user->setEmail('user@user.com');
        $user->setPassword($this->encoder->encodePassword($user, '123456'));
        $user->setRoles(['ROLE_USER']);
        $user->setAvatar('/img/no-photo.jpg');
        $user->setDisplayName('Some Man');
        $user->setCreatedAt(new \DateTime());
        $manager->persist($user);

        $roles = [
            [
                'name' => 'ROLE_GUEST'
            ],
            [
                'name' => 'ROLE_USER'
            ],
            [
                'name' => 'ROLE_SUPPORT',
                'parent' => ['ROLE_USER']
            ],
            [
                'name' => 'ROLE_ADMIN',
                'parent' => ['ROLE_USER', 'ROLE_SUPPORT']
            ]
        ];

        $persistedRoles = [];

        foreach ($roles as $r) {
            $role = new UserRole();
            $role->setName($r['name']);
            $role->setCreatedAt(new \DateTime());
            if (isset($r['parent'])) {
                foreach ($r['parent'] as $parent) {
                    if (isset($persistedRoles[$parent])) {
                        $role->addParentRole($persistedRoles[$parent]);
                    }
                }
            }
            $persistedRoles[$r['name']] = $role;
            $manager->persist($role);
        }


        $permissions = [
            [
                'role' => 'ROLE_GUEST',
                'resource' => 'app.index',
                'action' => 'GET',
                'type' => 'api',
                'is_granted' => true
            ],
            [
                'role' => 'ROLE_SUPPORT',
                'resource' => 'app.index',
                'action' => 'GET',
                'type' => 'api',
                'is_granted' => true
            ],
            [
                'role' => 'ROLE_ADMIN',
                'resource' => 'app.index',
                'action' => 'GET',
                'type' => 'api',
                'is_granted' => true
            ],
            [
                'role' => 'ROLE_ADMIN',
                'resource' => 'app.user_index',
                'action' => 'GET',
                'type' => 'api',
                'is_granted' => true
            ],
            [
                'role' => 'ROLE_GUEST',
                'resource' => 'app.user_index',
                'action' => 'GET',
                'type' => 'api',
                'is_granted' => false
            ],
            [
                'role' => 'ROLE_GUEST',
                'resource' => 'app.swagger_ui',
                'action' => 'GET',
                'type' => 'api',
                'is_granted' => true
            ],
            [
                'role' => 'ROLE_GUEST',
                'resource' => 'app.swagger_json',
                'action' => 'GET',
                'type' => 'api',
                'is_granted' => true
            ],
            [
                'role' => 'ROLE_GUEST',
                'resource' => 'app.auth_login',
                'action' => 'POST',
                'type' => 'api',
                'is_granted' => true
            ],
            [
                'role' => '*',
                'resource' => 'app.auth_acl',
                'action' => 'GET',
                'type' => 'api',
                'is_granted' => true
            ],
            [
                'role' => 'ROLE_GUEST',
                'resource' => 'app.auth_acl',
                'action' => 'GET',
                'type' => 'api',
                'is_granted' => false
            ],
            [
                'role' => 'ROLE_ADMIN',
                'resource' => 'app.permissions_list',
                'action' => 'GET',
                'type' => 'api',
                'is_granted' => true
            ],
            [
                'role' => 'ROLE_ADMIN',
                'resource' => 'app.permissions_create',
                'action' => 'POST',
                'type' => 'api',
                'is_granted' => true
            ],
            [
                'role' => 'ROLE_ADMIN',
                'resource' => 'app.permissions_update',
                'action' => 'PUT',
                'type' => 'api',
                'is_granted' => true
            ],
            [
                'role' => 'ROLE_ADMIN',
                'resource' => 'app.permissions_delete',
                'action' => 'DELETE',
                'type' => 'api',
                'is_granted' => true
            ],
            [
                'role' => 'ROLE_ADMIN',
                'resource' => 'app.user_roles_list',
                'action' => 'GET',
                'type' => 'api',
                'is_granted' => true
            ],
            [
                'role' => 'ROLE_ADMIN',
                'resource' => 'app.user_roles_create',
                'action' => 'POST',
                'type' => 'api',
                'is_granted' => true
            ],
            [
                'role' => 'ROLE_ADMIN',
                'resource' => 'app.user_roles_update',
                'action' => 'PUT',
                'type' => 'api',
                'is_granted' => true
            ],
            [
                'role' => 'ROLE_ADMIN',
                'resource' => 'app.user_roles_delete',
                'action' => 'DELETE',
                'type' => 'api',
                'is_granted' => true
            ],
            [
                'role' => 'ROLE_ADMIN',
                'resource' => 'app.system_users_list',
                'action' => 'GET',
                'type' => 'api',
                'is_granted' => true
            ],
            [
                'role' => 'ROLE_ADMIN',
                'resource' => 'app.system_users_create',
                'action' => 'POST',
                'type' => 'api',
                'is_granted' => true
            ],
            [
                'role' => 'ROLE_ADMIN',
                'resource' => 'app.system_users_update',
                'action' => 'PUT',
                'type' => 'api',
                'is_granted' => true
            ],
            [
                'role' => 'ROLE_ADMIN',
                'resource' => 'app.system_users_delete',
                'action' => 'DELETE',
                'type' => 'api',
                'is_granted' => true
            ],

            // webpanel dummy permissions
            [
                'role' => 'ROLE_GUEST',
                'resource' => '/auth/login',
                'action' => 'view',
                'type' => 'web',
                'is_granted' => true
            ],
            [
                'role' => 'ROLE_GUEST',
                'resource' => '/auth/logout',
                'action' => 'view',
                'type' => 'web',
                'is_granted' => true
            ],
            [
                'role' => 'ROLE_SUPPORT',
                'resource' => '/pages/dashboard',
                'action' => 'view',
                'type' => 'web',
                'is_granted' => true
            ],
            [
                'role' => 'ROLE_ADMIN',
                'resource' => '/pages/dashboard',
                'action' => 'view',
                'type' => 'web',
                'is_granted' => true
            ],
            [
                'role' => 'ROLE_ADMIN',
                'resource' => '/pages/system/permissions',
                'action' => 'view',
                'type' => 'web',
                'is_granted' => true
            ],
            [
                'role' => 'ROLE_ADMIN',
                'resource' => '/pages/system/users',
                'action' => 'view',
                'type' => 'web',
                'is_granted' => true
            ],
            [
                'role' => 'ROLE_ADMIN',
                'resource' => '/pages/system/user-roles',
                'action' => 'view',
                'type' => 'web',
                'is_granted' => true
            ],
        ];

        foreach ($permissions as $p) {
            $permission = new Permission();
            $permission->setAction($p['action']);
            $permission->setIsGranted($p['is_granted']);
            $permission->setResource($p['resource']);
            $permission->setRole($p['role']);
            $permission->setType($p['type']);
            $permission->setCreatedAt(new \DateTime());
            $manager->persist($permission);
        }

        $manager->flush();
    }
}
