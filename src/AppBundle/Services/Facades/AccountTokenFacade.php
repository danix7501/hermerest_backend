<?php
/**
 * Created by PhpStorm.
 * User: Daniel Romero
 * Date: 20/06/2018
 * Time: 1:21
 */

namespace AppBundle\Services\Facades;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;

class AccountTokenFacade extends AbstractFacade
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, 'AppBundle:AccountToken');
    }

    public function findByToken($token)
    {
        return $this->entityManager->getRepository($this->entityName)->findOneBy(
            ["token" => $token]
        );
    }

    public function removeAllFromUsuario(User $user)
    {
        return $this->entityManager->getRepository($this->entityName)->createQueryBuilder('accountToken')
            ->delete('AppBundle:AccountToken', 'accountToken')
            ->where('accountToken.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
}