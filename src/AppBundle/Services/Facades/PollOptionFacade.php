<?php
/**
 * Created by PhpStorm.
 * User: alour
 * Date: 09/04/2017
 * Time: 13:13
 */

namespace AppBundle\Services\Facades;


use Doctrine\ORM\EntityManager;

class PollOptionFacade extends AbstractFacade
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, 'AppBundle:PollOption');
    }
}