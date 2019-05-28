<?php

namespace App\Repository;

use App\Entity\UserRole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @method UserRole|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserRole|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserRole[]    findAll()
 * @method UserRole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRoleRepository extends ServiceEntityRepository
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(RegistryInterface $registry, ValidatorInterface $validator)
    {
        parent::__construct($registry, UserRole::class);
        $this->validator = $validator;
    }

    /**
     * @param UserRole $role
     * @param null $roleId
     * @return UserRole|null
     */
    public function save(UserRole $role, $roleId = null): UserRole
    {
        try {
            /** @var ConstraintViolationListInterface $errors */
            $errors = $this->validator->validate($role);
            if ($errors->count() > 0) {
                throw new ValidatorException((string)$errors);
            }

            $p = $this->findOneBy([
                'name' => $role->getName(),
            ]);
            $parent = $role->getParentRoles();
            if ($roleId !== null) {
                if ($p && (int)$roleId !== $p->getId()) {
                    throw new \InvalidArgumentException('Role with given name already exists');
                }
                $_p = $this->find($roleId);
                if (!$_p) {
                    throw new \DomainException('Role with given ID not found');
                }
                $_p->setName($role->getName());
                foreach ($_p->getParentRoles() as $parentRole) {
                    $_p->removeParentRole($parentRole);
                }
                $role = $_p;
            } else {
                if ($p) {
                    throw new \InvalidArgumentException('Role with given name already exists');
                }
                $role->setCreatedAt(new \DateTime());
            }
            $role->clearParentRoles();
            foreach ($parent as $item) {
                $prole = $this->findOneBy(['name' => $item->getName()]);
                if($prole !== null) {
                    $role->addParentRole($prole);
                }
            }

            $this->getEntityManager()->persist($role);
            $this->getEntityManager()->flush($role);
        } catch (\Exception $ex) {
            throw new \LogicException($ex->getMessage(), 0, $ex);
        }
        return $role;
    }

    /**
     * @param $id
     */
    public function delete($id)
    {
        try {
            $p = $this->find($id);
            if (!$p) {
                throw new \DomainException('Role with given ID not found');
            }
            $this->getEntityManager()->remove($p);
            $this->getEntityManager()->flush($p);
        } catch (\Exception $ex) {
            throw new \LogicException($ex->getMessage(), 0, $ex);
        }
    }

}
