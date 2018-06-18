<?php
/**
 * Created by PhpStorm.
 * User: Daniel Romero
 * Date: 18/06/2018
 * Time: 0:32
 */

namespace AppBundle\Services\Facades;

use Doctrine\ORM\EntityManager;

class DeviceFacade extends AbstractFacade
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, 'AppBundle:Device');
    }
}