<?php
/**
 * Created by PhpStorm.
 * User: Alexey Itsekson
 * Date: 21.01.2019
 * Time: 19:00
 */

namespace App\Security;

use App\Entity\Permission;
use App\Entity\UserRole;
use App\Utils\ReloadCacheInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Exception\InvalidArgumentException;

class AclPermissionsProvider implements PermissionsProviderInterface, ReloadCacheInterface
{
    private const CACHE_KEY = '__app__::permissions::acl';
    /**
     * @var CacheItemPoolInterface
     */
    private $cache;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ParameterBagInterface
     */
    private $params;

    /**
     * @var Acl
     */
    private $acl;

    private $logger;

    public function __construct(
        CacheItemPoolInterface $cache,
        EntityManagerInterface $em,
        ParameterBagInterface $params,
        LoggerInterface $logger
    )
    {
        $this->cache = $cache;
        $this->em = $em;
        $this->params = $params;
        $this->logger = $logger;
        if (getenv('APP_ENV') === 'test' || PHP_SAPI !== 'cli') {
            $this->initAcl();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isGranted(UserInterface $user, $resource, $action = null): bool
    {
        $isGranted = false;
        if ($this->acl instanceof Acl) {
            foreach ($user->getRoles() as $role) {
                try {
                    $isGranted = $this->acl->isAllowed($role, $resource, $action);
                } catch (InvalidArgumentException $ex) {
                    $this->logger->warning($ex, $ex->getTrace());
                    $isGranted = false;
                }
                if ($isGranted) {
                    break;
                }
            }
        }
        return $isGranted;
    }

    function reloadCache()
    {
        $key = hash('sha256', self::CACHE_KEY);
        $this->cache->deleteItem($key);
        $this->acl = null;
        $this->initAcl();
    }


    private function initAcl(): void
    {
        try {
            $key = hash('sha256', self::CACHE_KEY);
            if ($this->cache->hasItem($key)) {
                $item = $this->cache->getItem($key);
                if ($item) {
                    $this->acl = $item->get();
                    if (($this->acl instanceof Acl)) {
                        return;
                    }
                }
            }

            if (!$this->acl instanceof Acl) {
                $this->acl = new Acl();
            }
            /** @var UserRole[] $roles */
            $roles = $this->em->getRepository(UserRole::class)->findAll();
            /** @var Permission[] $permissions */
            $permissions = $this->em->getRepository(Permission::class)->findBy(['type' => 'api']);
            foreach ($roles as $role) {
                $parentRoles = $role->getParentRoles();
                $parentRolesAsArr = null;
                if ($parentRoles && $parentRoles->count() > 0) {
                    $parentRolesAsArr = array_map(function (UserRole $role) {
                        return $role->getName();
                    }, $parentRoles->toArray());
                }
                try {
                    $this->acl->addRole($role->getName(), $parentRolesAsArr);
                } catch (InvalidArgumentException $ex) {
                    $this->logger->warning($ex->getMessage());
                }
            }

            foreach ($permissions as $permission) {
                try {
                    $this->acl->addResource($permission->getResource());
                } catch (InvalidArgumentException $ex) {
                    $this->logger->warning($ex->getMessage());
                }
                $aRole = $permission->getRole() === '*' ? null : $permission->getRole();
                $aRes = $permission->getResource() === '*' ? null : $permission->getResource();
                $aAction = $permission->getAction() === '*' ? null : $permission->getAction();
                if ($permission->getIsGranted()) {
                    $this->acl->allow($aRole, $aRes, $aAction);
                } else {
                    $this->acl->deny($aRole, $aRes, $aAction);
                }
            }
            $item = $this->cache->getItem($key);
            if ($item) {
                $expiresAt = new \DateTime();
                $expiresAt->setTimestamp(time() + $this->params->get('app_acl_cache_ttl'));
                $item->set($this->acl);
                $item->expiresAt($expiresAt);
                $this->cache->save($item);
            }

        } catch (\LogicException $ex) {
            $this->logger->error($ex->getMessage(), $ex->getTrace());
        }
    }
}