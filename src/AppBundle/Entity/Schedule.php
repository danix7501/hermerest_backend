<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
/**
 * Schedule
 * @ORM\Entity
 * @ORM\Table(name="schedule")
 */
class Schedule
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @OneToOne(targetEntity="Teacher")
     * @JoinColumn(name="id_teacher", referencedColumnName="id")
     */
    private $teacher;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="schedule", type="date")
     */
    private $schedule;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_from", type="datetime")
     */
    private $timeFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_to", type="datetime")
     */
    private $timeTo;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set teacher
     *
     * @param integer $teacher
     *
     * @return Schedule
     */
    public function setTeacher($teacher)
    {
        $this->teacher = $teacher;

        return $this;
    }

    /**
     * Get teacher
     *
     * @return int
     */
    public function getTeacher()
    {
        return $this->teacher;
    }

    /**
     * Set schedule
     *
     * @param \DateTime $schedule
     *
     * @return Schedule
     */
    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;

        return $this;
    }

    /**
     * Get schedule
     *
     * @return \DateTime
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * Set timeFrom
     *
     * @param \DateTime $timeFrom
     *
     * @return Schedule
     */
    public function setTimeFrom($timeFrom)
    {
        $this->timeFrom = $timeFrom;

        return $this;
    }

    /**
     * Get timeFrom
     *
     * @return \DateTime
     */
    public function getTimeFrom()
    {
        return $this->timeFrom;
    }

    /**
     * Set timeTo
     *
     * @param \DateTime $timeTo
     *
     * @return Schedule
     */
    public function setTimeTo($timeTo)
    {
        $this->timeTo = $timeTo;

        return $this;
    }

    /**
     * Get timeTo
     *
     * @return \DateTime
     */
    public function getTimeTo()
    {
        return $this->timeTo;
    }
}

