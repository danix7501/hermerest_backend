<?php
/**
 * Created by PhpStorm.
 * User: alour
 * Date: 09/04/2017
 * Time: 13:13
 */

namespace AppBundle\Services\Facades;


use Doctrine\ORM\EntityManager;

class AuthorizationReplyFacade extends AbstractFacade
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, 'AppBundle:AuthorizationReply');
    }
    public function findByAuthorization($authorization)
    {
        return $this->entityManager->getRepository($this->entityName)->findOneBy(
            array('authorization' => $authorization)
        );
    }
}