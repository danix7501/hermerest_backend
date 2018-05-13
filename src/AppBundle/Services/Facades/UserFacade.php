<?php
/**
 * Created by PhpStorm.
 * User: Daniel Romero
 * Date: 09/05/2018
 * Time: 21:57
 */

namespace AppBundle\Services\Facades;

use Doctrine\ORM\EntityManager;

class UserFacade extends AbstractFacade
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, 'AppBundle:User');
    }

    public function findByEmailAndPlainPassword($username, $plainPassword)
    {
        return $this->entityManager->getRepository($this->entityName)->findOneBy(
            array(
                "username" => $username,
                "password" => hash('sha256', $plainPassword)
            )
        );
    }

}