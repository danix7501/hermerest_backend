<?php
/**
 * Created by PhpStorm.
 * User: alour
 * Date: 09/04/2017
 * Time: 13:13
 */

namespace AppBundle\Services\Facades;


use Doctrine\ORM\EntityManager;

class CourseFacade extends AbstractFacade
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, 'AppBundle:Course');
    }

    public function findByNameOfCourse($nameCourse)
    {
        return $this->entityManager->getRepository($this->entityName)->findOneBy(
            array("name" => $nameCourse)
        );
    }
}