<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(RegistryInterface $registry,
                                UserPasswordEncoderInterface $passwordEncoder,
                                ValidatorInterface $validator)
    {
        parent::__construct($registry, User::class);
        $this->passwordEncoder = $passwordEncoder;
        $this->validator = $validator;
    }

    /**
     * @param User $user
     * @param null $userId
     * @return User|null
     */
    public function save(User $user, $userId = null): User
    {
        try {
            $p = $this->findOneBy(['email' => $user->getEmail()]);
            if ($userId !== null) {
                if ($p && (int)$userId !== $p->getId()) {
                    throw new \InvalidArgumentException('User with given email already exists');
                }
                $_u = $this->find($userId);
                if (!$_u) {
                    throw new \DomainException('User with given ID not found');
                }
                $_u->setEmail($user->getEmail());
                $_u->setRoles($user->getRoles());
                if ($user->getPassword()) {
                    $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
                }
                $user = $_u;
            } else {
                if ($p) {
                    throw new \InvalidArgumentException('User with given parameters already exists');
                }
                $user->setCreatedAt(new \DateTime());
                if ($user->getPassword()) {
                    $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
                }
            }
            /** @var ConstraintViolationListInterface $errors */
            $errors = $this->validator->validate($user);
            if ($errors->count() > 0) {
                throw new ValidatorException((string)$errors);
            }
            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush($user);
        } catch (\Exception $ex) {
            throw new \LogicException($ex->getMessage(), 0, $ex);
        }
        return $user;
    }

    /**
     * @param $id
     */
    public function delete($id)
    {
        try {
            $p = $this->find($id);
            if (!$p) {
                throw new \DomainException('User with given ID not found');
            }
            $this->getEntityManager()->remove($p);
            $this->getEntityManager()->flush($p);
        } catch (\Exception $ex) {
            throw new \LogicException($ex->getMessage(), 0, $ex);
        }
    }

}
