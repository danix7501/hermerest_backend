<?php
/**
 * Created by PhpStorm.
 * User: danielromerocalero
 * Date: 10/6/18
 * Time: 18:46
 */

namespace AppBundle\Services\Facades;


use Doctrine\ORM\EntityManager;

class ScheduleFacade extends AbstractFacade
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, 'AppBundle:Schedule');
    }

    public function checkSchedule($teacher, $schedule, $timeFrom, $timeTo) {
        return $this->entityManager->getRepository($this->entityName)->findOneBy([
                "teacher" => $teacher,
                "schedule" => $schedule,
                "timeFrom" => $timeFrom,
                "timeTo" => $timeTo
            ]
        );
    }
}