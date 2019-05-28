<?php

namespace App\Repository;

use App\Entity\Permission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\LogicException;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @method Permission|null find($id, $lockMode = null, $lockVersion = null)
 * @method Permission|null findOneBy(array $criteria, array $orderBy = null)
 * @method Permission[]    findAll()
 * @method Permission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PermissionRepository extends ServiceEntityRepository
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(RegistryInterface $registry,  ValidatorInterface $validator)
    {
        parent::__construct($registry, Permission::class);
        $this->validator = $validator;
    }

    /**
     * @param $resource
     * @param $roles
     * @param $action
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByResourceAndAction($resource, $roles, $action)
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.resource = :resource')
            ->andWhere('p.role in (:roles)')
            ->andWhere('p.action = :action')
            ->setMaxResults(1)
            ->setParameters([
                'resource' => $resource,
                'roles' => $roles,
                'action' => $action
            ]);

        $res = $qb->getQuery()->getOneOrNullResult();
        return $res;
    }

    /**
     * @param Permission $permission
     * @param null $permissionId
     * @return Permission|null
     */
    public function save(Permission $permission, $permissionId = null): Permission
    {
        try {
            /** @var ConstraintViolationListInterface $errors */
            $errors = $this->validator->validate($permission);
            if ($errors->count() > 0) {
                throw new ValidatorException((string)$errors);
            }

            $p = $this->findOneBy(['role' => $permission->getRole(), 'resource' => $permission->getResource(), 'action' => $permission->getAction()]);
            if ($permissionId !== null) {
                if ($p && (int)$permissionId !== $p->getId()) {
                    throw new \InvalidArgumentException('Permission with given parameters already exists');
                }
                $_p = $this->find($permissionId);
                if (!$_p) {
                    throw new \DomainException('Permission with given ID not found');
                }
                $_p->setAction($permission->getAction());
                $_p->setIsGranted($permission->getIsGranted());
                $_p->setResource($permission->getResource());
                $_p->setRole($permission->getRole());
                $permission = $_p;
            } else {
                if ($p) {
                    throw new \InvalidArgumentException('Permission with given parameters already exists');
                }
                $permission->setCreatedAt(new \DateTime());
            }
            $this->getEntityManager()->persist($permission);
            $this->getEntityManager()->flush($permission);
        } catch (\Exception $ex) {
            throw new LogicException($ex->getMessage(), 0, $ex);
        }
        return $permission;
    }

    /**
     * @param $id
     */
    public function delete($id)
    {
        try {
            $p = $this->find($id);
            if (!$p) {
                throw new \DomainException('Permission with given ID not found');
            }
            $this->getEntityManager()->remove($p);
            $this->getEntityManager()->flush($p);
        } catch (\Exception $ex) {
            throw new LogicException($ex->getMessage(), 0, $ex);
        }
    }


}
