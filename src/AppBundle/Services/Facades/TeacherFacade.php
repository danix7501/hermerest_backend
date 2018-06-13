<?php
/**
 * Created by PhpStorm.
 * User: Daniel Romero
 * Date: 09/06/2018
 * Time: 18:11
 */

namespace AppBundle\Services\Facades;


use Doctrine\ORM\EntityManager;

class TeacherFacade extends AbstractFacade
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, 'AppBundle:Teacher');
    }

    public function findByNameOfTeacher($nameTeacher)
    {
        return $this->entityManager->getRepository($this->entityName)->findOneBy(
            array("name" => $nameTeacher)
        );
    }
}
