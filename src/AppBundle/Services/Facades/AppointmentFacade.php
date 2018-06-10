<?php
/**
 * Created by PhpStorm.
 * User: danielromerocalero
 * Date: 10/6/18
 * Time: 21:14
 */

namespace AppBundle\Services\Facades;


use Doctrine\ORM\EntityManager;

class AppointmentFacade extends AbstractFacade
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, 'AppBundle:Appointment');
    }

    public function findBySchedule($schedule)
    {
        return $this->entityManager->getRepository($this->entityName)->findOneBy(
            array(
                "schedule" => $schedule,
            )
        );
    }

}